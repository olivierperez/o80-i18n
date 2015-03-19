<?php
namespace o80;

class DictProviderUnitTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var DictProvider
     */
    private $provider;

    public function setUp() {
        $this->provider = new DictProvider();
        $this->provider->setLangsPath(__DIR__ . '/../resources/langs/');
    }

    /**
     * 'en' will match to 'en'
     */
    function testLoadExactFileTranslation_en() {
        // given

        // when
        $dict = $this->provider->load(array('en', ''));

        // then
        $this->assertNotNull($dict);
        $this->assertEquals(1, count($dict));
        $this->assertEquals('en Hello World!', $dict['HELLOWORLD']);
    }

    /**
     * 'en_GB' will match to 'en'
     */
    function ttestLoadMatchingFileTranslation_enGB() {
        // given

        // when
        $dict = $this->provider->load(array('en_GB', ''));

        // then
        $this->assertNotNull($dict);
        $this->assertEquals(1, count($dict));
        $this->assertEquals('en Hello World!', $dict['HELLOWORLD']);
    }

    /**
     * 'en_US' will match to 'en_US' instead of 'en'
     */
    function testLoadExactFileTranslation_en_US() {
        // given

        // when
        $dict = $this->provider->load(array('en_US', ''));

        // then
        $this->assertNotNull($dict);
        $this->assertEquals(1, count($dict));
        $this->assertEquals('en_US Hello World!', $dict['HELLOWORLD']);
    }

    /**
     * Try to load 'fr' file.
     */
    function testDontLoadNonExistingFile() {
        // given

        // when
        $dict = $this->provider->load(array('fr', ''));

        // then
        $this->assertNull($dict);
    }

    /**
     * Try to load 'fr' file, using default lang 'en'
     */
    function testLoadFromDefaultLangFile() {
        // given

        // when
        $dict = $this->provider->load(array('fr', 'en'));

        // then
        $this->assertNotNull($dict);
        $this->assertEquals(1, count($dict));
        $this->assertEquals('en Hello World!', $dict['HELLOWORLD']);
    }

}
