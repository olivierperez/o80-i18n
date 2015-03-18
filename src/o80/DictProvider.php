<?php
namespace o80;

class DictProvider {

    private $path = __DIR__;

    function __construct() {}

    public function setLangsPath($path) {
        $this->path = $path;
    }

    public function load($lang, $defaultLang) {
        // List file names
        $files = $this->listLangFiles();

        // Keep the first file that matches the default lang
        $defaultFile = null;

        // Check all file names
        foreach ($files as $file) {
            // Extract locale from filename
            $fileLocale = substr($file, 0, strlen($file) - 4);

            if (\Locale::filterMatches($lang, $fileLocale)) { // Check if filename matches $lang
                return $this->loadFile($file);
            }

            if ($defaultFile == null && \Locale::filterMatches($defaultLang, $fileLocale)) {
                $defaultFile = $file;
            }
        }

        if ($defaultFile != null) {
            return $this->loadFile($defaultFile);
        }

        return null;

    }

    /**
     * List the files from the {@code path} directory and sort them by filename size desc.
     *
     * @return array Array of files found
     */
    private function listLangFiles() {
        $files = array_diff(scandir($this->path), array('..', '.'));
        uasort($files, function ($a, $b) {
            return strlen($a) < strlen($b);
        });
        return $files;
    }

    /**
     * Parse a INI file from the {@code path} directry.
     *
     * @param $filename string The name of the file
     * @return array The dictionary
     */
    private function loadFile($filename) {
        return parse_ini_file($this->path . '/' . $filename);
    }

}
