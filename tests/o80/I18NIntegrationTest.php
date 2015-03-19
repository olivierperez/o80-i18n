<?php
namespace o80;

class I18NIntegrationTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     * @dataProvider langsProvider
     */
    function shouldFindKeyFromTheRightFile($defaultLang, $acceptLang, $sessionLang, $getLang) {
        // given
        $i18n = I18N::newInstance();
        $i18n->setPath(__DIR__ . '/../resources/langs');
        $i18n->setDefaultLang($defaultLang);
        $_GET['lang'] = $getLang;
        $_SESSION['lang'] = $sessionLang;
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $acceptLang;

        // when
        $text = $i18n->get('HELLOWORLD');

        // then
        $this->assertEquals('en Hello World!', $text);

    }

    public function langsProvider() {
        return array(
            // The lines below check the order of $_GET, $_SESSION, $_SERVER, $defaultLang
            array('en', '', '', ''),
            array('', 'en', '', ''),
            array('fr', 'en', '', ''),
            array('', '', 'en', ''),
            array('fr', '', 'en', ''),
            array('fr', 'fr', 'en', ''),
            array('', '', '', 'en'),
            array('fr', '', '', 'en'),
            array('fr', '', 'fr', 'en'),
            array('fr', 'fr', '', 'en'),
            array('fr', 'fr', 'fr', 'en'),

            // The lines below check the fallback on other available languages
            array('fr', 'fr', 'fr', 'en'),
            array('en', 'fr', 'fr', 'fr'),
            array('en_US', 'fr', 'en', 'fr'),
            array('en_US', 'en', 'fr', 'en_GB')
        );
    }

}
