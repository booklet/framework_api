<?php
class AssetHtmlTag
{
    private $files_paths;
    private $content_type;

    public function __construct(array $files_paths, $content_type)
    {
        $this->files_paths = $files_paths;
        $this->content_type = strtolower($content_type);
    }

    public function getHtmlTags()
    {
        $html = '';
        foreach ($this->files_paths as $file_path) {
            $html .= $this->{$this->content_type . 'Tag'}($file_path);
        }

        return $html;
    }

    private function jsTag($file_path)
    {
        return '<script type="text/javascript" src="/' . $file_path . '"></script>' . "\n";
    }

    private function cssTag($file_path)
    {
        return '<link rel="stylesheet" media="all" href="/' . $file_path . '" />' . "\n";
    }
}
