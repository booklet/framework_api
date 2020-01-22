<?php
include_once 'tests/fixtures/paperclip/NewPaperClipTestingClass.php';

class NewPaperClipValidatorTest extends \CustomPHPUnitTestCase
{
    public $skip_database_clear_before = ['all'];

    public function testValidContentType()
    {
        $pdf_type_file = 'tests/fixtures/paperclip/tests_files/client-test-file-09.pdf';
        $jpg_type_file = 'tests/fixtures/paperclip/tests_files/animal.jpg';
        $png_type_file = 'tests/fixtures/paperclip/tests_files/client-test-file-05.png';

        $paper_clip_testing_class = new NewPaperClipTestingClass();

        $validator = new NewPaperClipValidator($paper_clip_testing_class, 'preview', $pdf_type_file);
        $this->assertEquals($validator->valid(), true);

        $validator = new NewPaperClipValidator($paper_clip_testing_class, 'preview', $png_type_file);
        $this->assertEquals($validator->valid(), false);
        $this->assertEquals($validator->errors(), ['preview' => ['has an illegal type (image/png).']]);
    }

    public function testValidContentTypeFromFilesArray()
    {
        $file = [
            'name' => 'client-test-file',
            'type' => 'application/pdf',
            'size' => 334554,
            'tmp_name' => 'tests/fixtures/paperclip/tests_files/client-test-file-09.pdf',
        ];

        $paper_clip_testing_class = new NewPaperClipTestingClass();

        $validator = new NewPaperClipValidator($paper_clip_testing_class, 'preview', $file);
        $this->assertEquals($validator->valid(), true);

        $file = [
            'name' => 'client-test-file',
            'type' => 'image/png',
            'size' => 334554,
            'tmp_name' => 'tests/fixtures/paperclip/tests_files/client-test-file-09.pdf',
        ];

        $validator = new NewPaperClipValidator($paper_clip_testing_class, 'preview', $file);
        $this->assertEquals($validator->valid(), false);
        $this->assertEquals($validator->errors(), ['preview' => ['has an illegal type (image/png).']]);
    }

    public function testValidFileSize()
    {
        $below_2mb = 'tests/fixtures/paperclip/tests_files/animal.jpg';
        $over_2mb = 'tests/fixtures/paperclip/tests_files/client-test-file-03.tif';

        $paper_clip_testing_class = new NewPaperClipTestingClass();

        $validator = new NewPaperClipValidator($paper_clip_testing_class, 'preview', $below_2mb);
        $this->assertEquals($validator->valid(), true);

        $validator = new NewPaperClipValidator($paper_clip_testing_class, 'preview', $over_2mb);
        $this->assertEquals($validator->valid(), false);
        $this->assertEquals($validator->errors(), ['preview' => ['is too big (max 2 MB).']]);
    }

    public function testValidFileSizeFromFilesArray()
    {
        $file = [
            'name' => 'client-test-file',
            'type' => 'image/jpeg',
            'size' => 1024,
            'tmp_name' => 'tests/fixtures/paperclip/tests_files/animal.jpg',
        ];

        $paper_clip_testing_class = new NewPaperClipTestingClass();

        $validator = new NewPaperClipValidator($paper_clip_testing_class, 'preview', $file);
        $this->assertEquals($validator->valid(), true);

        $file = [
            'name' => 'client-test-file',
            'type' => 'image/tiff',
            'size' => 3097152,
            'tmp_name' => 'tests/fixtures/paperclip/tests_files/client-test-file-03.tif',
        ];

        $validator = new NewPaperClipValidator($paper_clip_testing_class, 'preview', $file);

        $this->assertEquals($validator->valid(), false);
        $this->assertEquals($validator->errors(), ['preview' => ['is too big (max 2 MB).']]);
    }
}
