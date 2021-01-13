<?php
namespace App\Event;

class SmsEvent
{
    public $data;

    public string $function;

    public function __construct($data, string $function)
    {
        $this->data = $data;
        $this->function = $function;
    }
}
