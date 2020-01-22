<?php
// Add dynamic paperclip methods to model instances
// Example for "preview" attachment:
// $this->preview()
// $this->previewPath(), $this->previewPath('style_name')
// $this->previewUrl(), $this->previewUrl('style_name')
// $this->previewSave(array $file) or (string $file_path) without validation!
// $this->previewReprocess()
// $this->previewDestroy()

trait NewPaperClipTrait
{
    public function __call($function_name, $args)
    {
        // "previewPath" -> "preview"
        $attachment_name = NewPaperClip::getAttachmentNameFromFunctionName($function_name);

        if (array_key_exists($attachment_name, $this->hasAttachedFile())) {
            // previewPath()
            if (StringUntils::areEndsWith($function_name, 'Path')) {
                $paperclip = new NewPaperClip($this);
                $style = $args[0] ?? null;

                return $paperclip->attachmentPath($attachment_name, $style);
            }

            // previewUrl()
            if (StringUntils::areEndsWith($function_name, 'Url')) {
                $paperclip = new NewPaperClip($this);
                $style = $args[0] ?? null;

                return $paperclip->attachmentUrl($attachment_name, $style);
            }

            // previewSave(array $file) or (string $file_path)
            // $model->preview = $file
            if (StringUntils::areEndsWith($function_name, 'Save')) {
                $paperclip = new NewPaperClip($this);
                $data = $args[0] ?? null;

                return $paperclip->saveFile($attachment_name, $data);
            }

            // previewReprocess()
            if (StringUntils::areEndsWith($function_name, 'Reprocess')) {
                $paperclip = new NewPaperClip($this);

                return $paperclip->reprocess($attachment_name);
            }

            // previewValid()
            // if (StringUntils::areEndsWith($function_name, 'Valid')) {
            //     $paperclip = new NewPaperClip($this);
            //     // $style = $args[0] ?? null;
            //     $file = $args[1] ?? null;

            //     return $paperclip->attachmentValid($attachment_name, $file);
            // }

            // previewDestroy()
            if (StringUntils::areEndsWith($function_name, 'Destroy')) {
                $paperclip = new NewPaperClip($this);

                return $paperclip->destroy($attachment_name);
            }

            // preview()
            return [
                $attachment_name . '_file_name' => $this->{$attachment_name . '_file_name'},
                $attachment_name . '_file_size' => $this->{$attachment_name . '_file_size'},
                $attachment_name . '_content_type' => $this->{$attachment_name . '_content_type'},
                $attachment_name . '_updated_at' => $this->{$attachment_name . '_updated_at'},
            ];
        }

        return parent::__call($function_name, $args);
    }
}
