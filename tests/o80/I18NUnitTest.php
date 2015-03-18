<?php
namespace o80;

class SessionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider langsProvider
     */
    public function testShouldFindIpOfClient($defaultLang, $getLang, $sessionLang, $acceptLang, $expectedLang) {
        // given
        $i18n = new I18N($defaultLang);
        $_GET['lang'] = $getLang;
        $_SESSION['lang'] = $sessionLang;
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $acceptLang;

        // when
        $lang = $i18n->getLang();

        // then
        $this->assertTrue($expectedLang === $lang);
    }

    public function langsProvider() {
        return array(
            array(null, '', '', '', null), // nothing defined
            array('en', '', '', '', 'en'), // default lang 'en'
            array('', 'fr', '', '', 'fr'), // $_GET
            array('en', 'fr', '', '', 'fr'), // $_GET 'fr' > default 'en'
            array('', '', 'gb', '', 'gb'), // $_SESSION
            array('en', '', 'gb', '', 'gb'), // $_SESSION 'gb' > default 'en'
            array('en', 'fr', 'gb', '', 'fr'), // $_GET 'fr' > $_SESSION 'gb' > default 'en'
            array('', '', '', 'de', 'de'), // HTTP_ACCEPT_LANGUAGE
            array('en', '', '', 'de', 'de'), // HTTP_ACCEPT_LANGUAGE > default 'en'
            array('en', '', 'gb', 'de', 'gb'), // $_SESSION 'gb' > HTTP_ACCEPT_LANGUAGE > default 'en'
            array('en', 'fr', 'gb', 'de', 'fr'), // $_GET 'fr' > $_SESSION 'gb' > HTTP_ACCEPT_LANGUAGE > default 'en'
        );
    }

    private function invoke(&$object, $methodName) {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionMethod = $reflectionClass->getMethod($methodName);
        $reflectionMethod->setAccessible(true);

        $params = array_slice(func_get_args(), 2); //get all the parameters after $methodName
        return $reflectionMethod->invokeArgs($object, $params);
    }

}
