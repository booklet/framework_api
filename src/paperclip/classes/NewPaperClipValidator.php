<?php
class NewPaperClipValidator
{
    private $model_object;
    private $attachment_name;
    private $file_path;
    private $errors = [];

    public function __construct($model_object, string $attachment_name, $file)
    {
        $this->model_object = $model_object;
        $this->attachment_name = $attachment_name;
        $this->file = $file;
    }

    public function valid()
    {
        $attachment = $this->model_object->hasAttachedFile()[$this->attachment_name];

        if (isset($attachment['content_type'])) {
            $this->isValidContentType($attachment['content_type']);
        }

        if (isset($attachment['max_size'])) {
            $this->isValidMaxSize($attachment['max_size']);
        }

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

    private function isValidContentType($content_types)
    {
        $content_type = $this->file['type'] ?? mime_content_type($this->file);

        if (!in_array($content_type, array_keys($content_types))) {
            $this->addError($this->attachment_name, 'has an illegal type (' . $content_type . ').');
        }
    }

    private function isValidMaxSize($max_size)
    {
        $file_size = $this->file['size'] ?? filesize($this->file);

        if ($file_size > $max_size) {
            $human_file_max_size = FilesUntils::humanFilesize($max_size);

            $this->addError($this->attachment_name, 'is too big (max ' . $human_file_max_size . ').');
        }
    }

    private function addError($attr, $error)
    {
        if (!isset($this->errors[$attr])) {
            $this->errors[$attr] = [];
        }

        $this->errors[$attr][] = $error;
    }

    public function errors()
    {
        return $this->errors;
    }
}
