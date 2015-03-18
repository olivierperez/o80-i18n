<?php
namespace o80;

class SessionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider langsProvider
     */
    public function testShouldFindIpOfClient($defaultLang, $getLang, $sessionLang, $acceptLang, $expectedLang) {
        // given
        $i18n = I18N::newInstance();
        $i18n->setDefaultLang($defaultLang);
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

    /**
     * @dataProvider httpAcceptLAnguagesProvider
     */
    public function testGetHttpAcceptLanguages($httpAcceptLanguages, $expected) {
        // given
        $i18n = I18N::newInstance();
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $httpAcceptLanguages;

        // when
        $langs = $i18n->getHttpAcceptLanguages();

        // then
        $this->assertEquals($expected, $langs);
    }

    public function httpAcceptLAnguagesProvider() {
        return array(
            array('en', array('en' => 1)),
            array('en-US,en;q=0.8,fr-FR;q=0.5,fr;q=0.3', array('en-US' => 1, 'en' => 0.8, 'fr-FR' => 0.5, 'fr' => 0.3))
        );
    }

    public function testLoadShouldCallDictProvider() {
        // given
        $i18n = I18N::newInstance();
        $providerMock = $this->getMock('\\o80\\DictProvider');

        $reflectionClass = new \ReflectionClass($i18n);
        $reflectionProperty = $reflectionClass->getProperty('dictProvider');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($i18n, $providerMock);

        // expects
        $providerMock->expects($this->once())->method('setLangsPath');
        $providerMock->expects($this->once())->method('load');

        // when
        $this->invoke($i18n, 'load', $i18n);

        // then
    }

    public function testDontLoadDictMoreThanOnce() {
        // given
        $i18n = $this->getMockBuilder('\\o80\\I18N')
            ->disableOriginalConstructor()
            ->setMethods(array('load'))
            ->getMock();

        // assert
        $i18n->expects($this->once())
            ->method('load')
            ->willReturn(array('a'=>'A', 'b'=>'B'));

        // when
        $a = $i18n->get('a');
        $b = $i18n->get('b');
        $missingKeyC = $i18n->get('c');

        // then
        $this->assertEquals('A', $a);
        $this->assertEquals('B', $b);
        $this->assertEquals('[missing key: c]', $missingKeyC);
    }

    private function invoke(&$object, $methodName) {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionMethod = $reflectionClass->getMethod($methodName);
        $reflectionMethod->setAccessible(true);

        $params = array_slice(func_get_args(), 2); // get all the parameters after $methodName
        return $reflectionMethod->invokeArgs($object, $params);
    }

}
