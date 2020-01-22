<?php
trait AssetMinifier
{
    private $output_file_path;
    private $output_file_name_prefix;
    private $input_files;
    private $environment;

    public function __construct($params)
    {
        $this->output_file_path = $params['output_file_path'];
        $this->output_file_name_prefix = $params['output_file_name_prefix'];
        $this->output_file_url = $params['output_file_url'] ?? $this->output_file_path;
        $this->output_url_prefix = $params['output_url_prefix'] ?? '';
        $this->input_files = $params['input_files'];
        $this->environment = $params['environment'] ?? 'production';

        $this->createMinifierFile();
    }

    public function getMinifierFilePath()
    {
        return $this->output_file_path . '/' . $this->getFileName();
    }

    public function getMinifierUrlPath()
    {
        return $this->output_url_prefix . $this->output_file_url . '/' . $this->getFileName();
    }

    public function getHtmlTag()
    {
        $tags = new AssetHtmlTag($this->getFilesUrls(), self::MINIFY_TYPE);

        return $tags->getHtmlTags();
    }

    private function getFileName()
    {
        return $this->output_file_name_prefix . '-' . $this->getStamp() . self::MINIFY_FILE_EXTENSION;
    }

    private function getFilesPaths()
    {
        $files = $this->isDevelopmentEnvironment() ? $this->input_files : $this->getMinifierFilePath();

        return (array) $files;
    }

    private function getFilesUrls()
    {
        foreach ($this->input_files as $file_path) {
            $input_files[] = $this->output_url_prefix . $file_path;
        }
        $files = $this->isDevelopmentEnvironment() ? $input_files : $this->getMinifierUrlPath();

        return (array) $files;
    }

    private function getStamp()
    {
        return (new AssetStamp($this->input_files))->getStamp();
    }

    private function createMinifierFile()
    {
        if ($this->isDevelopmentEnvironment() or $this->isMinifierFileExists()) {
            return;
        }

        file_put_contents($this->getMinifierFilePath(), $this->minifyData());
    }

    private function minifyData()
    {
        $minifier_class_name = 'MatthiasMullie\Minify\\' . self::MINIFY_TYPE;
        $minifier = new $minifier_class_name($this->input_files);

        return $minifier->minify();
    }

    private function isMinifierFileExists()
    {
        return file_exists($this->getMinifierFilePath());
    }

    private function isDevelopmentEnvironment()
    {
        return $this->environment == 'development';
    }
}
