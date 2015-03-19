<?php
namespace o80;

class I18N {

    private static $instance;

    private $defaultLang;

    private $dict = null;

    private $path;

    private $dictProvider = null;

    private $useLangFromGET = true;

    public function __construct($dictProvider = null) {
        $this->dictProvider = $dictProvider != null ? $dictProvider : new DictProvider();
    }

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new I18N();
        }

        return self::$instance;
    }

    public function getAvailableLangs() {
        $langs = array();
        if ($this->useLangFromGET && isset($_GET) && array_key_exists('lang', $_GET)) {
            $langs[] = $_GET['lang'];
        }
        if (isset($_SESSION) && array_key_exists('lang', $_SESSION)) {
            $langs[] = $_SESSION['lang'];
        }
        $langs = array_merge($langs, $this->getHttpAcceptLanguages());
        if (!empty($this->defaultLang)) {
            $langs[] = $this->defaultLang;
        }

        return $langs;
    }

    /**
     * Get the translation of a key. The language will be automaticaly selected in :
     * $\_GET, $\_SESSION, $\_SERVER or $defaultLang attribute.
     *
     * @param string $key The key of the translation
     * @return string The translation, or <code>[missing key:$key]</code> if not found
     * @throws CantLoadDictionaryException Thrown when there is no file to be loaded for the prefered languages
     */
    public function get($key) {
        if ($this->dict === null) {
            $this->dict = $this->load();
        }
        return array_key_exists($key, $this->dict) ? $this->dict[$key] : '[missing key: ' . $key . ']';
    }

    /**
     * Set the path of the dictionaries files directory.
     *
     * @param string $path The path of the directory containing the dictionaries files
     */
    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * Set the default language.
     *
     * @param string $defaultLang The default language to use when the other doesn't match
     */
    public function setDefaultLang($defaultLang) {
        $this->defaultLang = $defaultLang;
    }

    /**
     * Load the dictionary that match the prefered languages.
     *
     * @return array The associative array of dictionary
     * @throws CantLoadDictionaryException Thrown when there is no match between languages files and selected languages.
     */
    public function load() {
        $this->dictProvider->setLangsPath($this->path);
        $dict = $this->dictProvider->load($this->getAvailableLangs());

        if ($dict === null) {
            throw new CantLoadDictionaryException(CantLoadDictionaryException::NO_MATCHING_FILES);
        }

        return $dict;
    }


    public function getHttpAcceptLanguages() {
        $result = array();
        if (isset($_SERVER) && array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
            preg_match_all("/([[:alpha:]]{1,8}(?:-[[:alpha:]|-]{1,8})?)" .
                           "(?:\\s*;\\s*q\\s*=\\s*(?:1\\.0{0,3}|0\\.\\d{0,3}))?\\s*(?:,|$)/i",
                           $_SERVER['HTTP_ACCEPT_LANGUAGE'], $hits);

            foreach ($hits[1] as $hit) {
                $lang = str_replace('-', '_', $hit);
                $result[] = $lang;
            }
        }

        return $result;
    }

    public function useLangFromGET($useLangFromGET) {
        $this->useLangFromGET = $useLangFromGET;
    }
}
