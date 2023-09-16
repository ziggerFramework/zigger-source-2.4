<?php
namespace Make\Library;

class Mail extends \Make\Database\Pdosql {

    public $tpl = 'default';
    public $to = array();
    public $from = array();
    public $chk_url;
    public $mb_id ;
    public $mb_pwd ;
    public $subject;
    public $memo;
    public $st_tit;
    public $smtp_sock;
    public $smtp_id;
    public $smtp_pwd;
    public $smtp_server;
    public $smtp_port;
    public $attach = array();
    protected $mailBoundary;
    protected $mailFromArray = array();
    protected $mailToArray = array();
    protected $mailHeaderArray = array();
    protected $mailAttachArray = array();

    // mail init
    private function init()
    {
        $this->tpl = 'default';
        $this->to = array();
        $this->from = array();
        $this->chk_url = '';
        $this->mb_id  = '';
        $this->mb_pwd  = '';
        $this->subject = '';
        $this->memo = '';
        $this->st_tit = '';
        $this->smtp_sock = '';
        $this->smtp_id = '';
        $this->smtp_pwd = '';
        $this->smtp_server = '';
        $this->smtp_port = '';
        $this->attach = array();
        $this->mailBoundary = '';
        $this->mailFromArray = array();
        $this->mailToArray = array();
        $this->mailHeaderArray = array();
        $this->mailAttachArray = array();
    }

    // mail set
    public function set($arr)
    {
        $this->init();

        foreach($arr as $key => $value) {
            $this->{$key} = $value;
        }

        $this->mailBoundary = md5(uniqid(microtime()));
    }

    // headers
    protected function getBoundary()
    {
        return $this->mailBoundary;
    }

    protected function setCommonHeader()
    {
        $this->addHeader('From', $this->getFrom());
        $this->addHeader('User-Agent', 'Zigger Sendmail System');
        $this->addHeader('X-Accept-Language', 'ko, en');
        $this->addHeader('X-Sender', $this->mailFromArray['email']);
        $this->addHeader('X-Mailer', 'PHP');
        $this->addHeader('X-Priority', 1);
        $this->addHeader('Reply-to', $this->mailFromArray['email']);
        $this->addHeader('Return-Path', $this->mailFromArray['email']);

        if (count($this->mailAttachArray) > 0) {
            $this->addHeader('MIME-Version', '1.0');
            $this->addHeader('Content-Type', 'multipart/mixed; boundary = "'.$this->getBoundary().'"');

        } else {
            $this->addHeader('Content-Type', 'text/html; charset=UTF-8');
            $this->addHeader('Content-Transfer-Encoding', '8bit');
        }
    }

    protected function addHeader($content, $value)
    {
        $this->mailHeaderArray[$content] = $value;
    }

    protected function makeHeaders()
    {
        $header = '';
        foreach ($this->mailHeaderArray as $key => $value) {
            $header .= $key.": ".$value."\n";
        }
        return $header."\r\n";
    }

    // body
    protected function setSubject()
    {
        return ($this->subject) ? $return = $this->base64Contents($this->subject) : '';
    }

    protected function base64Contents($contets)
    {
        return "=?UTF-8?B?".base64_encode($contets)."?=";
    }

    protected function encodingContents($contets)
    {
        return chunk_split(base64_encode($contets));
    }

    protected function makeHtmlBody($email)
    {
        global $CONF;

        if (!$this->st_tit) $this->st_tit = $CONF['title'];

        $html = '';
        $body = '';

        if ($this->tpl) {
            $this->query(
                "
                select *
                from {$this->table("mailtpl")}
                where type=:col1
                ",
                array(
                    $this->tpl
                )
            );
            $this->specialchars = 0;
            $this->nl2br = 0;
            $arr = $this->fetchs();

            $html = $arr['html'];
            
        } else {
            $html = $this->memo;
        }

        $html = str_replace('{{check_url}}', ($this->chk_url) ? $this->chk_url : '', $html);
        $html = str_replace('{{id}}', ($this->mb_id) ? $this->mb_id : '', $html);
        $html = str_replace('{{password}}', ($this->mb_pwd) ? $this->mb_pwd : '', $html);
        $html = str_replace('{{name}}', ($this->mailToArray[$email]) ? $this->mailToArray[$email] : '', $html);
        $html = str_replace('{{article}}', ($this->memo) ? $this->memo : '', $html);
        $html = str_replace('{{site_title}}', ($this->st_tit) ? $this->st_tit : '', $html);

        if (count($this->mailAttachArray) > 0) {
            $body .= "--".$this->getBoundary()."\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $body .= $this->encodingContents($html)."\r\n\r\n";
            $body .= "\r\n".$this->makeAttach();

        } else {
            $body = $html;
        }

        return $body;
    }

    // attach
    public function setAttach()
    {
        foreach ($this->attach as $attach) {
            $fp = fopen($attach['path'], 'r');

            if ($fp) {
                $fBody = fread($fp, filesize($attach['path']));
                @fclose($fp);

                $this->mailAttachArray[$attach['name']] = $fBody;
            }
        }
    }

    protected function makeAttach()
    {
        $arrRet = array();

        if (count($this->mailAttachArray) > 0) {
            foreach ($this->mailAttachArray as $name => $fBody) {
                $tmpAttach = "--".$this->getBoundary()."\r\n";
                $tmpAttach .= "Content-Type: application/octet-stream\r\n";
                $tmpAttach .= "Content-Transfer-Encoding: base64\r\n";
                $tmpAttach .= "Content-Disposition: attachment; filename=\"".$name."\"\r\n\r\n";
                $tmpAttach .= $this->encodingContents($fBody)."\r\n\r\n";
                $arrRet[] = $tmpAttach;
            }
        }

        return implode('', $arrRet);
    }

    // mail from
    public function setFrom()
    {
        global $CONF;

        if (!isset($this->from['name'])) {
            $this->mailFromArray['name'] = $CONF['title'];

        } else {
            $this->mailFromArray['name'] = $this->from['name'];
        }

        if (!isset($this->from['email'])) {
            $this->mailFromArray['email'] = $CONF['email'];

        } else {
            $this->mailFromArray['email'] = $this->from['email'];
        }
    }

    protected function getFrom()
    {
        return $this->base64Contents($this->mailFromArray['name']).' <'.$this->mailFromArray['email'].'>';
    }

    // mail to
    public function setTo()
    {
        foreach ($this->to as $to) {
            if (!isset($to['name'])) $to['name'] = '';
            $this->mailToArray[$to['email']] = $to['name'];
        }
    }

    // use SMTP Server
    protected function useSmtpServer()
    {
        global $CONF;

        return ($CONF['use_smtp'] == 'Y') ? true : false;
    }

    protected function getSmtpServerInfo()
    {
        global $CONF;

        $this->smtp_id = base64_encode($CONF['smtp_id']);
        $this->smtp_pwd = base64_encode($CONF['smtp_pwd']);
        $this->smtp_server = $CONF['smtp_server'];
        $this->smtp_port = $CONF['smtp_port'];
    }

    protected function putSocket($val)
    {
        @fputs($this->smtp_sock, $val."\r\n");
        return @fgets($this->smtp_sock, 512);
    }

    // send
    public function send()
    {
        $this->setFrom();
        $this->setTo();
        $this->setAttach();
        $this->setCommonHeader();

        // SMTP 발송
        if ($this->useSmtpServer() !== false) {
            $this->send_smtp();

        // Local 발송
        } else {
            $this->send_local();
        }
    }

    // SMTP 발송
    protected function send_smtp()
    {
        $successCount = 0;
        $this->getSmtpServerInfo();
        $this->addHeader('Subject', $this->setSubject());

        // socket 연결
        if (strstr($this->smtp_server, 'ssl://') || strstr($this->smtp_server, 'tls://')) {
            $context = stream_context_create();
            $result = stream_context_set_option($context, 'ssl', 'verify_peer', false);
            $result = stream_context_set_option($context, 'ssl', 'verify_host', false);
            $this->smtp_sock = stream_socket_client($this->smtp_server.':'.$this->smtp_port, $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $context) or die (ERR_MSG_7);

        } else {
            $this->smtp_sock = fsockopen($this->smtp_server, $this->smtp_port) or die (ERR_MSG_7);
        }

        if ($this->smtp_sock) {
            $this->putSocket('HELO '.$this->smtp_server);

            if ($this->smtp_id) {
                $this->putSocket('AUTH LOGIN');
                $this->putSocket($this->smtp_id);
                $this->putSocket($this->smtp_pwd);
            }
        }

        // socket 발송
        foreach ($this->mailToArray as $email => $name) {

            $html = $this->makeHtmlBody($email);

            $to = ($name) ? $this->base64Contents($name).' <'.$email.'>' : $email;

            $this->addHeader('To', $to);

            $contents = $this->makeHeaders()."\r\n".$html;

            $this->putSocket('MAIL From:'.$this->mailFromArray['email']);
            $this->putSocket('RCPT To:'.$email);
            $this->putSocket('DATA');
            $this->putSocket($contents);
            $this->putSocket(".\r\n");
        }

        $this->putSocket('QUIT');
        
        return $successCount;
    }

    // Local 발송
    protected function send_local()
    {
        $successCount = 0;

        foreach ($this->mailToArray as $email => $name) {

            $html = $this->makeHtmlBody($email);

            $to = ($name) ? $this->base64Contents($name).' <'.$email.'>' : $email;

            $header = $this->makeHeaders();

            if (mail($to, $this->setSubject(), $html, $header)) $successCount++;
        }

        return $successCount;
    }

}
