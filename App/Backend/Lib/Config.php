<?php

namespace Lib;

class Config
{

    protected $file = __DIR__ . '../../../Config/config.json';
    protected $content;


    public function get(): object
    {
        if ($this->content == null) {
            $this->openFile();
        }
        return  $this->content;
    }

    protected function openFile()
    {
        $json = file_get_contents($this->file);
        $this->content = json_decode($json);
    }
}