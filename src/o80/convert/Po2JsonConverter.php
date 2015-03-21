<?php
namespace o80\convert;

class Po2JsonConverter extends FromPoConverter {

    private $currentSection = null;

    private $json = null;

    function __construct() {
    }

    protected function onConvert() {
        $this->json = array();
    }

    protected function beforeSection($sectionName) {
        $this->currentSection = $sectionName;
        $this->json[$this->currentSection] = array();
    }

    protected function afterSection($sectionName) {
        $this->currentSection = null;
    }

    protected function onEntry($key, $value) {
        if ($this->currentSection === null) {
            $this->json[$key] = $value;
        } else {
            $this->json[$this->currentSection][$key] = $value;
        }
    }

    protected function toString() {
        return json_encode($this->json, JSON_PRETTY_PRINT | ~JSON_ERROR_UTF8);
    }
}
