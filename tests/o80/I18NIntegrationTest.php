<?php
namespace o80;

class I18NIntegrationTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider langsProvider
     */
    function testFindKeyFromFile($defaultLang, $acceptLang, $sessionLang, $getLang) {
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
        );
    }

}
