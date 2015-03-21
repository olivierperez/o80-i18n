<?php
namespace o80\convert;

abstract class FromPoConverter implements Converter {

    function __construct() {
    }

    /**
     * This method convert from a format to another.
     *
     * @param string $source The input string of the convertion
     * @return string The output of the convertion
     */
    function convert($source) {

        $this->onConvert();

        preg_match_all("/(?:#(.+)(?:\r|\n)*)|(?:msgid \"(.+)\"(?:\r|\n)*msgstr \"(.+)\"(?:\r|\n)*)/i", $source, $results, PREG_SET_ORDER);
        $sectionName = null;

        foreach ($results as $result) {
            if (!empty($result[1])) { // Section
                if ($sectionName !== null) {
                    $this->afterSection($sectionName);
                }
                $sectionName = trim(str_replace('#', '', $result[1]));
                $this->beforeSection($sectionName);
            } else { // Translation
                $key = $result[2];
                $value = $result[3];
                $this->onEntry($key, $value);
            }
        }

        if ($sectionName !== null) {
            $this->afterSection($sectionName);
        }
        return $this->toString();
    }

    protected abstract function onConvert();

    protected abstract function beforeSection($sectionName);

    protected abstract function afterSection($sectionName);

    protected abstract function onEntry($key, $value);

    protected abstract function toString();

}
