<?php
namespace o80;

class I18N {

    private $defaultLang;

    private $dict = null;

    private $path;

    private $dictProvider = null;

    private function __construct() {
        $this->dictProvider = new DictProvider();
    }

    public static function newInstance() {
        $instance = new I18N();

        return $instance;
    }

    public function getAvailableLangs() {
        $langs = array();
        if (isset($_GET) && array_key_exists('lang', $_GET)) {
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

    public function get($key) {
        if ($this->dict === null) {
            $this->dict = $this->load();
        }
        return array_key_exists($key, $this->dict) ? $this->dict[$key] : '[missing key: ' . $key . ']';
    }

    /**
     * @param mixed $path
     */
    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * @param mixed $defaultLang
     */
    public function setDefaultLang($defaultLang) {
        $this->defaultLang = $defaultLang;
    }

    /**
     * @return array|null
     */
    public function load() {
        $this->dictProvider->setLangsPath($this->path);
        $dict = $this->dictProvider->load($this->getAvailableLangs());

        return $dict;
    }

    public function getHttpAcceptLanguages() {
        $result = array();
        if (isset($_SERVER) && array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
            preg_match_all("/([[:alpha:]]{1,8}(?:-[[:alpha:]|-]{1,8})?)" .
                           "(?:\\s*;\\s*q\\s*=\\s*(1\\.0{0,3}|0\\.\\d{0,3}))?\\s*(?:,|$)/i",
                           $_SERVER['HTTP_ACCEPT_LANGUAGE'], $hits);
            $hits = array_combine($hits[1], $hits[2]);

            foreach ($hits as $key => $hit) {
                $lang = str_replace('-', '_', $key);
                $result[] = $lang;
            }
        }

        return $result;
    }
}
