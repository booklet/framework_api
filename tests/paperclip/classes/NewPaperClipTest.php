<?php
include_once 'tests/fixtures/paperclip/NewPaperClipTestingClass.php';

class NewPaperClipTest extends \CustomPHPUnitTestCase
{
    public $skip_database_clear_before = ['all'];

    public function testAttachmentStyles()
    {
        $paper_clip_testing_class = new NewPaperClipTestingClass();
        $paper_clip = new NewPaperClip($paper_clip_testing_class);

        $expect_results = [
            'medium' => '300x300>',
            'thumbnail' => '100x100#',
        ];

        $this->assertEquals($paper_clip->attachmentStyles('preview'), $expect_results);
    }

    public function testSaveFile()
    {
        $paper_clip_testing_class = new NewPaperClipTestingClass();
        $paper_clip = new NewPaperClip($paper_clip_testing_class);

        $paper_clip->saveFile('preview', 'tests/fixtures/paperclip/tests_files/animal.jpg');

        $this->assertEquals($paper_clip_testing_class->preview_file_name, 'animal.jpg');
        $this->assertEquals($paper_clip_testing_class->preview_file_size, 47851);
        $this->assertEquals($paper_clip_testing_class->preview_content_type, 'image/jpeg');
        $this->assertNotNull($paper_clip_testing_class->preview_updated_at);

        $this->assertEquals(file_exists('system/files/new_paper_clip_testing_class/preview/000/000/000/original/animal.jpg'), true);
        $this->assertEquals(file_exists('system/files/new_paper_clip_testing_class/preview/000/000/000/medium/animal.jpg'), true);
        $this->assertEquals(file_exists('system/files/new_paper_clip_testing_class/preview/000/000/000/thumbnail/animal.jpg'), true);

        FilesUntils::deleteDirectoryAndEverythingIn('system/files/new_paper_clip_testing_class');
    }

    public function testReprocess()
    {
        $paper_clip_testing_class = new NewPaperClipTestingClass();
        $paper_clip = new NewPaperClip($paper_clip_testing_class);

        $paper_clip->saveFile('preview', 'tests/fixtures/paperclip/tests_files/animal.jpg');

        $this->assertEquals($paper_clip_testing_class->preview_file_name, 'animal.jpg');
        $this->assertEquals($paper_clip_testing_class->preview_file_size, 47851);
        $this->assertEquals($paper_clip_testing_class->preview_content_type, 'image/jpeg');
        $this->assertNotNull($paper_clip_testing_class->preview_updated_at);
        $this->assertEquals(file_exists('system/files/new_paper_clip_testing_class/preview/000/000/000/medium/animal.jpg'), true);

        $image = new Imagick('system/files/new_paper_clip_testing_class/preview/000/000/000/medium/animal.jpg');
        $this->assertEquals($image->getImageGeometry(), ['width' => 300, 'height' => 225]);
        $image = new Imagick('system/files/new_paper_clip_testing_class/preview/000/000/000/thumbnail/animal.jpg');
        $this->assertEquals($image->getImageGeometry(), ['width' => 100, 'height' => 100]);

        $paper_clip_testing_class->fake_styles = [
            'preview' => [
                'styles' => [
                    'medium' => '200x200>',
                    'thumbnail' => '50x50#',
                ],
            ],
        ];
        $paper_clip = new NewPaperClip($paper_clip_testing_class);
        $paper_clip->reprocess('preview');

        $image = new Imagick('system/files/new_paper_clip_testing_class/preview/000/000/000/medium/animal.jpg');
        $this->assertEquals($image->getImageGeometry(), ['width' => 200, 'height' => 150]);
        $image = new Imagick('system/files/new_paper_clip_testing_class/preview/000/000/000/thumbnail/animal.jpg');
        $this->assertEquals($image->getImageGeometry(), ['width' => 50, 'height' => 50]);

        FilesUntils::deleteDirectoryAndEverythingIn('system/files/new_paper_clip_testing_class');
    }

    public function testDestroy()
    {
        $paper_clip_testing_class = new NewPaperClipTestingClass();
        $paper_clip = new NewPaperClip($paper_clip_testing_class);
        $paper_clip->saveFile('preview', 'tests/fixtures/paperclip/tests_files/animal.jpg');
        $paper_clip->reprocess('preview');

        $this->assertEquals($paper_clip_testing_class->preview_file_name, 'animal.jpg');

        $paper_clip->destroy('preview');

        $this->assertNull($paper_clip_testing_class->preview_file_name);
    }

    public function testGetAttachmentNameFromFunctionName()
    {
        $this->assertEquals(NewPaperClip::getAttachmentNameFromFunctionName('previewPath'), 'preview');
    }
}
