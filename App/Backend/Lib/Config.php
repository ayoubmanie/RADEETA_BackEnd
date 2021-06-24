<?php

namespace Lib;

class Config
{

    protected $configFileName = '';
    protected $fileContent = '';

    public function __construct($configFileName)
    {
        $this->configFileName = $configFileName;
    }


    public function get()
    {
        if ($this->fileContent == '') {
            $this->openFile();
        }
        return  $this->fileContent;
    }

    protected function openFile()
    {
        $json = file_get_contents($this->configFileName);
        $this->fileContent = json_decode($json);
    }
}