<?php
include_once 'tests/fixtures/paperclip/NewPaperClipTestingClass.php';

class NewPaperClipSaveFilesTest extends \CustomPHPUnitTestCase
{
    public $skip_database_clear_before = ['all'];
    private $paper_clip;
    private $base_path = 'system/files/new_paper_clip_testing_class/preview/000/000/000/';
    private $original_path = 'system/files/new_paper_clip_testing_class/preview/000/000/000/original/';
    private $medium_path = 'system/files/new_paper_clip_testing_class/preview/000/000/000/medium/';
    private $thumbnail_path = 'system/files/new_paper_clip_testing_class/preview/000/000/000/thumbnail/';

    public function setUp()
    {
        FilesUntils::deleteDirectoryAndEverythingIn('system/files/new_paper_clip_testing_class');
        $paper_clip_testing_class = new NewPaperClipTestingClass();
        $paper_clip_testing_class->fake_styles = [
            'preview' => [
                'styles' => [
                    'medium' => '300x300>',
                    'thumbnail' => '100x100#',
                ],
            ],
        ];
        $this->paper_clip = new NewPaperClip($paper_clip_testing_class);
    }

    public function tearDown()
    {
        FilesUntils::deleteDirectoryAndEverythingIn('system/files/new_paper_clip_testing_class');
    }

    public function check($path, $file_name, $width, $height, $files_count)
    {
        $image = new Imagick($path . $file_name);
        $this->assertEquals($image->getImageGeometry(), ['width' => $width, 'height' => $height]);
        $this->assertEquals(count(FilesUntils::getFilesFromDirectory($path)), $files_count);
    }

    public function testSaveJpg()
    {
        $this->paper_clip->saveFile('preview', 'tests/fixtures/paperclip/tests_files/animal.jpg');
        $original_file_name = $styles_file_name = 'animal.jpg';

        $this->check($this->original_path, $original_file_name, 640, 480, 1);
        $this->check($this->medium_path, $styles_file_name, 300, 225, 1);
        $this->check($this->thumbnail_path, $styles_file_name, 100, 100, 1);
    }

    public function testSavePdfWithRotationAttribute()
    {
        // This file has rotate params what caused wrong orientation when change to jpg
        $this->paper_clip->saveFile('preview', 'tests/fixtures/paperclip/tests_files/client-test-file-02-rotate.pdf');
        $original_file_name = 'client_test_file_02_rotate.pdf';
        $styles_file_name = 'client_test_file_02_rotate.jpg';

        $this->check($this->original_path, $original_file_name, 238, 351, 2);
        $this->check($this->original_path, $styles_file_name, 992, 1465, 2);
        $this->check($this->medium_path, $styles_file_name, 203, 300, 1);
        $this->check($this->thumbnail_path, $styles_file_name, 100, 100, 1);
    }

    public function testSaveTiff()
    {
        $this->paper_clip->saveFile('preview', 'tests/fixtures/paperclip/tests_files/client-test-file-03.tif');
        $original_file_name = 'client_test_file_03.tif';
        $styles_file_name = 'client_test_file_03.jpg';

        $this->check($this->original_path, $original_file_name, 758, 757, 1);
        $this->check($this->medium_path, $styles_file_name, 300, 300, 1);
        $this->check($this->thumbnail_path, $styles_file_name, 100, 100, 1);
    }

    public function testSaveGif()
    {
        $this->paper_clip->saveFile('preview', 'tests/fixtures/paperclip/tests_files/client-test-file-04.gif');
        $original_file_name = 'client_test_file_04.gif';
        $styles_file_name = 'client_test_file_04.jpg';

        $this->check($this->original_path, $original_file_name, 721, 392, 1);
        $this->check($this->medium_path, $styles_file_name, 300, 163, 1);
        $this->check($this->thumbnail_path, $styles_file_name, 100, 100, 1);
    }

    public function testSavePng()
    {
        $this->paper_clip->saveFile('preview', 'tests/fixtures/paperclip/tests_files/client-test-file-05.png');
        $original_file_name = 'client_test_file_05.png';
        $styles_file_name = 'client_test_file_05.jpg';

        $this->check($this->original_path, $original_file_name, 2223, 1678, 1);
        $this->check($this->medium_path, $styles_file_name, 300, 226, 1);
        $this->check($this->thumbnail_path, $styles_file_name, 100, 100, 1);
    }

    public function testSaveAi()
    {
        $this->paper_clip->saveFile('preview', 'tests/fixtures/paperclip/tests_files/client-test-file-06.ai');
        $original_file_name = 'client_test_file_06.ai';
        $styles_file_name = 'client_test_file_06.jpg';

        $this->check($this->original_path, $original_file_name, 595, 842, 2);
        $this->check($this->medium_path, $styles_file_name, 212, 300, 1);
        $this->check($this->thumbnail_path, $styles_file_name, 100, 100, 1);

        $this->paper_clip->saveFile('preview', 'tests/fixtures/paperclip/tests_files/client-test-file-06a.ai');
        $original_file_name = 'client_test_file_06a.ai';
        $styles_file_name = 'client_test_file_06a.jpg';

        $this->check($this->original_path, $original_file_name, 181, 125, 4);
        $this->check($this->medium_path, $styles_file_name, 300, 206, 2);
        $this->check($this->thumbnail_path, $styles_file_name, 100, 100, 2);
    }

    public function testSaveEps()
    {
        $this->paper_clip->saveFile('preview', 'tests/fixtures/paperclip/tests_files/client-test-file-07.eps');
        $original_file_name = 'client_test_file_07.eps';
        $styles_file_name = 'client_test_file_07.jpg';

        $this->check($this->original_path, $original_file_name, 73, 83, 1);
        $this->check($this->medium_path, $styles_file_name, 73, 83, 1);
        $this->check($this->thumbnail_path, $styles_file_name, 100, 100, 1);
    }

    public function testSaveSvg()
    {
        $this->paper_clip->saveFile('preview', 'tests/fixtures/paperclip/tests_files/client-test-file-08.svg');
        $original_file_name = 'client_test_file_08.svg';
        $styles_file_name = 'client_test_file_08.jpg';

        $this->check($this->original_path, $original_file_name, 60, 60, 1);
        $this->check($this->medium_path, $styles_file_name, 60, 60, 1);
        $this->check($this->thumbnail_path, $styles_file_name, 100, 100, 1);
    }

    public function testSavePdfColors()
    {
        $this->paper_clip->saveFile('preview', 'tests/fixtures/paperclip/tests_files/client-test-file-09.pdf');
        $original_file_name = 'client_test_file_09.pdf';
        $styles_file_name = 'client_test_file_09.jpg';

        $this->check($this->original_path, $original_file_name, 241, 255, 2);
        $this->check($this->medium_path, $styles_file_name, 283, 300, 1);
        $this->check($this->thumbnail_path, $styles_file_name, 100, 100, 1);
    }
}
