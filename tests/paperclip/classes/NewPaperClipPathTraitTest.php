<?php
include_once 'tests/fixtures/paperclip/NewPaperClipTestingClass.php';

class NewPaperClipPathTraitTest extends \CustomPHPUnitTestCase
{
    public $skip_database_clear_before = ['all'];

    public function testAttachmentDirectory()
    {
        $paper_clip_testing_class = new NewPaperClipTestingClass();
        $paper_clip_testing_class->id = 1234;

        $paper_clip = new NewPaperClip($paper_clip_testing_class);
        $get_file = $paper_clip->attachmentDirectory('preview', 'medium');

        $this->assertEquals($get_file, 'system/files/new_paper_clip_testing_class/preview/000/001/234/medium');

        $get_file = $paper_clip->attachmentDirectory('preview');

        $this->assertEquals($get_file, 'system/files/new_paper_clip_testing_class/preview/000/001/234/original');
    }

    public function testAttachmentPath()
    {
        $paper_clip_testing_class = new NewPaperClipTestingClass();
        $paper_clip_testing_class->id = 1234;

        $paper_clip = new NewPaperClip($paper_clip_testing_class);
        $get_file = $paper_clip->attachmentPath('preview', 'medium');

        $this->assertEquals($get_file, null);

        $paper_clip_testing_class->preview_file_name = 'test.pdf';

        $paper_clip = new NewPaperClip($paper_clip_testing_class);
        $get_file = $paper_clip->attachmentPath('preview', 'medium');

        $this->assertEquals($get_file, 'system/files/new_paper_clip_testing_class/preview/000/001/234/medium/test.jpg');
    }

    public function testAttachmentUrl()
    {
        Config::set('paperclip_host', 'http://api.booklet.dev');

        $paper_clip_testing_class = new NewPaperClipTestingClass();
        $paper_clip_testing_class->id = 1234;
        $paper_clip_testing_class->preview_file_name = 'test.pdf';

        $paper_clip = new NewPaperClip($paper_clip_testing_class);

        $this->assertEquals($paper_clip->attachmentUrl('preview'), 'http://api.booklet.dev/system/files/new_paper_clip_testing_class/preview/000/001/234/original/test.pdf');
        $this->assertEquals($paper_clip->attachmentUrl('preview', 'medium'), 'http://api.booklet.dev/system/files/new_paper_clip_testing_class/preview/000/001/234/medium/test.jpg');
    }

    public function testAttachmentName()
    {
        $paper_clip_testing_class = new NewPaperClipTestingClass();
        $paper_clip_testing_class->id = 1234;
        $paper_clip_testing_class->preview_file_name = 'test.pdf';

        $paper_clip = new NewPaperClip($paper_clip_testing_class);

        $this->assertEquals($paper_clip->attachmentName('preview'), 'test.pdf');
        $this->assertEquals($paper_clip->attachmentName('preview', 'medium'), 'test.jpg');
    }

    public function testAttachmentPaths()
    {
        $paper_clip_testing_class = new NewPaperClipTestingClass();
        $paper_clip_testing_class->id = 1234;
        $paper_clip_testing_class->preview_file_name = 'test.pdf';

        $paper_clip = new NewPaperClip($paper_clip_testing_class);

        $expect_results = [
            'original' => 'system/files/new_paper_clip_testing_class/preview/000/001/234/original/test.pdf',
            'medium' => 'system/files/new_paper_clip_testing_class/preview/000/001/234/medium/test.jpg',
            'thumbnail' => 'system/files/new_paper_clip_testing_class/preview/000/001/234/thumbnail/test.jpg',
        ];

        $this->assertEquals($paper_clip->attachmentPaths('preview'), $expect_results);
    }

    public function testAttachmentUrls()
    {
        Config::set('paperclip_host', 'http://api.booklet.dev');

        $paper_clip_testing_class = new NewPaperClipTestingClass();
        $paper_clip_testing_class->id = 1234;
        $paper_clip_testing_class->preview_file_name = 'test.pdf';

        $paper_clip = new NewPaperClip($paper_clip_testing_class);

        $expect_results = [
            'original' => 'http://api.booklet.dev/system/files/new_paper_clip_testing_class/preview/000/001/234/original/test.pdf',
            'medium' => 'http://api.booklet.dev/system/files/new_paper_clip_testing_class/preview/000/001/234/medium/test.jpg',
            'thumbnail' => 'http://api.booklet.dev/system/files/new_paper_clip_testing_class/preview/000/001/234/thumbnail/test.jpg',
        ];

        $this->assertEquals($paper_clip->attachmentUrls('preview'), $expect_results);
    }
}
