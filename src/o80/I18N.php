<?php
namespace o80;

class I18N {

    private $defaultLang;

    function __construct($defaultLang) {
        $this->defaultLang = $defaultLang;
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
}
