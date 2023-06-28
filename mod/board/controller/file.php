<?php
namespace Module\Board;

use Corelib\Method;
use Corelib\Func;
use Make\Database\Pdosql;
use Make\Library\Uploader;

//
// Module Controller
// ( Down )
//
class Down extends \Controller\Make_Controller {

    public function init()
    {
        global $board_id;

        $sql = new Pdosql();

        $req = Method::request('get', 'board_id, idx, file');

        $board_id = $req['board_id'];

        if (!$board_id || !$req['idx'] || !$req['file']) Func::err('필수 값이 누락 되었습니다.');

        // 게시글의 첨부파일 정보 불러옴
        $sql->query(
            "
            select *
            from {$sql->table("mod:board_data_".addslashes($board_id))}
            where idx=:col1
            ",
            array(
                $req['idx']
            )
        );

        $target_file = $sql->fetch('file'.$req['file']);

        // 첨부파일이 확인되지 않는 경우
        if (!$target_file) Func::err('첨부파일이 확인되지 않습니다.');

        // 파일 정보
        $fileinfo = Func::get_fileinfo($target_file);

        // Object Storage에 저장된 파일인 경우
        if ($fileinfo['storage'] == 'Y') {

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $fileinfo['replink']);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec ($ch);

            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_code == 200) {

                header('Content-Disposition: attachment; filename='.urlencode($fileinfo['orgfile']));
                header('Content-type: application/octet-stream');
                header('Content-Transfer-Encoding: binary');

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_URL, $fileinfo['replink']);

                $file = curl_exec($ch);
                curl_close($ch);

            }

        }

        // Local에 저장된 파일인 경우
        else if ($fileinfo['storage'] == 'N') {

            $fileinfo = array();
            $fileinfo['path'] = MOD_BOARD_DATA_PATH.'/'.$board_id.'/'.$target_file;
            $fileinfo['size'] = filesize($fileinfo['path']);
            $fileinfo['parts'] = pathinfo($fileinfo['path']);
            $fileinfo['name'] = $fileinfo['parts']['basename'];

            // 파일 다운로드 스트림
            $file_datainfo = Func::get_fileinfo($fileinfo['name']);

            header('Content-Type:application/octet-stream');
            header('Content-Disposition:attachment; filename='.$file_datainfo['orgfile']);
            header('Content-Transfer-Encoding:binary');
            header('Content-Length:'.(string)$fileinfo['size']);
            header('Cache-Control:Cache,must-revalidate');
            header('Pragma:No-Cache');
            header('Expires:0');
            ob_clean();
            flush();

            readfile($fileinfo['path']);

        }

        // 파일 다운로드 횟수 증가
        $sql->query(
            "
            update {$sql->table("mod:board_data_".$board_id)}
            set file{$req['file']}_cnt = file{$req['file']}_cnt + 1
            where idx={$req['idx']}
            ", []
        );

    }

}
