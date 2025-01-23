<?php

namespace App\Constants;

class MimeTypes
{
    public const JSON = 'application/json';
    public const PDF = 'application/pdf';
    public const CSV = 'text/csv';
    public const TXT = 'text/plain';
    public const MSWORD = 'application/msword';
    public const EXCEL = 'application/vnd.ms-excel';
    public const XML = 'application/xml';
    public const XHTML = 'application/xhtml+xml';
    public const ZIP = 'application/zip';
    public const GZIP = 'application/gzip';
    public const XGZIP = 'application/x-gzip';
    public const XTAR = 'application/x-tar';
    public const GIF = 'image/gif';
    public const JPEG = 'image/jpeg';
    public const PNG = 'image/png';
    public const SVG = 'image/svg+xml';
    public const AUDIO_MPEG = 'audio/mpeg';
    public const AUDIO_OGG = 'audio/ogg';
    public const WAV = 'audio/wav';
    public const MP4 = 'video/mp4';
    public const VIDEO_MPEG = 'video/mpeg';
    public const VIDEO_OGG = 'video/ogg';

    public const IMAGE_TYPES = [
        self::GIF,
        self::JPEG,
        self::PNG,
    ];

    public const VIDEO_TYPES = [
        self::MP4,
        self::VIDEO_MPEG,
        self::VIDEO_OGG,
    ];

    public static function getConstants(): array
    {
        $reflectionClass = new \ReflectionClass(MimeTypes::class);

        return $reflectionClass->getConstants();
    }
}
