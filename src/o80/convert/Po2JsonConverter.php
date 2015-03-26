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
        $json = PHP_VERSION_ID >= 50400 ?
            json_encode($this->json, JSON_PRETTY_PRINT | ~(JSON_ERROR_UTF8 | JSON_HEX_QUOT | JSON_HEX_APOS)) :
            $this->prettyPrint(json_encode($this->json, ~(JSON_ERROR_UTF8 | JSON_HEX_QUOT | JSON_HEX_APOS)));
        $json = str_replace('\\\\n', '\\n', $json);
        $json = str_replace('\\\\\\"', '\\"', $json);
        return $json;
    }

    /**
     * Picked up on https://gist.github.com/odan/7a04c02dbce59217a33c gist.
     *
     * @param string $json JSON object
     * @return string The String representation of JSON ojbect
     */
    private function prettyPrint($json) {
        $tokens = preg_split('|([\{\}\]\[,])|', $json, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = '';
        $indent = 0;
        $ind = '  ';
        $lineBreak = "\n";

        $inLiteral = false;
        foreach ($tokens as $token) {
            if ($token == '') {
                continue;
            }
            $prefix = str_repeat($ind, $indent);
            if (!$inLiteral && ($token == '{' || $token == '[')) {
                $indent++;
                if (($result != '') && ($result[(strlen($result) - 1)] == $lineBreak)) {
                    $result .= $prefix;
                }
                $result .= $token . $lineBreak;
            } elseif (!$inLiteral && ($token == '}' || $token == ']')) {
                $indent--;
                $prefix = str_repeat($ind, $indent);
                $result .= $lineBreak . $prefix . $token;
            } elseif (!$inLiteral && $token == ',') {
                $result .= $token . $lineBreak;
            } else {
                $result .= ($inLiteral ? '' : $prefix) . $token;
                // Count # of unescaped double-quotes in token, subtract # of
                // escaped double-quotes and if the result is odd then we are
                // inside a string literal
                if ((substr_count($token, "\"") - substr_count($token, "\\\"")) % 2 != 0) {
                    $inLiteral = !$inLiteral;
                }
            }
        }
        return $result;
    }
}
