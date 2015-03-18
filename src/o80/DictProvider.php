<?php
namespace o80;

class DictProvider {

    private $path;

    function __construct() {
    }

    public function setLangsPath($path) {
        $this->path = $path;
    }

    public function load($lang) {
        $files = array_diff(scandir($this->path), array('..', '.'));
        uasort($files, function($a, $b){
            return strlen($a) < strlen($b);
        });


        foreach ($files as $file) {
            $fileLocale = substr($file, 0, strlen($file) - 4);
            if (\Locale::filterMatches($lang, $fileLocale)) {
                $filepath = $this->path . '/' .$file;
                return parse_ini_file($filepath);
            }
        }

        return null;

    }

}
