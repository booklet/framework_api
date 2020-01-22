<?php
class PaperClipTestingClass
{
    public $id;
    public $preview_file_name;
    public $preview_content_type;
    public $preview_updated_at;

    public function getPreviewUrl($style = 'original')
    {
        $paper_clip = new PaperClip($this);

        return Config::get('web_address') . '/' . $paper_clip->getFile('preview', $style, $this->preview_file_name);
    }

    public function hasAttachedFile()
    {
        return [
          'preview' => [
              'styles' => ['medium' => '300x300>', 'thumbnail' => '100x100#'],
          ],
      ];
    }

    //    public function hasAttachedFile()
    //    {
    //        return [
    //            'preview' => [
    //                'styles' => [
    //                    'medium' => '300x300>',
    //                    'thumbnail' => ['100x100#', 'format' => 'png']
    //                ],
    //                'path' => "public/system/:attachment/:id/:style/:filename",
    //                'default_style' => 'medium',
    //                'default_url' => '/images/foto_:style.jpg'
    //            ],
    //            'avatar' => [
    //                'styles' => [
    //                    'large' => '300x300',
    //                    'thumbnail' => ['100x100#', 'format' => 'png']
    //                ],
    //                'path' => "public/system/:attachment/:id/:style/:filename",
    //                'default_url' => 'public/system/:attachment/:id/missing_:style.jpg'
    //            ],
    //        ];
    //    }
    //
    //    // co z validacja
    //    // validates_attachment_content_type :avatar, content_type: /\Aimage/
    //    // validates_attachment_size

    public function save()
    {
        $this->preview_updated_at = date('d-m-Y H:i:s');
    }
}
