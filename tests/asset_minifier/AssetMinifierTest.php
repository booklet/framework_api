<?php
class AssetMinifierTest extends \CustomPHPUnitTestCase
{
    public function jsData()
    {
        return [
            'output_file_path' => 'tests/fixtures/asset_minifier/output',
            'output_file_name_prefix' => 'test-js',
            'output_file_url' => 'public/minifier/js',
            'input_files' => [
                'tests/fixtures/asset_minifier/file1.js',
                'tests/fixtures/asset_minifier/file2.js',
            ],
        ];
    }

    public function testMinifierJs()
    {
        $asset = new AssetMinifierJs($this->jsData());
        $path = $asset->getMinifierFilePath();

        $this->assertContains('tests/fixtures/asset_minifier/output/test-js-', $path);
        $this->assertContains('.js', $path);
        $this->assertEquals(filesize($path), 79);

        $url_path = $asset->getMinifierUrlPath();

        $this->assertContains('public/minifier/js/test-js-', $url_path);

        $html = $asset->getHtmlTag();

        $this->assertContains('<script type="text/javascript" src="/public/minifier/js/test-js-', $html);
        $this->assertContains('.js"></script>', $html);

        unlink($path);
    }

    public function testMinifierJsOptions()
    {
        $data = $this->jsData();
        $data['output_url_prefix'] = 'panel-klienta/';
        $asset = new AssetMinifierJs($data);

        $url_path = $asset->getMinifierUrlPath();

        $this->assertContains('panel-klienta/public/minifier/js/test-js-', $url_path);

        $file_path = $asset->getMinifierFilePath();

        $this->assertContains('tests/fixtures/asset_minifier/output/test-js-', $file_path);

        $html = $asset->getHtmlTag();

        $this->assertContains('<script type="text/javascript" src="/panel-klienta/public/minifier/js/test-js-', $html);
        $this->assertContains('.js"></script>', $html);
    }

    public function testMinifierJsDevelopment()
    {
        $data = $this->jsData();
        $data['environment'] = 'development';
        $data['output_url_prefix'] = 'panel-klienta/';
        $asset = new AssetMinifierJs($data);
        $html = $asset->getHtmlTag();

        $this->assertContains('<script type="text/javascript" src="/panel-klienta/tests/fixtures/asset_minifier/file1.js"></script>', $html);
        $this->assertContains('<script type="text/javascript" src="/panel-klienta/tests/fixtures/asset_minifier/file2.js"></script>', $html);
    }

    public function cssData()
    {
        return [
            'output_file_path' => 'tests/fixtures/asset_minifier/output',
            'output_file_name_prefix' => 'test-css',
            'output_file_url' => 'public/minifier/css',
            'input_files' => [
                'tests/fixtures/asset_minifier/file1.css',
                'tests/fixtures/asset_minifier/file2.css',
            ],
        ];
    }

    public function testMinifierCss()
    {
        $asset = new AssetMinifierCss($this->cssData());
        $path = $asset->getMinifierFilePath();

        $this->assertContains('tests/fixtures/asset_minifier/output/test-css-', $path);
        $this->assertContains('.css', $path);
        $this->assertEquals(filesize($path), 126);

        $url_path = $asset->getMinifierUrlPath();

        $this->assertContains('public/minifier/css/test-css-', $url_path);

        $html = $asset->getHtmlTag();

        $this->assertContains('<link rel="stylesheet" media="all" href="/public/minifier/css/test-css-', $html);
        $this->assertContains('.css" />', $html);

        unlink($path);
    }

    public function testMinifierCssOptions()
    {
        $data = $this->cssData();
        $data['output_url_prefix'] = 'panel-klienta/';
        $asset = new AssetMinifierCss($data);

        $url_path = $asset->getMinifierUrlPath();

        $this->assertContains('panel-klienta/public/minifier/css/test-css-', $url_path);

        $file_path = $asset->getMinifierFilePath();

        $this->assertContains('tests/fixtures/asset_minifier/output/test-css-', $file_path);

        $html = $asset->getHtmlTag();

        $this->assertContains('<link rel="stylesheet" media="all" href="/panel-klienta/public/minifier/css/test-css-', $html);
        $this->assertContains('.css" />', $html);
    }

    public function testMinifierCssDevelopment()
    {
        $data = $this->cssData();
        $data['environment'] = 'development';
        $data['output_url_prefix'] = 'panel-klienta/';
        $asset = new AssetMinifierCss($data);
        $html = $asset->getHtmlTag();

        $this->assertContains('<link rel="stylesheet" media="all" href="/panel-klienta/tests/fixtures/asset_minifier/file1.css" />', $html);
        $this->assertContains('<link rel="stylesheet" media="all" href="/panel-klienta/tests/fixtures/asset_minifier/file2.css" />', $html);
    }
}
