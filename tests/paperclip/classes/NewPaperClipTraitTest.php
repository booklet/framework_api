<?php
include_once 'tests/fixtures/paperclip/NewPaperClipTestingClass.php';

class NewPaperClipTraitTest extends \CustomPHPUnitTestCase
{
    public $skip_database_clear_before = ['all'];

    public function testAttachmentPath()
    {
        $paper_clip_testing_class = new NewPaperClipTestingClass();
        $paper_clip_testing_class->id = 1;

        $this->assertEquals($paper_clip_testing_class->previewPath(), null);

        $paper_clip_testing_class->preview_file_name = 'file.pdf';

        $this->assertEquals($paper_clip_testing_class->previewPath(),
          'system/files/new_paper_clip_testing_class/preview/000/000/001/original/file.pdf');
        $this->assertEquals($paper_clip_testing_class->previewPath('medium'),
          'system/files/new_paper_clip_testing_class/preview/000/000/001/medium/file.jpg');
    }

    public function testAttachmentUrl()
    {
        $paper_clip_testing_class = new NewPaperClipTestingClass();
        $paper_clip_testing_class->id = 1;

        $this->assertEquals($paper_clip_testing_class->previewUrl(),
          'http://api.booklet.dev/system/files/new_paper_clip_testing_class/preview/original/missing.png');

        $paper_clip_testing_class->preview_file_name = 'file.pdf';

        $this->assertEquals($paper_clip_testing_class->previewUrl(),
          'http://api.booklet.dev/system/files/new_paper_clip_testing_class/preview/000/000/001/original/file.pdf');
        $this->assertEquals($paper_clip_testing_class->previewUrl('medium'),
          'http://api.booklet.dev/system/files/new_paper_clip_testing_class/preview/000/000/001/medium/file.jpg');
    }

    public function testAttachmentReprocess()
    {
        $this->markTestSkipped();
        // $paper_clip_testing_class->previewReprocess()
    }
}
