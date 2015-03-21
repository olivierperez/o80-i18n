<?php
namespace o80\i18n;

use o80\I18NTestCase;

class IniProviderUnitTest extends I18NTestCase {

    /**
     * @var IniProvider
     */
    private $iniProvider;

    public function setUp() {
        $this->iniProvider = new IniProvider();
        $this->iniProvider->setLangsPath($this->getTestResourcePath('langs'));
    }

    /**
     * @test
     * 'en' will match to 'en'
     */
    function shouldLoadExactFileTranslation_en() {
        // given

        // when
        $dict = $this->iniProvider->load(array('en', ''));

        // then
        $this->assertNotNull($dict);
        $this->assertEquals(1, count($dict));
        $this->assertEquals('en Hello World!', $dict['HELLOWORLD']);
    }

    /**
     * @test
     * 'en_GB' will match to 'en'
     */
    function shouldLoadMatchingFileTranslation_enGB() {
        // given

        // when
        $dict = $this->iniProvider->load(array('en_GB', ''));

        // then
        $this->assertNotNull($dict);
        $this->assertEquals(1, count($dict));
        $this->assertEquals('en Hello World!', $dict['HELLOWORLD']);
    }

    /**
     * @test
     * 'en_US' will match to 'en_US' instead of 'en'
     */
    function shouldLoadExactFileTranslation_en_US() {
        // given

        // when
        $dict = $this->iniProvider->load(array('en_US', ''));

        // then
        $this->assertNotNull($dict);
        $this->assertEquals(1, count($dict));
        $this->assertEquals('en_US Hello World!', $dict['HELLOWORLD']);
    }

    /**
     * @test
     * Try to load 'fr' file.
     */
    function shouldDontLoadNonExistingFile() {
        // given

        // when
        $dict = $this->iniProvider->load(array('fr', ''));

        // then
        $this->assertNull($dict);
    }

    /**
     * @test
     * Try to load 'fr' file, using default lang 'en'
     */
    function shouldLoadFromDefaultLangFile() {
        // given

        // when
        $dict = $this->iniProvider->load(array('fr', 'en'));

        // then
        $this->assertNotNull($dict);
        $this->assertEquals(1, count($dict));
        $this->assertEquals('en Hello World!', $dict['HELLOWORLD']);
    }

    /**
     * @test
     * @expectedException \o80\i18n\CantLoadDictionaryException
     * @expectedExceptionMessage \o80\i18n\CantLoadDictionaryException::NO_DICTIONARY_FILES
     */
    public function shouldThrowExceptionWhenNoFileArePresentInThePath() {
        // given
        $providerMock = $this->getMock('\\o80\\i18n\\IniProvider', array('listLangFiles'));

        // stub
        $providerMock->method('listLangFiles')->willReturn(array());

        // when
        $providerMock->load(array('fr', 'en'));

        // then
        $providerMock->expects($this->once())->method('listLangFiles');
    }

}
