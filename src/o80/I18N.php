<?php
namespace o80;

class I18N {

    private $defaultLang;

    private $dict;

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
        return in_array($key, $this->dict) ? $this->dict[$key] : $key;
    }

    /**
     * @param mixed $defaultLang
     */
    public function setDefaultLang($defaultLang) {
        $this->defaultLang = $defaultLang;
    }
}
