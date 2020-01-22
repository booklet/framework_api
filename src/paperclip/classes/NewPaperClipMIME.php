<?php
class NewPaperClipMIME
{
    const POSTSCRIPT_TYPES = [
        'application/pdf', // pdf
        'application/postscript', //ai eps ps
    ];

    const IMAGES_TYPES = [
        'image/svg+xml', // svg, svgz
        'image/gif', // gif
        'image/jpeg', // jpeg jpg jpe
        'image/png', // png
        'image/tiff', // tiff tif
        'image/bmp', // bmp
    ];

    public static function getFileType($file_path)
    {
        return mime_content_type($file_path);
    }

    public static function isAllowedMIMEType($mime_type)
    {
        return in_array($mime_type, array_merge(self::POSTSCRIPT_TYPES, self::IMAGES_TYPES));
    }

    public static function isPostScriptFileByMIME($mime_type)
    {
        return in_array($mime_type, self::POSTSCRIPT_TYPES);
    }

    public static function isImageFileByMIME($file_path)
    {
        return in_array($mime_type, self::IMAGES_TYPES);
    }
}
