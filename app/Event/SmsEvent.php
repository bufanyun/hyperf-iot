<?php
namespace App\Event;

class SmsEvent
{
    // 建议这里定义成 public 属性，以便监听器对该属性的直接使用，或者你提供该属性的 Getter
    public $sms;

    public function __construct($sms)
    {
        $this->sms = $sms;
    }
}
