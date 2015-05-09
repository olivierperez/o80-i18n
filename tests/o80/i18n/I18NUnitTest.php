<?php
namespace o80\i18n;

use o80\I18NTestCase;

class I18NUnitTest extends I18NTestCase {

    public function setUp() {
        $_GET = array();
        $_SESSION = array();
        $_SERVER = array();
    }

    /**
     * @test
     * @dataProvider availableLangsProvider
     */
    public function shouldOrderAvailableLangs($getLang, $sessionLang, $acceptLangs, $defaultLang) {
        // given
        $_GET['lang'] = $getLang;
        $_SESSION['lang'] = $sessionLang;

        $i18n = $this->getMockBuilder('\\o80\\i18n\\I18N')
            ->setMethods(array('getHttpAcceptLanguages'))
            ->getMock();
        $i18n->setDefaultLang($defaultLang);
        $i18n->expects($this->once())
            ->method('getHttpAcceptLanguages')
            ->willReturn($acceptLangs);

        // when
        $langs = $i18n->getUserLangs();

        // then
        $expected = array($getLang, $sessionLang);
        $expected = array_merge($expected, $acceptLangs);
        $expected[] = $defaultLang;
        $this->assertEquals($expected, $langs);
    }

    public function availableLangsProvider() {
        return array(
            array('en', 'en', array('en' => 1), 'en'),
            array('fr', 'en_US', array('en' => 1), 'en'),
        );
    }

    /**
     * @test
     * @dataProvider httpAcceptLAnguagesProvider
     */
    public function shouldGetHttpAcceptLanguages($httpAcceptLanguages, $expected) {
        // given
        $i18n = new I18N();
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $httpAcceptLanguages;

        // when
        $langs = $i18n->getHttpAcceptLanguages();

        // then
        $this->assertEquals($expected, $langs);
    }

    public function httpAcceptLAnguagesProvider() {
        return array(
            array('en', array('en')),
            array('en-US,en;q=0.8,fr-FR;q=0.5,fr;q=0.3', array('en_US', 'en', 'fr_FR', 'fr'))
        );
    }

    /**
     * @test
     */
    public function shouldLoadShouldCallJsonProvider() {
        // given
        $i18n = new I18N();
        $providerMock = $this->getMock('\\o80\\i18n\\JsonProvider');

        $reflectionClass = new \ReflectionClass($i18n);
        $reflectionProperty = $reflectionClass->getProperty('dictProvider');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($i18n, $providerMock);

        // expects
        $providerMock->expects($this->once())->method('setLangsPath');
        $providerMock->expects($this->once())->method('load')->willReturn(array('Key' => 'Message'));

        // when
        $this->invoke($i18n, 'load', $i18n);

        // then
    }

    /**
     * @test
     */
    public function shouldNotLoadDictMoreThanOnce() {
        // given
        $i18n = $this->getMockBuilder('\\o80\\i18n\\I18N')
            ->setMethods(array('load'))
            ->getMock();

        // assert
        $i18n->expects($this->once())
            ->method('load')
            ->willReturn(array('s' => array('a' => 'A', 'b' => 'B')));

        // when
        $a = $i18n->get('s', 'a');
        $b = $i18n->get('s', 'b');
        $missingKeySC = $i18n->get('s', 'c');
        $missingKeyXC = $i18n->get('x', 'c');

        // then
        $this->assertEquals('A', $a);
        $this->assertEquals('B', $b);
        $this->assertEquals('[missing key: s.c]', $missingKeySC);
        $this->assertEquals('[missing key: x.c]', $missingKeyXC);
    }

    /**
     * @test
     * @expectedException \o80\i18n\CantLoadDictionaryException
     * @expectedExceptionMessage \o80\i18n\CantLoadDictionaryException::NO_MATCHING_FILES
     */
    public function shouldThrowExceptionWhenNoFileAreMatchingTheLanguages() {
        // given
        $i18n = new I18N();
        $providerMock = $this->getMock('\\o80\\i18n\\JsonProvider');

        $reflectionClass = new \ReflectionClass($i18n);
        $reflectionProperty = $reflectionClass->getProperty('dictProvider');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($i18n, $providerMock);

        // expects
        $providerMock->expects($this->once())->method('load')->willReturn(null);

        // when
        $this->invoke($i18n, 'load', $i18n);

        // then
    }

    /**
     * @test
     * @dataProvider useLangFromGETProvider
     */
    public function shouldNotLookInto_GET($useLangFromGET, $expected) {
        // given
        $i18n = new I18N();
        $i18n->setDefaultLang('en');
        $_GET['lang'] = 'fr';

        // stub

        // when
        $i18n->useLangFromGET($useLangFromGET);
        $langs = $i18n->getUserLangs();

        // then
        $this->assertEquals($expected, $langs);

    }

    public static function useLangFromGETProvider() {
        return array(
            array(true, array('fr', 'en')),
            array(false, array('en'))
        );
    }

    /**
     * @test
     */
    public function shouldNotGetLoadedLangCodeBeforeLoading() {
        // given
        $i18n = new I18N();

        // when
        $loadedLang = $i18n->getLoadedLang();

        // then
        $this->assertNull($loadedLang);
    }

    /**
     * @test
     */
    public function shouldChangeDateFormatterWhenLoadingALang() {
        // given
        $providerMock = $this->getMock('\\o80\\i18n\\JsonProvider');
        $i18n = new I18N($providerMock);
        $origin = setlocale(LC_CTYPE, 0);

        // stub
        $providerMock->expects($this->once())->method('load')->willReturn(array('a' => 'A'));
        $providerMock->expects($this->once())->method('getLoadedLang')->willReturn('fr');

        // when
        $i18n->load();
        $locale = setlocale(LC_CTYPE, 0);

        // then
        $this->asserttrue($locale == 'fr' || $locale == $origin); // $locale == $origin is a hack where "FR" is not managed by the server
    }

    /**
     * @test
     */
    public function shouldFormatMessages() {
        // given
        $i18n = $this->getMockBuilder('\\o80\\i18n\\I18N')
            ->setMethods(array('load'))
            ->getMock();

        // assert
        $i18n->expects($this->once())
            ->method('load')
            ->willReturn(array('section' => array('hello' => 'Hello %s!', 'count' => '%d lines of code', 'multi' => '%s wrote %d lines of code')));

        // when
        $helloOlivier = $i18n->format('section', 'hello', 'Olivier');
        $countLines = $i18n->format('section', 'count', '5');
        $multi = $i18n->format('section', 'multi', array('Olivier', 10));

        // then
        $this->assertEquals('Hello Olivier!', $helloOlivier);
        $this->assertEquals('5 lines of code', $countLines);
        $this->assertEquals('Olivier wrote 10 lines of code', $multi);
    }

}
