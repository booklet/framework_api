<?php
// Save orginal file and udate database.

class NewPaperClipSave
{
    private $model_object;
    private $attachment_name;
    private $file; // normalize file array or file path string
    private $style_dir;
    private $safe_file_name;
    private $file_path;
    private $source_file_path;
    private $is_uploaded_file = false;

    use NewPaperClipPathTrait;

    public function __construct(array $params)
    {
        $this->model_object = $params['model_object'];
        $this->attachment_name = $params['attachment_name'];

        if (is_array($params['file'])) {
            $this->is_uploaded_file = true;

            if (isset($params['file']['tmp_name'])) {
                // if file array
                $this->file = $params['file'];
            } else {
                // if array of arrays
                $this->file = $params['file'][0];
            }
        } else {
            $this->file = $params['file'];
        }

        $this->style_dir = $this->attachmentDirectory($this->attachment_name, NewPaperClip::ORIGINAL);
        $this->safe_file_name = $this->safeFileName($this->file);

        $this->file_path = $this->style_dir . '/' . $this->safe_file_name;
        // If array get tmp_name value or passed file path
        $this->source_file_path = $this->file['tmp_name'] ?? $this->file;
    }

    public function saveOriginalFile()
    {
        if ($this->createStyleDirectory($this->style_dir)) {
            $this->copyOriginalFile();
            $this->updateModelAttachmentData();
        }
    }

    public function copyOriginalFile()
    {
        //  die(print_r(is_readable($this->source_file_path) , true));
        //  die(print_r($this->source_file_path , true));
        //  die(print_r(fileperms($this->style_dir) , true));
        //  die(print_r(file_exists($this->source_file_path) , true));
        //  33152 which is 0600
        //  chmod($this->source_file_path, 0604);
        //  touch($this->source_file_path);
        if (!file_exists($this->source_file_path)) {
            throw new Exception('File missing: ' . $this->source_file_path);
        }

        if ($this->is_uploaded_file) {
            if (!rename($this->source_file_path, $this->file_path)) {
                throw new Exception('Orginal file copy (rename) failure.');
            }
        } else {
            if (!copy($this->source_file_path, $this->file_path)) {
                throw new Exception('Orginal file copy failure.');
            }
        }

        chmod($this->file_path, 0644);
    }

    public function updateModelAttachmentData()
    {
        $content_type = $this->file['type'] ?? mime_content_type($this->file_path);
        $file_size = $this->file['size'] ?? filesize($this->file_path);
        $params = [
            $this->attachment_name . '_file_name' => $this->safe_file_name,
            $this->attachment_name . '_file_size' => $file_size,
            $this->attachment_name . '_content_type' => $content_type,
            $this->attachment_name . '_updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->model_object->update($params, ['validate' => false, 'callbacks' => false]);
    }

    private function safeFileName($file)
    {
        $name = $file['name'] ?? pathinfo($file)['basename'];

        return StringUntils::sanitizeFileName($name);
    }
}
