<?php
namespace Make\Library;

use Corelib\Func;

class Sms {

    public $timestamp;
    public $from;
    public $to = array();
    public $sendType;
    public $subject;
    public $countryCode = "82";
    public $memo;
    public $reserveTime;
    public $reserveTimeZone = 'Asia/Seoul';
    public $scheduleCode;
    public $attach = array();
    protected $smsHeaderArray = array();

    public function set($arr)
    {
        foreach ($arr as $key => $value) {
            $this->{$key} = $value;
        }
    }

    // apiurl
    protected function getApiUrl()
    {
        global $CONF;
        return $CONF['sms_key1'];
    }

    // accesskey
    protected function getAccesskey()
    {
        global $CONF;
        return $CONF['sms_key3'];
    }

    // secretkey
    protected function getSecretkey()
    {
        global $CONF;
        return $CONF['sms_key4'];
    }

    // api id
    protected function getServiceId()
    {
        global $CONF;
        return $CONF['sms_key2'];
    }

    // sigstamp
    protected function getSigstamp()
    {
        $sigstamp = "POST";
        $sigstamp .= " ";
        $sigstamp .= '/sms/v2/services/'.$this->getServiceId().'/messages';
        $sigstamp .= "\n";
        $sigstamp .= $this->timestamp;
        $sigstamp .= "\n";
        $sigstamp .= $this->getAccesskey();
        $signature = base64_encode(hash_hmac('sha256', $sigstamp, $this->getSecretkey(), true));

        return $signature;
    }

    // from
    protected function getFrom()
    {
        global $CONF;

        if (!$this->from) $this->from = $CONF['sms_from'];

        return $this->from;
    }

    // article length
    protected function getByteLength($str) {
        $strlen = mb_strwidth(str_replace("\r\n", "\n", $str), 'UTF-8');
        return $strlen;
    }

    // sendType
    protected function getSendType()
    {
        if (!strtolower($this->sendType)) {
            if (count($this->attach) >= 1) {
                $this->sendType = 'mms';
            }
            else if ($this->getByteLength($this->memo) > 80) {
                $this->sendType = 'lms';
            }
            else {
                $this->sendType = 'sms';
            }
        }
        return $this->sendType;
    }

    // attach
    protected function getAttach()
    {
        $arr = array();

        if (count($this->attach) >= 1) {
            for ($i = 0; $i < count($this->attach); $i++) {
                if (!file_exists($this->attach[$i])) {
                    $this->sendType = 'lms';
                    continue;
                }
                $arr[] = array(
                    'name' => 'mms-images.jpg',
                    'body' => base64_encode(file_get_contents($this->attach[$i]))
                );
            }
        }

        return $arr;
    }

    // headers
    protected function getTimestamp()
    {
        list($microtime, $timestamp) = explode(' ',microtime());
        $time = $timestamp.substr($microtime, 2, 3);

        $this->timestamp = $time;
    }

    protected function setCommonHeader()
    {
        $this->getTimestamp();

        $this->addHeader(0, 'Content-Type: application/json; charset=UTF-8');
        $this->addHeader(1, 'x-ncp-apigw-timestamp: '.$this->timestamp);
        $this->addHeader(2, 'x-ncp-iam-access-key: '.$this->getAccesskey());
        $this->addHeader(3, 'x-ncp-apigw-signature-v2: '.$this->getSigstamp());
    }

    protected function addHeader($content, $value)
    {
        $this->smsHeaderArray[$content] = $value;
    }

    // make body
    protected function makeBody()
    {
        $body = array();

        // default
        $body = array(
            'type' => $this->getSendType(),
            'contentType' => 'COMM',
            'countryCode' => $this->countryCode,
            'from' => $this->getFrom(),
            'subject' => $this->subject,
            'content' => $this->memo
        );

        // reserve data
        if ($this->reserveTime) {
            $body['reserveTime'] = $this->reserveTime;
            $body['reserveTimeZone'] = $this->reserveTimeZone;
        }

        // to
        foreach ($this->to as $key => $value) {
            $body['messages'][] = array(
                'to' => $value
            );
        }

        // attach
        if (count($this->attach) >= 1) {
            $body['files'] = $this->getAttach();
        }

        // scheduleCode
        if ($this->scheduleCode) {
            $body['scheduleCode'] = $this->scheduleCode;
        }

        return $body;
    }

    // send
    public function send()
    {
        global $CONF;

        if (!$CONF['sms_key1'] || !$CONF['sms_key2'] || !$CONF['sms_key3'] || !$CONF['sms_key4']) return false;

        $this->setCommonHeader();

        $response = Func::url_get_contents(
            $this->getApiUrl().$this->getServiceId().'/messages', // url
            true, // post
            $this->smsHeaderArray, // header
            json_encode($this->makeBody()) // body
        );
        $json = $response;

        return $json;
    }

}
