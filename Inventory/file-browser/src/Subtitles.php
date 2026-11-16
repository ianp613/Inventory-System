<?php
declare(strict_types=1);

/**
 * Turns SRT / WebVTT / ASS / SSA subtitle files into WebVTT, which is what browsers can show.
 * Handles the usual text encodings (UTF-8, UTF-8 with BOM, UTF-16, Windows-1252).
 * ASS/SSA styling (colors, fonts, positions) is dropped; the text and timing are kept.
 */
final class Subtitles
{
    public static function toVtt(string $raw, string $ext): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", self::toUtf8($raw));
        switch (strtolower($ext)) {
            case 'ass':
            case 'ssa':
                return self::fromAss($text);
            case 'vtt':
                return self::fromVtt($text);
            default:
                return self::fromSrt($text);
        }
    }

    private static function toUtf8(string $raw): string
    {
        if (strncmp($raw, "\xEF\xBB\xBF", 3) === 0) {
            return substr($raw, 3);
        }
        if (strncmp($raw, "\xFF\xFE", 2) === 0) {
            return mb_convert_encoding(substr($raw, 2), 'UTF-8', 'UTF-16LE');
        }
        if (strncmp($raw, "\xFE\xFF", 2) === 0) {
            return mb_convert_encoding(substr($raw, 2), 'UTF-8', 'UTF-16BE');
        }
        if (substr_count(substr($raw, 0, 200), "\0") > 20) {   // UTF-16 without a BOM
            return mb_convert_encoding($raw, 'UTF-8', 'UTF-16LE');
        }
        if (mb_check_encoding($raw, 'UTF-8')) {
            return $raw;
        }
        return mb_convert_encoding($raw, 'UTF-8', 'Windows-1252');
    }

    private static function stamp(string $h, string $m, string $s, string $frac): string
    {
        return sprintf('%02d:%02d:%02d.%s', (int)$h, (int)$m, (int)$s, str_pad(substr($frac, 0, 3), 3, '0', STR_PAD_RIGHT));
    }

    /** Cue text: drop {\an8}-style tags and <font>, keep <i> <b> <u>, make it safe for WebVTT. */
    private static function clean(string $p): string
    {
        $p = preg_replace('/\{\\\\[^}]*\}/', '', $p) ?? $p;
        $p = preg_replace('~</?(?:font|span)[^>]*>~i', '', $p) ?? $p;
        $p = preg_replace('/&(?!(?:amp|lt|gt|nbsp|lrm|rlm);)/i', '&amp;', $p) ?? $p;
        $p = str_replace('-->', '->', $p);
        return trim($p);
    }

    private static function fromVtt(string $text): string
    {
        $text = ltrim($text);
        return strncmp($text, 'WEBVTT', 6) === 0 ? $text : "WEBVTT\n\n" . $text;
    }

    private static function fromSrt(string $text): string
    {
        $out = "WEBVTT\n\n";
        $re = '/(\d{1,2}):(\d{2}):(\d{2})[,.](\d{1,3})\s*-->\s*(\d{1,2}):(\d{2}):(\d{2})[,.](\d{1,3})/';
        foreach (preg_split('/\n{2,}/', trim($text)) ?: [] as $block) {
            $lines = explode("\n", $block);
            $ti = null;
            foreach ($lines as $i => $l) {
                if (strpos($l, '-->') !== false) {
                    $ti = $i;
                    break;
                }
            }
            if ($ti === null || !preg_match($re, $lines[$ti], $m)) {
                continue;
            }
            $payload = self::clean(implode("\n", array_slice($lines, $ti + 1)));
            if ($payload === '') {
                continue;
            }
            $out .= self::stamp($m[1], $m[2], $m[3], $m[4]) . ' --> ' . self::stamp($m[5], $m[6], $m[7], $m[8]) . "\n" . $payload . "\n\n";
        }
        return $out;
    }

    private static function fromAss(string $text): string
    {
        $inEvents = false;
        $fmt = [];
        $cues = [];
        foreach (explode("\n", $text) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if ($line[0] === '[') {
                $inEvents = strcasecmp($line, '[Events]') === 0;
                continue;
            }
            if (!$inEvents) {
                continue;
            }
            if (stripos($line, 'Format:') === 0) {
                $fmt = array_map(static fn($x) => strtolower(trim($x)), explode(',', substr($line, 7)));
                continue;
            }
            if (stripos($line, 'Dialogue:') !== 0 || !$fmt) {
                continue;
            }
            $parts = explode(',', substr($line, 9), count($fmt));
            if (count($parts) < count($fmt)) {
                continue;
            }
            $row = array_combine($fmt, $parts);
            $re = '/^\s*(\d+):(\d{2}):(\d{2})[.](\d{1,3})\s*$/';
            if (!isset($row['start'], $row['end'], $row['text'])
                || !preg_match($re, $row['start'], $a) || !preg_match($re, $row['end'], $b)) {
                continue;
            }
            $t = $row['text'];
            if (preg_match('/\{[^}]*\\\\p[1-9]/', $t)) {
                continue; // vector drawing, not text
            }
            $t = str_replace(['\\N', '\\n', '\\h'], ["\n", "\n", ' '], $t);
            $t = self::clean($t);
            if ($t === '') {
                continue;
            }
            $cues[] = [self::stamp($a[1], $a[2], $a[3], $a[4]), self::stamp($b[1], $b[2], $b[3], $b[4]), $t];
        }
        usort($cues, static fn($x, $y) => strcmp($x[0], $y[0]));
        $out = "WEBVTT\n\n";
        foreach ($cues as [$s, $e, $t]) {
            $out .= $s . ' --> ' . $e . "\n" . $t . "\n\n";
        }
        return $out;
    }
}
