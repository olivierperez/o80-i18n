<?php
namespace o80;

class DictProvider {

    private $path = __DIR__;

    function __construct() {}

    /**
     * @param $path
     */
    public function setLangsPath($path) {
        $this->path = $path;
    }

    /**
     * @param $langs array Ordered list of accepted languages, prefered ones are first
     * @return array|null The dictionary or null if not found
     */
    public function load($langs) {
        // List file names
        $files = $this->listLangFiles();

        foreach ($langs as $lang) {
            $dict = $this->loadMatchingFile($files, $lang);
            if ($dict !== null) {
                return $dict;
            }
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

    /**
     * @param $files
     * @param $lang
     * @return array
     */
    private function loadMatchingFile($files, $lang) {
        // Check all file names
        foreach ($files as $file) {
            // Extract locale from filename
            $fileLocale = substr($file, 0, strlen($file) - 4);

            if (\Locale::filterMatches($lang, $fileLocale)) { // Check if filename matches $lang
                return $this->loadFile($file);
            }
        }
        return null;
    }

}
