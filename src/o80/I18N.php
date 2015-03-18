<?php
namespace o80;

class I18N {

    private $defaultLang;

    private $dict;

    private $path;

    private $dictProvider = null;

    private function __construct() {
        $this->dictProvider = new DictProvider();
    }

    public static function newInstance() {
        $instance = new I18N();

        return $instance;
    }

    public function getLang() {
        if (!empty($_GET['lang'])) {
            return $_GET['lang'];
        } elseif (!empty($_SESSION['lang'])) {
            return $_SESSION['lang'];
        } elseif (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }
        return $this->defaultLang;
    }

    public function get($key) {
        if ($this->dict == null) {
            $this->dict = $this->load();
        }
        return in_array($key, $this->dict) ? $this->dict[$key] : $key;
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
    private function load() {
        $this->dictProvider->setLangsPath($this->path);
        $dict = $this->dictProvider->load($this->getLang(), $this->defaultLang);

        return $dict;
    }

    public function getHttpAcceptLanguages() {
        preg_match_all("/([[:alpha:]]{1,8}(?:-[[:alpha:]|-]{1,8})?)" .
                       "(?:\\s*;\\s*q\\s*=\\s*(1\\.0{0,3}|0\\.\\d{0,3}))?\\s*(?:,|$)/i",
                       $_SERVER['HTTP_ACCEPT_LANGUAGE'], $hits);
        $hits = array_combine($hits[1], $hits[2]);

        foreach ($hits as $key => $hit) {
            if (empty($hit)) {
                $hits[$key] = 1;
            }
        }


        return $hits;
    }
}
