<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor;

use DynamicOOOS\Spatie\CalendarLinks\Link;
if (!\defined('ABSPATH')) {
    exit;
}
/**
 * ICS generator with RFC 5545 line folding and UTC timestamps.
 *
 * Two fixes over the Spatie generator:
 *
 * 1. Line folding (RFC 5545 section 3.1): content lines longer than 75 octets
 *    are folded with CRLF followed by a space; some clients (e.g. Microsoft
 *    Teams) reject unfolded files.
 *
 * 2. UTC timestamps: the parent writes DTSTART;TZID=<zone>:<local time> but
 *    never emits a matching VTIMEZONE component, which Microsoft Outlook and
 *    Teams reject. Timed events are written as UTC instants (…Z), which are
 *    self-contained and are exactly what the Google, Yahoo and Outlook.com
 *    generators of this same library already produce; calendar apps convert
 *    them back to the viewer's timezone.
 */
class Rfc5545IcsGenerator extends \DynamicOOOS\Spatie\CalendarLinks\Generators\Ics
{
    const MAX_LINE_OCTETS = 75;
    /**
     * @param Link $link
     * @return string
     */
    public function generate(Link $link) : string
    {
        $ics = ['BEGIN:VCALENDAR', 'VERSION:2.0', 'BEGIN:VEVENT', 'UID:' . ($this->options['UID'] ?? $this->generateEventUid($link)), 'SUMMARY:' . $this->escapeString($link->title)];
        if ($link->allDay) {
            $ics[] = 'DTSTART:' . $link->from->format($this->dateFormat);
            $ics[] = 'DURATION:P' . \max(1, $link->from->diff($link->to)->days) . 'D';
        } else {
            $ics[] = 'DTSTART:' . self::to_utc_stamp($link->from);
            $ics[] = 'DTEND:' . self::to_utc_stamp($link->to);
        }
        if ($link->description) {
            $ics[] = 'DESCRIPTION:' . $this->escapeString($link->description);
        }
        if ($link->address) {
            $ics[] = 'LOCATION:' . $this->escapeString($link->address);
        }
        if (isset($this->options['URL'])) {
            $ics[] = 'URL;VALUE=URI:' . $this->options['URL'];
        }
        $ics[] = 'END:VEVENT';
        $ics[] = 'END:VCALENDAR';
        return $this->buildLink($ics);
    }
    /**
     * Format a date as a UTC iCalendar timestamp (YYYYMMDDTHHMMSSZ).
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    private static function to_utc_stamp(\DateTimeInterface $date)
    {
        // Building from the absolute timestamp yields a UTC instant regardless of
        // the source timezone, and only uses getTimestamp(), which is available on
        // DateTimeInterface (setTimezone() is not).
        $utc = new \DateTimeImmutable('@' . $date->getTimestamp());
        // The date and time parts are assembled without a backslash-escaped format
        // string (e.g. 'Ymd\THis\Z'): PHP-Scoper mistakes such strings for a
        // namespaced class name and prefixes them, which would corrupt the output.
        return $utc->format('Ymd') . 'T' . $utc->format('His') . 'Z';
    }
    /**
     * @param string[] $propertiesAndComponents
     * @return string
     */
    protected function buildLink(array $propertiesAndComponents) : string
    {
        $folded = [];
        foreach ($propertiesAndComponents as $line) {
            $folded[] = self::fold_line($line);
        }
        // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- data URI, same as the parent generator.
        return 'data:text/calendar;charset=utf8;base64,' . \base64_encode(\implode("\r\n", $folded));
    }
    /**
     * Fold a content line to a maximum of 75 octets per RFC 5545 section 3.1.
     * Continuation lines start with a single space, which counts towards
     * their 75 octets. Multibyte UTF-8 characters are never split.
     *
     * @param string $line
     * @return string
     */
    public static function fold_line($line)
    {
        if (\strlen($line) <= self::MAX_LINE_OCTETS) {
            return $line;
        }
        $parts = [];
        $limit = self::MAX_LINE_OCTETS;
        $length = \strlen($line);
        while ($length > $limit) {
            $cut = $limit;
            // Do not split in the middle of a UTF-8 multibyte sequence:
            // continuation bytes match 0b10xxxxxx.
            while ($cut > 0 && (\ord($line[$cut]) & 0xc0) === 0x80) {
                --$cut;
            }
            $parts[] = \substr($line, 0, $cut);
            $line = ' ' . \substr($line, $cut, $length - $cut);
            $length = \strlen($line);
        }
        $parts[] = $line;
        return \implode("\r\n", $parts);
    }
}
