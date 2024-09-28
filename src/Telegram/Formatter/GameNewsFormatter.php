<?php

declare(strict_types=1);

namespace DeadlockHub\Telegram\Formatter;

use DeadlockHub\Entity\Enum\GameNewsType;

readonly class GameNewsFormatter
{
    public function __construct(
    ) {
    }

    public function format(GameNewsType $type, string $message): string
    {
        return self::cleanupMessage($message);
    }

    private static function cleanupMessage(string $message): string
    {
        $message = self::createValidDomDocument($message);

        $message = strip_tags($message, '<i><b>');

        return trim($message, "\n\r\t\v\0");
    }

    private static function createValidDomDocument(string $message): string
    {
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument(encoding: 'UTF-8');
        $dom->loadHTML(mb_encode_numericentity($message, [0x80, 0x10FFFF, 0, ~0], 'UTF-8'), LIBXML_HTML_NODEFDTD);

        $html = $dom->saveHTML($dom->documentElement);

        if ($html === false) {
            throw new \RuntimeException("Cannot save HTML document.");
        }

        return $html;
    }
}
