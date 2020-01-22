<?php
class AssetHtmlTagTest extends \CustomPHPUnitTestCase
{
    public function testGetHtmlTagsJs()
    {
        $tag = new AssetHtmlTag([
            'tests/fixtures/asset_minifier/file1.js',
            'tests/fixtures/asset_minifier/file2.js',
        ], 'js');

        $html = $tag->getHtmlTags();
        $this->assertEquals($html, "<script type=\"text/javascript\" src=\"/tests/fixtures/asset_minifier/file1.js\"></script>\n<script type=\"text/javascript\" src=\"/tests/fixtures/asset_minifier/file2.js\"></script>\n");
    }

    public function testGetHtmlTagsCss()
    {
        $tag = new AssetHtmlTag([
            'tests/fixtures/asset_minifier/file1.css',
            'tests/fixtures/asset_minifier/file2.css',
        ], 'css');

        $html = $tag->getHtmlTags();
        $this->assertEquals($html, "<link rel=\"stylesheet\" media=\"all\" href=\"/tests/fixtures/asset_minifier/file1.css\" />\n<link rel=\"stylesheet\" media=\"all\" href=\"/tests/fixtures/asset_minifier/file2.css\" />\n");
    }
}
