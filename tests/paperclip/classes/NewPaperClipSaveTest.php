<?php
include_once 'tests/fixtures/paperclip/NewPaperClipTestingClass.php';

class NewPaperClipSaveTest extends \CustomPHPUnitTestCase
{
    public $skip_database_clear_before = ['all'];

    public function testSaveFileFromArray()
    {
        copy('tests/fixtures/paperclip/tests_files/animal.jpg', 'tests/fixtures/paperclip/tmp/animal.jpg');

        $paper_clip_testing_class = new NewPaperClipTestingClass();
        $paper_clip_testing_class->id = 1234;

        $file = $this->fileParamsArray();

        $saver = new NewPaperClipSave([
            'model_object' => $paper_clip_testing_class,
            'attachment_name' => 'preview',
            'file' => $file,
        ]);
        $saver->saveOriginalFile();

        $this->assertEquals($paper_clip_testing_class->preview_file_name, 'animal.jpg');
        $this->assertEquals($paper_clip_testing_class->preview_file_size, 1000);
        $this->assertEquals($paper_clip_testing_class->preview_content_type, 'image/jpeg');
        $this->assertNotNull($paper_clip_testing_class->preview_updated_at);
        $this->assertEquals(file_exists('system/files/new_paper_clip_testing_class/preview/000/001/234/original/animal.jpg'), true);

        FilesUntils::deleteDirectoryAndEverythingIn('system/files/new_paper_clip_testing_class');
    }

    public function testSaveFileFromString()
    {
        copy('tests/fixtures/paperclip/tests_files/animal.jpg', 'tests/fixtures/paperclip/tmp/animal.jpg');

        $paper_clip_testing_class = new NewPaperClipTestingClass();
        $paper_clip_testing_class->id = 1234;

        $saver = new NewPaperClipSave([
            'model_object' => $paper_clip_testing_class,
            'attachment_name' => 'preview',
            'file' => 'tests/fixtures/paperclip/tmp/animal.jpg',
        ]);
        $saver->saveOriginalFile();

        $this->assertEquals($paper_clip_testing_class->preview_file_name, 'animal.jpg');
        $this->assertEquals($paper_clip_testing_class->preview_file_size, 47851);
        $this->assertEquals($paper_clip_testing_class->preview_content_type, 'image/jpeg');
        $this->assertNotNull($paper_clip_testing_class->preview_updated_at);
        $this->assertEquals(file_exists('system/files/new_paper_clip_testing_class/preview/000/001/234/original/animal.jpg'), true);

        FilesUntils::deleteDirectoryAndEverythingIn('system/files/new_paper_clip_testing_class');
    }

    public function fileParamsArray()
    {
        return [
            'name' => 'animal.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => 'tests/fixtures/paperclip/tmp/animal.jpg',
            'error' => 0,
            'size' => 1000,
        ];
    }

    public function uploadFileParamsArray()
    {
        return [
            'preview' => [
                'name' => [
                    'animal.jpg',
                    'not exists.jpg',
                ],
                'type' => [
                    'image/jpeg',
                    'image/jpeg',
                ],
                'tmp_name' => [
                    'tests/fixtures/paperclip/tmp/animal.jpg',
                    'tests/fixtures/paperclip/tmp/not-exists.jpg',
                ],
                'error' => [
                    0,
                    0,
                ],
                'size' => [
                    1000,
                    2000,
                ],
            ],
        ];
    }
}
