<?php
class NewPaperClip
{
    const FILES_DIRECTORY = 'system/files/';
    const ORIGINAL = 'original';
    const DEFAULT_MISSING_URL_PATH = 'system/files/:class/:attachment/:style/missing.png';
    const DEFAULT_FILE_EXTENSION = 'jpg';

    private $model_object;

    use NewPaperClipPathTrait;

    public function __construct($model_object)
    {
        $this->model_object = $model_object;
    }

    public function saveFile(string $attachment_name, $file)
    {
        // Validacja nastapi przy wywolaniu funkcji valid() lub save(), tu juz nie validujemy
        $this->saveOriginalFile($attachment_name, $file);

        // Process all attachment styles
        $this->model_object->{$attachment_name . 'Reprocess'}();
    }

    public function saveOriginalFile(string $attachment_name, $file)
    {
        $saver = new NewPaperClipSave([
            'model_object' => $this->model_object,
            'attachment_name' => $attachment_name,
            'file' => $file,
        ]);

        $saver->saveOriginalFile();
    }

    public function process(string $attachment_name)
    {
        return $this->reprocess($attachment_name);
    }

    // Create styles filesbase on style params
    public function reprocess(string $attachment_name)
    {
        // TODO
        // Destroy styles that was removed in model declaration
        foreach ($this->attachmentStyles($attachment_name) as $style_name => $style_params) {
            try {
                $this->reprocessStyles($attachment_name, $style_name, $style_params);
            } catch (Throwable $t) {
                $this->logErrorCreatingStyle($t, $style_name, $attachment_name, $style_params);
            }
        }
    }

    public function reprocessStyles($attachment_name, $style_name, $style_params)
    {
        $original_file_path = $this->attachmentPath($attachment_name);
        $style_file_path = $this->attachmentPath($attachment_name, $style_name);
        $style_file_dir = $this->attachmentDirectory($attachment_name, $style_name);

        $this->createStyleDirectory($style_file_dir);

        $processor = new NewPaperClipProcessor($original_file_path);
        $processor->processFileAndSave($style_params, $style_file_path);
    }

    //    public function attachmentValid(string $attachment_name, string $file)
    //    {
    //        $file_path = $file['tmp_name'] ?? $file;
    //        $validator = new NewPaperClipValidator($this->model_object, $attachment_name, $file_path);
    //
    //        return $validator->valid();
    //    }

    public function destroy(string $attachment_name)
    {
        // Clear database
        $this->model_object->update([
            $attachment_name . '_file_name' => null,
            $attachment_name . '_file_size' => null,
            $attachment_name . '_content_type' => null,
            $attachment_name . '_updated_at' => null,
        ], ['validate' => false, 'callbacks' => false]);

        // Delete styles folders
        foreach ($this->attachmentStyles($attachment_name, ['include_original' => true]) as $style_name => $style_params) {
            $path_to_destroy = $this->attachmentDirectory($attachment_name, $style_name);

            // Check if path is safe to destroy
            $root = defined('ROOT_DIR') ? ROOT_DIR : '';
            if (StringUntils::isInclude($path_to_destroy, $root . self::FILES_DIRECTORY) and
                StringUntils::isInclude($path_to_destroy, $this->classUnderscoreName())) {
                FilesUntils::deleteDirectoryAndEverythingIn($path_to_destroy);
            }
        }

        return true;
    }

    // List of styles
    public function attachmentStyles(string $attachment_name, array $params = [])
    {
        $styles = $this->model_object->hasAttachedFile()[$attachment_name]['styles'];

        if (isset($params['include_original'])) {
            return array_merge([self::ORIGINAL => null], $styles);
        }

        return $styles;
    }

    // "previewSave" > "preview"
    public static function getAttachmentNameFromFunctionName(string $function_name)
    {
        $parts = preg_split('/(?=[A-Z])/', $function_name);

        return $parts[0];
    }

    private function logErrorCreatingStyle($t, $style_name, $attachment_name, $style_params)
    {
        if (class_exists('Logger')) {
            Logger::alert('Error creating style ' . $style_name . ' for attachment ' . $attachment_name, [
                'attachment_name' => $attachment_name,
                'style_name' => $style_name,
                'style_params' => $style_params,
                'message' => $t->getMessage(),
                'backtrace' => $t->getTraceAsString(),
                'throwable' => $t,
            ]);
        }
    }
}
