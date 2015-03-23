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

//        preg_match_all("/(?:#(.+)(?:\r|\n)*)|(?:msgid \"(.+)\"(?:\r|\n)*msgstr \"(.+)\"(?:\r|\n)*)/i", $source, $results, PREG_SET_ORDER);
        $lines = explode(PHP_EOL, $source);
        $sectionName = null;
        $msgid = null;
        $msgstr = null;

        foreach ($lines as $line) {
            if (!empty($line)) {
                if ($line[0] === '#') { // Section
                    if ($sectionName !== null) {
                        $this->afterSection($sectionName);
                    }
                    $sectionName = trim(str_replace('#', '', $line));
                    $this->beforeSection($sectionName);

                } elseif (substr($line, 0, 6) === 'msgid ') { // Msg Id
                    if ($msgid != null) {
                        $this->onEntry($msgid, $msgstr);
                    }
                    preg_match('/^msgid \"(.*)\"\s*$/', $line, $matches);
                    $msgid = $matches[1];
                    $msgstr = null;

                } elseif (substr($line, 0, 7) === 'msgstr ') { // Msg Str
                    preg_match('/^msgstr \"(.*)\"\s*$/', $line, $matches);
                    $msgstr = $matches[1];

                } elseif (preg_match('/^\s*\"(.*)\"\s*$/', $line, $matches)) { // Add on MsgId or MsgStr
                    if ($msgstr === null) {
                        $msgid .= $matches[1];
                    } else {
                        $msgstr .= $matches[1];
                    }

                }
            }
        }

        if ($msgid != null) {
            $this->onEntry($msgid, $msgstr);
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
