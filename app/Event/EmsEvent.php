<?php
namespace App\Event;

class EmsEvent
{
    public $data;

    public string $function;

    public function __construct($data, string $function)
    {
        $this->data = $data;
        $this->function = $function;
    }
}
