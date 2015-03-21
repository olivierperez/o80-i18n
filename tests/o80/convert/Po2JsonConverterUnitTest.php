<?php
namespace o80\convert;

use o80\I18NTestCase;

class Po2JsonConverterUnitTest extends I18NTestCase {

    /**
     * @test
     */
    public function shouldConvertFromPoToJson() {
        // given
        $converter = new Po2JsonConverter();
        $source = $this->readTestResource('langs/en.po');
        $expected = $this->readTestResource('langs/en.json');

        // when
        $json = $converter->convert($source);

        // then
        $this->assertJsonStringEqualsJsonString($expected, $json);
    }

}
