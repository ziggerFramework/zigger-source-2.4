<?php
namespace Make\Library;

use Corelib\Func;
use Make\Database\Pdosql;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class Uploader {

    private $file_upload;
    public $path;
    public $file;
    public $intdict = SET_INTDICT_FILE;
    public $file_idx = 0;

    // 파일 유무 검사
    public function isfile($file)
    {
        return (@is_file($file)) ? true : false;
    }

    // 디렉토리 유무 검사
    public function isdir($dir)
    {
        return (@is_dir($dir)) ? true : false;
    }

    // 파일 검사
    public function chkfile($type)
    {
        $intd = explode(',', preg_replace('/\s+/', '', $this->intdict));
        $f_type = Func::get_filetype($this->file['name']);
        $chk = true;

        $chk = !in_array($f_type, $intd);

        if ($type == 'notmatch') {
            return ($chk === false) ? false : true;

        } else if ($type == 'match') {
            return ($chk === false) ? true : false;
        }

    }

    // 첨부 파일명 변환
    public function replace_filename($file)
    {
        global $CONF;

        $lastChar = 'N';

        if (isset($CONF['use_s3']) && $CONF['use_s3'] == 'Y') $lastChar = 'Y';

        $tstamp = md5(rand(0,999999999).date('ymdhis', time()));
        $tstamp .= md5($file);
        $file_name = $tstamp.$this->file_idx.$lastChar.'.'.Func::get_filetype($file);

        $this->file_idx++;

        return $file_name;
    }

    // 파일 byte 검사
    public function chkbyte($limit)
    {
        return ($this->file['size'] > $limit) ? false : true;
    }

    // 저장 위치 검사 및 생성
    public function chkpath()
    {
        $paths = str_replace(PH_PATH.'/', '', $this->path);
        $paths = explode('/', $paths);

        $path_sum = '';
        foreach ($paths as $key => $value) {

            if ($key > 0) {
                $path_sum .= '/'.$value;
            } else {
                $path_sum = PH_PATH.'/'.$value;
            }

            if (!is_dir($path_sum)) {
                @mkdir($path_sum, 0707);
                @chmod($path_sum, 0707);
            }
        }
    }

    // DB 기록
    private function record_dataupload($replace_filename, $filename = '')
    {
        global $CONF;

        $storage = (isset($CONF['use_s3']) && $CONF['use_s3'] == 'Y') ? 'Y' : 'N';
        $path = str_replace(PH_DATA_PATH, '', $this->path);

        if (!$filename) {
            $filename = $this->file['name'];
        }

        $sql = new Pdosql();
        $sql->query(
            "
            insert into {$sql->table("dataupload")}
            (filepath, orgfile, repfile, storage, byte, regdate)
            values
            (:col1, :col2, :col3, :col4, :col5, now())
            ",
            array(
                $path, $filename, $replace_filename, $storage, $this->file['size']
            )
        );
    }

    private function record_datacopy($org_file, $replace_filename)
    {
        $sql = new Pdosql();

        $org_file_name = basename($org_file);
        $replace_filename_name = basename($replace_filename);

        $fileinfo = Func::get_fileinfo($org_file_name);

        $sql->query(
            "
            select *
            from {$sql->table("dataupload")}
            where orgfile=:col1 and repfile=:col2
            ",
            array(
                $fileinfo['orgfile'], $replace_filename
            )
        );

        if ($sql->getcount() > 0) return;

        $replace_path = str_replace(PH_DATA_PATH, '', $replace_filename);
        $replace_path = str_replace('/'.$replace_filename_name, '', $replace_path);

        $sql->query(
            "
            insert into {$sql->table("dataupload")}
            (filepath, orgfile, repfile, storage, byte, regdate)
            values
            (:col1, :col2, :col3, :col4, :col5, now())
            ",
            array(
                $replace_path, $fileinfo['orgfile'], $replace_filename_name, $fileinfo['storage'], $fileinfo['byte']
            )
        );
    }

    private function record_datadrop($replace_filename)
    {
        $sql = new Pdosql();
        $sql->query(
            "
            delete
            from {$sql->table("dataupload")}
            where repfile=:col1
            ",
            array(
                $replace_filename
            )
        );
    }

    // S3
    private function get_s3_action($type, $filename, $copy_filename = '', $tmp = true)
    {
        global $CONF;

        $s3 = S3Client::factory(
            array(
                'endpoint' => $CONF['s3_key1'],
                'version' => 'latest',
                'region' => $CONF['s3_key5'],
                'credentials' => array(
                    'key' => $CONF['s3_key3'],
                    'secret'  => $CONF['s3_key4'],
                ),
                'use_path_style_endpoint' => ($CONF['s3_path_style'] == 'Y') ? true : false
            )
        );

        // S3 upload
        if ($type == 'upload') {

            if ($tmp === false) {
                $savefile = $this->file;

            } else {
                $savefile = $this->file['tmp_name'];
            }

            $awsSource = fopen($savefile, 'rb');

            try {
                $s3->putObject([
                    'Bucket' => $CONF['s3_key2'],
                    'Key' => str_replace(PH_DATA_PATH.'/', '', $this->path).'/'.$filename,
                    'Body' => $awsSource,
                    'ACL' => 'public-read'
                ]);

            } catch (S3Exception $e) {
                if ($e->getMessage()) {
                    Func::err_print(ERR_MSG_13);
                    return false;
                }
            }
        }

        // S3 delete
        if ($type == 'delete') {
            try {
                $s3->deleteObject([
                    'Bucket' => $CONF['s3_key2'],
                    'Key' => str_replace(PH_DATA_PATH.'/', '', $this->path).'/'.$filename
                ]);

            } catch (S3Exception $e) {
                if ($e->getMessage()) {
                    Func::err_print(ERR_MSG_13);
                    return false;
                }
            }
        }

        // S3 copy
        if ($type == 'copy') {
            try {
                $s3->copyObject([
                    'Bucket' => $CONF['s3_key2'],
                    'CopySource' => $CONF['s3_key2'].'/'.str_replace(PH_DATA_PATH.'/', '', $filename),
                    'Key' => str_replace(PH_DATA_PATH.'/', '', $copy_filename),
                    'ACL' => 'public-read'
                ]);

            } catch (S3Exception $e) {
                if ($e->getMessage()) {
                    Func::err_print(ERR_MSG_13);
                    return false;
                }
            }
        }

    }

    // copy
    public function filecopy($old_file, $new_file)
    {
        $old_filename = basename($old_file);
        $new_filename = basename($new_file);
        $fileinfo = Func::get_fileinfo($old_filename);

        $this->path = str_replace('/'.basename($old_file), '', $old_file);

        // s3
        if ($fileinfo['storage'] == 'Y') {
            $this->get_s3_action('copy', $old_file, $new_file);
        }
        // local
        else if ($this->isfile($old_file)) {
            copy($old_file, $new_file);

        }

        $this->record_datacopy($old_file, $new_file);
    }

    // save
    public function upload($file, $tmp = true)
    {
        global $CONF;

        $chked = true;

        //s3
        if (isset($CONF['use_s3']) && $CONF['use_s3'] == 'Y') {
            $this->get_s3_action('upload', $file, '', $tmp);
        }

        //local
        else {
            $savefile =  ($tmp === false) ? $this->file : $this->file['tmp_name'];
            if (!$this->file_upload = move_uploaded_file($savefile, $this->path.'/'.$file)) $chked = false;
        }

        if ($chked === true) $this->record_dataupload($file);

        return $chked;
    }

    // delete
    public function drop($file)
    {
        global $CONF;

        $fileinfo = Func::get_fileinfo($file);

        if (!$fileinfo) return false;

        // s3
        if ($fileinfo['storage'] == 'Y') {
            $this->get_s3_action('delete', $file);
        }

        // local
        else if ($this->isfile($this->path.'/'.$file)) {
            unlink($this->path.'/'.$file);
        }

        $this->record_datadrop($file);

    }

    // delete directory
    public function dropdir()
    {
        if ($this->isdir($this->path)) {
            $dir = dir($this->path);
            while (($entry=$dir->read()) !== false) {
                if ($entry != '.' && $entry != '..') unlink($this->path.'/'.$entry);
            }
            $dir->close();
            @rmdir($this->path);
        }
    }

    // ckeditor plugin 사진 삭제
    public function edt_drop($article)
    {
        $this->path = PH_PLUGIN_PATH.'/'.PH_PLUGIN_CKEDITOR;

        preg_match_all("/ckeditor\/[a-zA-Z0-9-_\.]+.(jpg|gif|png|bmp)/i", $article,$sEditor_images_ex);

        for ($i = 0; $i < count($sEditor_images_ex[0]); $i++) {
            $this->name = str_replace(PH_PLUGIN_CKEDITOR.'/', '', $this->sEditor_images_ex[0][$i]);
            if ($this->isfile($this->name)) $this->filedrop($this->name);
        }
    }

}
