<?php
trait NewPaperClipPathTrait
{
    // return "system/files/paper_clip_testing_class/preview/000/001/234/original"
    public function attachmentDirectory(string $attachment_name, $style = NewPaperClip::ORIGINAL)
    {
        $class_underscore_name = $this->classUnderscoreName();
        $id_path = $this->idPath();

        $root = defined('ROOT_DIR') ? ROOT_DIR : '';
        return $root . NewPaperClip::FILES_DIRECTORY . $class_underscore_name . '/' . $attachment_name . '/' . $id_path . $style;
    }

    // return "system/files/paper_clip_testing_class/preview/000/001/234/original/plik.pdf"
    public function attachmentPath(string $attachment_name, $style = null)
    {
        $style = $style ?? NewPaperClip::ORIGINAL;

        if (!$this->isFilePresent($attachment_name)) {
            return null;
        }

        $dir = $this->attachmentDirectory($attachment_name, $style);
        $file_name = $this->attachmentName($attachment_name, $style);

        return $dir . '/' . $file_name;
    }

    // Dodane przy przejsciu na uzywanie ROOT_DIR,
    // file path i file url to dwie rozne rzeczy!
    public function attachmentUrlDirectory(string $attachment_name, $style = NewPaperClip::ORIGINAL)
    {
        $class_underscore_name = $this->classUnderscoreName();
        $id_path = $this->idPath();

        return NewPaperClip::FILES_DIRECTORY . $class_underscore_name . '/' . $attachment_name . '/' . $id_path . $style;
    }

    // Dodane przy przejsciu na uzywanie ROOT_DIR,
    // file path i file url to dwie rozne rzeczy!
    public function attachmentUrlPath(string $attachment_name, $style = null)
    {
        $style = $style ?? NewPaperClip::ORIGINAL;

        if (!$this->isFilePresent($attachment_name)) {
            return null;
        }

        $dir = $this->attachmentUrlDirectory($attachment_name, $style);
        $file_name = $this->attachmentName($attachment_name, $style);

        return $dir . '/' . $file_name;
    }

    // return "http://api.booklet.dev/system/files/paper_clip_testing_class/preview/000/001/234/original/plik.pdf"
    public function attachmentUrl(string $attachment_name, $style = null)
    {
        $style = $style ?? NewPaperClip::ORIGINAL;

        // to do URLa, wiec nie moze zawierac ROOT_DIR
        $path = $this->attachmentUrlPath($attachment_name, $style);

        if (!$path) {
            // missing path
            $path = $this->pathGenerate(NewPaperClip::DEFAULT_MISSING_URL_PATH, [
                'attachment' => $attachment_name,
                'style' => $style,
            ]);
        }

        return $this->getHost() . '/' . $path;
    }

    public function attachmentName(string $attachment_name, $style = NewPaperClip::ORIGINAL)
    {
        if (!$this->isFilePresent($attachment_name)) {
            return null;
        }

        $original_file_name = $this->model_object->{$attachment_name . '_file_name'};

        if ($style == NewPaperClip::ORIGINAL) {
            return $original_file_name;
        }

        return FilesUntils::getFileName($original_file_name) . '.' . NewPaperClip::DEFAULT_FILE_EXTENSION;
    }

    // []
    public function attachmentPaths(string $attachment_name)
    {
        $paths = [];
        $paths[NewPaperClip::ORIGINAL] = $this->attachmentPath($attachment_name);
        foreach ($this->model_object->hasAttachedFile()[$attachment_name]['styles'] as $style_name => $style_parameters) {
            $paths[$style_name] = $this->attachmentPath($attachment_name, $style_name);
        }

        return $paths;
    }

    // []
    public function attachmentUrls(string $attachment_name)
    {
        $paths = $this->attachmentPaths($attachment_name);

        foreach ($paths as $style_name => &$value) {
            $value = $this->getHost() . '/' . $value;
        }

        return $paths;
    }

    private function isFilePresent(string $attachment_name)
    {
        return !empty($this->model_object->{$attachment_name . '_file_name'});
    }

    private function isFileExists(string $attachment_name)
    {
        $original_file_path = $this->attachmentPath($attachment_name);

        return file_exists($original_file_path);
    }

    // TODO Move to files/directories untils
    private function createStyleDirectory($directory)
    {
        if (file_exists($directory)) {
            return true;
        }

        if (!mkdir($directory, 0755, true)) {
            throw new Exception('Can\'t create directory ' . $directory . '.');
        }

        chmod($directory, 0777);

        return true;
    }

    private function classUnderscoreName()
    {
        // Add to support custom files folder
        $class_constants = (new ReflectionClass($this->model_object))->getConstants();
        if (array_key_exists('PAPERCLIP_FILES_FOLDER', $class_constants)) {
            return StringUntils::camelCaseToUnderscore($class_constants['PAPERCLIP_FILES_FOLDER']);
        }

        return StringUntils::camelCaseToUnderscore(get_class($this->model_object));
    }

    private function idPath()
    {
        return FilesUntils::objectIdToPath($this->model_object);
    }

    // Replace symbols with proper data
    // system/files/:class/:attachment/:style/missing.png';
    // system/files/class_name/pview/medium/missing.png';
    private function pathGenerate(string $path, array $params = [])
    {
        // :class
        $class_underscore_name = $this->classUnderscoreName();
        $path = str_replace(':class', $class_underscore_name, $path);

        // :attachment
        if (isset($params['attachment'])) {
            $path = str_replace(':attachment', $params['attachment'], $path);
        }

        // :id
        $id_path = $this->idPath();
        $path = str_replace(':id', $id_path, $path);

        // :style
        if (isset($params['style'])) {
            $path = str_replace(':style', $params['style'], $path);
        }

        return $path;
    }

    private function getHost()
    {
        $host = Config::get('paperclip_host') ?? null;
        if (!$host) {
            throw new Exception('Paperclip. Missing config "paperclip_host" param.');
        }

        return $host;
    }
}
