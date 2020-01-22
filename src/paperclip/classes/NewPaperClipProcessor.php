<?php
class NewPaperClipProcessor
{
    private $original_file_path;
    private $mime_type;
    private $pdf_to_jpg_original_file_path;

    use NewPaperClipPathTrait;

    public function __construct($original_file_path)
    {
        $this->original_file_path = $original_file_path;
        $this->mime_type = NewPaperClipMIME::getFileType($original_file_path);
    }

    public function processFileAndSave($style_params, string $target_save_path, $page_num = 1)
    {
        if (!NewPaperClipMIME::isAllowedMIMEType($this->mime_type)) {
            throw new Exception('Paperclip. Not allowed file type: ' . $this->mime_type . '.');
        }

        // Convert original pdf to jpg
        // To create other styles we use this jpg instead use heavy pdfs
        $this->createJpgIfNotExistAndItsPdfFile();
        $file_path = $this->pdf_to_jpg_original_file_path ?? $this->original_file_path;

        list($width, $height) = $this->styleDimensions($style_params);
        $resizing_action = $this->styleResizingOption($style_params);

        $page = '[' . ($page_num - 1) . ']';

        ImagickUntils::$resizing_action($file_path . $page, $target_save_path, $width, $height);
    }

    private function createJpgIfNotExistAndItsPdfFile()
    {
        $this->pdf_to_jpg_original_file_path = null;
        $path_parts = pathinfo($this->original_file_path);
        $target_jpg_path = $path_parts['dirname'] . '/' . $path_parts['filename'] . '.jpg';

        if ($this->isPdfFile() and !file_exists($target_jpg_path)) {
            $pdf_tools = new PDFTools($this->original_file_path);
            $pdf_tools->convertToJpg($target_jpg_path);
        }

        // Use jpg file in next styles operations
        if ($this->isPdfFile()) {
            $this->pdf_to_jpg_original_file_path = $target_jpg_path;
        }
    }

    private function isPostScriptFile()
    {
        return NewPaperClipMIME::isPostScriptFileByMIME($this->mime_type);
    }

    private function isPdfFile()
    {
        return NewPaperClipMIME::getFileType($this->original_file_path) == 'application/pdf';
    }

    private function styleResizingOption($style_params)
    {
        // 100x100> - miniatura zostanie zmodyfikowana tylko wtedy, gdy jest ona większa niż wymagane wymiary, zachowując proporcje
        // 100x100# - miniatura zostanie centralnie przycięta, zapewniając wymagane wymiary
        // 100x100  - miniatura zostanie przeskalowana do wymiaru zachowując proporcje

        if (StringUntils::areEndsWith($style_params, '>')) {
            return 'resizeIfLarger';
        }

        if (StringUntils::areEndsWith($style_params, '#')) {
            return 'crop';
        }

        return 'resize';
    }

    // 100x100>, 100x100#, 100x100 => ['width' => 100, 'height' => 100]
    public function styleDimensions($style_params)
    {
        list($width, $height) = explode('x', $style_params);

        return [intval($width), intval($height)];
    }
}
