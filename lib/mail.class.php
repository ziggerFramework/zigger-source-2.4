<?php
namespace Make\Library;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail extends \Make\Database\Pdosql {

    public $mailer;
    public $tpl;
    public $to;
    public $from;
    public $chk_url;
    public $mb_id;
    public $mb_pwd ;
    public $subject;
    public $memo;
    public $st_tit;
    public $smtp_id;
    public $smtp_pwd;
    public $smtp_server;
    public $smtp_port;
    public $attach;
    protected $maile;
    protected $mailFromArray;
    protected $smtp_charset;
    protected $smtp_secure_verify_peer;
    protected $smtp_secure_verify_peer_name;

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
        $this->smtp_id = '';
        $this->smtp_pwd = '';
        $this->smtp_server = '';
        $this->smtp_port = '';
        $this->attach = array();
        $this->mailFromArray = array();
        $this->smtp_charset = 'UTF-8';
        $this->smtp_secure_verify_peer = false;
        $this->smtp_secure_verify_peer_name = false;
    }

    // mail set
    public function set($arr)
    {
        $this->init();

        foreach($arr as $key => $value) {
            $this->{$key} = $value;
        }

        $this->mailer = new PHPMailer(true);
        $this->mailer->isHTML(true);
    }

    // headers
    protected function setCommonHeader()
    {
        $this->mailer->addCustomHeader('User-Agent', 'Zigger Sendmail System');
        $this->mailer->addCustomHeader('X-Accept-Language', 'ko, en');
        $this->mailer->addCustomHeader('X-Sender', $this->mailFromArray['email']);
        $this->mailer->addCustomHeader('X-Mailer', 'PHP');
        $this->mailer->addCustomHeader('Reply-to', $this->mailFromArray['email']);
        $this->mailer->addCustomHeader('Return-Path', $this->mailFromArray['email']);
    }

    // body
    protected function setSubject()
    {
        $this->mailer->Subject = $this->subject;
    }

    protected function setHtmlBody($to)
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
                where `type`=:col1
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
        $html = str_replace('{{email}}', (isset($to['email'])) ? $to['email'] : '', $html);
        $html = str_replace('{{name}}', (isset($to['name'])) ? $to['name'] : '', $html);
        $html = str_replace('{{article}}', ($this->memo) ? $this->memo : '', $html);
        $html = str_replace('{{site_title}}', ($this->st_tit) ? $this->st_tit : '', $html);

        $this->mailer->Body = $html;
    }

    // attach
    public function setAttach()
    {
        foreach ($this->attach as $attach) {
            if (!file_exists($attach['path'])) continue;
            $this->mailer->addAttachment($attach['path'], $attach['name']);
        }
    }
    
    // mail from
    public function setFrom()
    {
        global $CONF;

        $from_name = (isset($this->from['name']) && !empty($this->from['name'])) ? $this->from['name'] : $CONF['title'];
        $from_email = (isset($this->from['email']) && !empty($this->from['email'])) ? $this->from['email'] : $CONF['email'];
        
        $this->mailer->setFrom($from_email, $from_name);

        $this->mailFromArray['name'] = (!isset($this->from['name'])) ? $CONF['title'] : $this->from['name'];
        $this->mailFromArray['email'] = (!isset($this->from['email'])) ? $CONF['email'] : $this->from['email'];
    }

    // mail to
    public function setTo($to)
    {
        $this->mailer->clearAddresses();

        $to_email = (isset($to['email']) && !empty($to['email'])) ? $to['email'] : '';
        $to_name = (isset($to['name']) && !empty($to['name'])) ? $to['name'] : '';

        $this->mailer->addAddress($to_email, $to_name);
    }

    // use smtp
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
        
        $this->mailer->isSMTP();
        $this->mailer->Host = ($this->useSmtpServer() === true) ? $CONF['smtp_server'] : 'localhost';
        $this->mailer->SMTPAuth = $this->useSmtpServer();
        $this->mailer->Username = $CONF['smtp_id'];
        $this->mailer->Password = $CONF['smtp_pwd'];
        $this->mailer->CharSet = $this->smtp_charset;
        if ($this->useSmtpServer() === true) $this->mailer->SMTPSecure = $CONF['use_smtp_secure']; // Secure Mode (ssl / tls)
        if ($this->useSmtpServer() === true) $this->mailer->Port = $CONF['smtp_port']; // SMTP Port

        // Secure Options
        $this->mailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => $this->smtp_secure_verify_peer,
                'verify_peer_name' => $this->smtp_secure_verify_peer_name
            )
        );
    }

    // send
    public function send()
    {
        $this->getSmtpServerInfo();
        $this->setFrom();
        $this->setCommonHeader();
        $this->setSubject();

        $successCount = 0;

        foreach ($this->to as $to) {

            $this->setTo($to);
            $this->setAttach();
            $this->setHtmlBody($to);
    
            $smtp_max_attempts = 5;
            $smtp_now_attempts = 0;
    
            do {
                $smtp_sent = $this->mailer->send();
                $smtp_now_attempts++;
                
                if (!$smtp_sent) {
                    if ($smtp_now_attempts >= $smtp_max_attempts) {
                        break;  // 최대 시도 횟수 초과 시 연결 포기
                    }
                    sleep(1);  // 재시도 전 대기 시간 (초)
                }
            } while (!$smtp_sent);
    
            if (!$smtp_sent) exit(ERR_MSG_7);

            $successCount++;

        }

        return $successCount;
    }

}