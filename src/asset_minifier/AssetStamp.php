<?php
class AssetStamp
{
    private $files_paths;

    public function __construct(array $files_paths)
    {
        $this->files_paths = $files_paths;
    }

    public function getStamp()
    {
        $aku = '';
        foreach ($this->files_paths as $file_path) {
            $aku .= filemtime($file_path);
        }

        return md5($aku);
    }
}
