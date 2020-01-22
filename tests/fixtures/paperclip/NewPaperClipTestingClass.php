<?php
class NewPaperClipTestingClass extends Model
{
    const ALLOWED_CONTENT_TYPE = [
        'application/pdf' => ['pdf'],
        'image/jpeg' => ['jpeg', 'jpg', 'jpe'],
        'image/tiff' => ['tiff', 'tif'],
    ];

    use NewPaperClipTrait;

    public function fields()
    {
        return [
            'id' => ['type' => 'integer'],
            'preview_file_name' => ['type' => 'string'],
            'preview_file_size' => ['type' => 'integer'],
            'preview_content_type' => ['type' => 'string'],
            'preview_updated_at' => ['type' => 'datetime'],
            'pages_count' => ['type' => 'integer'],
            'width' => ['type' => 'integer'],
            'height' => ['type' => 'integer'],
            'is_preview_generated' => ['type' => 'boolean'],
            'created_at' => ['type' => 'datetime'],
            'updated_at' => ['type' => 'datetime'],
        ];
    }

    public static function relations()
    {
        return [
            'items' => ['relation' => 'has_many', 'class' => 'ExampleClass'],
        ];
    }

    public function getPreviewUrl($style = 'original')
    {
        $paper_clip = new NewPaperClip($this);

        return Config::get('web_address') . '/' . $paper_clip->attachmentPath('preview', $style);
    }

    public function hasAttachedFile()
    {
        // Only for test!
        if (isset($this->fake_styles)) {
            return $this->fake_styles;
        }

        return [
            'preview' => [
                'styles' => [
                    'medium' => '300x300>',
                    'thumbnail' => '100x100#',
                ],
                'content_type' => self::ALLOWED_CONTENT_TYPE,
                'max_size' => 2097152, // 2 MB
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

    public function specialPropertis()
    {
        return ['fake_styles'];
    }

    // overwrite save method
    public function save(array $params = [])
    {
        $this->preview_updated_at = date('d-m-Y H:i:s');
    }
}
