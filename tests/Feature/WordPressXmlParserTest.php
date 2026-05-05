<?php

namespace Tests\Feature;

use App\Modules\CMS\Services\WordPressXmlParser;
use Tests\TestCase;

class WordPressXmlParserTest extends TestCase
{
    public function test_parser_extracts_summary_and_wordpress_entities_from_fixture(): void
    {
        $result = app(WordPressXmlParser::class)->parseFile(base_path('tests/Fixtures/wordpress-small.xml'));

        $this->assertSame('Legacy Site', $result['site']['title']);
        $this->assertSame(1, $result['summary']['authors']);
        $this->assertSame(1, $result['summary']['categories']);
        $this->assertSame(1, $result['summary']['tags']);
        $this->assertSame(1, $result['summary']['posts']);
        $this->assertSame(1, $result['summary']['pages']);
        $this->assertSame(1, $result['summary']['attachments']);
        $this->assertSame(1, $result['summary']['unknown_post_types']);
        $this->assertSame('hello-world', $result['posts'][0]['slug']);
        $this->assertSame('<p>Hello content</p>', $result['posts'][0]['content_html']);
        $this->assertSame('300', $result['posts'][0]['featured_image_id']);
        $this->assertSame('about', $result['pages'][0]['slug']);
        $this->assertSame('https://legacy.example.test/uploads/hero.jpg', $result['attachments'][0]['attachment_url']);
    }

    public function test_parser_handles_malformed_xml_gracefully(): void
    {
        $result = app(WordPressXmlParser::class)->parse('<rss><channel>');

        $this->assertSame(0, $result['summary']['posts']);
        $this->assertNotEmpty($result['warnings']);
    }
}
