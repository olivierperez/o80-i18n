<?php
namespace o80;

abstract class I18NTestCase extends \PHPUnit_Framework_TestCase {

    protected function getTestResourcePath($resourcepath) {
        return __DIR__ . '/../resources/'.$resourcepath;
    }

    protected function readTestResource($resourcepath) {
        return file_get_contents($this->getTestResourcePath($resourcepath));
    }

}
