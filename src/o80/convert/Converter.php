<?php
namespace o80\convert;

interface Converter {

    /**
     * This method convert from a format to another.
     *
     * @param string $source The input string of the convertion
     * @return string The output of the convertion
     */
    function convert($source);

}
