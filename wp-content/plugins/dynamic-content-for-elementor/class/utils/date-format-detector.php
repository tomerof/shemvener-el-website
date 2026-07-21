<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Utils;

if (!\defined('ABSPATH')) {
    exit;
}
/**
 * Date Format Detector
 *
 * Autodetects PHP date format from an array of date string samples.
 * Uses regex pattern matching, strict validation, and multi-sample
 * disambiguation to handle ambiguous formats like d/m/Y vs m/d/Y.
 */
class DateFormatDetector
{
    /**
     * Format patterns with regex and PHP format strings
     *
     * Each pattern has:
     * - regex: Pattern to match the date string structure
     * - formats: Array of possible PHP date formats (first = preferred)
     * - ambiguous: Whether this pattern could match multiple formats (d/m vs m/d)
     *
     * @var array<string,array{regex:string,formats:string[],ambiguous:bool,description:string}>
     */
    private const FORMAT_PATTERNS = [
        // Compact formats (no separators)
        'compact_datetime' => ['regex' => '/^\\d{14}$/', 'formats' => ['YmdHis'], 'ambiguous' => \false, 'description' => 'Compact DateTime'],
        'compact_date' => ['regex' => '/^\\d{8}$/', 'formats' => ['Ymd'], 'ambiguous' => \false, 'description' => 'ACF Date'],
        // ISO formats (Year first - unambiguous)
        'iso_datetime_dash' => ['regex' => '/^\\d{4}-\\d{2}-\\d{2} \\d{2}:\\d{2}:\\d{2}$/', 'formats' => ['Y-m-d H:i:s'], 'ambiguous' => \false, 'description' => 'ISO DateTime'],
        'iso_datetime_dash_t' => ['regex' => '/^\\d{4}-\\d{2}-\\d{2}T\\d{2}:\\d{2}:\\d{2}$/', 'formats' => ['Y-m-d\\TH:i:s'], 'ambiguous' => \false, 'description' => 'ISO 8601 DateTime'],
        'iso_datetime_dash_minutes' => ['regex' => '/^\\d{4}-\\d{2}-\\d{2} \\d{2}:\\d{2}$/', 'formats' => ['Y-m-d H:i'], 'ambiguous' => \false, 'description' => 'ISO DateTime (no seconds)'],
        'iso_date_dash' => ['regex' => '/^\\d{4}-\\d{2}-\\d{2}$/', 'formats' => ['Y-m-d'], 'ambiguous' => \false, 'description' => 'ISO Date'],
        'iso_datetime_slash' => ['regex' => '/^\\d{4}\\/\\d{2}\\/\\d{2} \\d{2}:\\d{2}:\\d{2}$/', 'formats' => ['Y/m/d H:i:s'], 'ambiguous' => \false, 'description' => 'ISO DateTime (slashes)'],
        'iso_date_slash' => ['regex' => '/^\\d{4}\\/\\d{2}\\/\\d{2}$/', 'formats' => ['Y/m/d'], 'ambiguous' => \false, 'description' => 'ISO Date (slashes)'],
        // Ambiguous formats (day/month position unclear)
        'ambiguous_datetime_slash' => ['regex' => '/^\\d{1,2}\\/\\d{1,2}\\/\\d{4} \\d{2}:\\d{2}:\\d{2}$/', 'formats' => ['d/m/Y H:i:s', 'm/d/Y H:i:s'], 'ambiguous' => \true, 'description' => 'DateTime (slashes)'],
        'ambiguous_datetime_slash_minutes' => ['regex' => '/^\\d{1,2}\\/\\d{1,2}\\/\\d{4} \\d{2}:\\d{2}$/', 'formats' => ['d/m/Y H:i', 'm/d/Y H:i'], 'ambiguous' => \true, 'description' => 'DateTime (slashes, no seconds)'],
        'ambiguous_date_slash' => ['regex' => '/^\\d{1,2}\\/\\d{1,2}\\/\\d{4}$/', 'formats' => ['d/m/Y', 'm/d/Y'], 'ambiguous' => \true, 'description' => 'Date (slashes)'],
        'ambiguous_datetime_dash' => ['regex' => '/^\\d{1,2}-\\d{1,2}-\\d{4} \\d{2}:\\d{2}:\\d{2}$/', 'formats' => ['d-m-Y H:i:s', 'm-d-Y H:i:s'], 'ambiguous' => \true, 'description' => 'DateTime (dashes)'],
        'ambiguous_date_dash' => ['regex' => '/^\\d{1,2}-\\d{1,2}-\\d{4}$/', 'formats' => ['d-m-Y', 'm-d-Y'], 'ambiguous' => \true, 'description' => 'Date (dashes)'],
        'ambiguous_date_dot' => ['regex' => '/^\\d{1,2}\\.\\d{1,2}\\.\\d{4}$/', 'formats' => ['d.m.Y', 'm.d.Y'], 'ambiguous' => \true, 'description' => 'Date (dots)'],
        // 2-digit year ambiguous formats
        'ambiguous_date_slash_2y' => ['regex' => '/^\\d{1,2}\\/\\d{1,2}\\/\\d{2}$/', 'formats' => ['d/m/y', 'm/d/y'], 'ambiguous' => \true, 'description' => 'Date (slashes, 2-digit year)'],
        'ambiguous_date_dash_2y' => ['regex' => '/^\\d{1,2}-\\d{1,2}-\\d{2}$/', 'formats' => ['d-m-y', 'm-d-y'], 'ambiguous' => \true, 'description' => 'Date (dashes, 2-digit year)'],
    ];
    /**
     * Descriptions for formats
     *
     * @var array<string,string>
     */
    private const FORMAT_DESCRIPTIONS = ['Ymd' => 'ACF Date (Ymd)', 'YmdHis' => 'Compact DateTime (YmdHis)', 'Y-m-d' => 'ISO Date (Y-m-d)', 'Y-m-d H:i:s' => 'ISO DateTime (Y-m-d H:i:s)', 'Y-m-d H:i' => 'ISO DateTime (Y-m-d H:i)', 'Y-m-d\\TH:i:s' => 'ISO 8601 DateTime', 'Y/m/d' => 'ISO Date (Y/m/d)', 'Y/m/d H:i:s' => 'ISO DateTime (Y/m/d H:i:s)', 'd/m/Y' => 'European Date (d/m/Y)', 'd/m/Y H:i' => 'European DateTime (d/m/Y H:i)', 'd/m/Y H:i:s' => 'European DateTime (d/m/Y H:i:s)', 'm/d/Y' => 'US Date (m/d/Y)', 'm/d/Y H:i' => 'US DateTime (m/d/Y H:i)', 'm/d/Y H:i:s' => 'US DateTime (m/d/Y H:i:s)', 'd-m-Y' => 'European Date (d-m-Y)', 'd-m-Y H:i:s' => 'European DateTime (d-m-Y H:i:s)', 'm-d-Y' => 'US Date (m-d-Y)', 'm-d-Y H:i:s' => 'US DateTime (m-d-Y H:i:s)', 'd.m.Y' => 'European Date (d.m.Y)', 'm.d.Y' => 'US Date (m.d.Y)', 'd/m/y' => 'European Date 2-digit year (d/m/y)', 'm/d/y' => 'US Date 2-digit year (m/d/y)', 'd-m-y' => 'European Date 2-digit year (d-m-y)', 'm-d-y' => 'US Date 2-digit year (m-d-y)', 'timestamp' => 'Unix Timestamp'];
    /**
     * Detect the date format from an array of sample strings
     *
     * @param array<int,mixed> $samples Array of date strings to analyze (non-strings are filtered out)
     * @return array{success:true,format:string,description:string,alternatives:array<int,string>,sample:string,readable:string}|array{success:false,message:string,sample?:string}
     */
    public function detect(array $samples) : array
    {
        /** @var array<int,string> $samples Filtered to contain only non-empty strings */
        $samples = \array_values(\array_map('trim', \array_filter($samples, function ($s) {
            return \is_string($s) && \trim($s) !== '';
        })));
        if (empty($samples)) {
            return ['success' => \false, 'message' => 'No valid samples provided'];
        }
        // Use first sample to detect pattern
        $first_sample = \reset($samples);
        // Check for timestamp first
        if ($this->is_timestamp($first_sample)) {
            $d = new \DateTime("@{$first_sample}");
            return ['success' => \true, 'format' => 'timestamp', 'description' => self::FORMAT_DESCRIPTIONS['timestamp'], 'sample' => $first_sample, 'readable' => $d->format('l, F j, Y g:i A'), 'alternatives' => []];
        }
        // Find matching pattern
        $matched_pattern = $this->find_matching_pattern($first_sample);
        if ($matched_pattern === null) {
            return ['success' => \false, 'message' => 'Could not detect format', 'sample' => $first_sample];
        }
        $pattern_config = self::FORMAT_PATTERNS[$matched_pattern];
        $candidate_formats = $pattern_config['formats'];
        // If not ambiguous, validate and return the single format
        if (!$pattern_config['ambiguous']) {
            $format = $candidate_formats[0];
            if ($this->validate_format($first_sample, $format)) {
                $d = \DateTime::createFromFormat($format, $first_sample);
                return ['success' => \true, 'format' => $format, 'description' => $this->get_format_description($format), 'sample' => $first_sample, 'readable' => $d ? $d->format('l, F j, Y g:i A') : '', 'alternatives' => []];
            }
            return ['success' => \false, 'message' => 'Format validation failed', 'sample' => $first_sample];
        }
        // Ambiguous format: use multi-sample disambiguation
        $valid_formats = $this->disambiguate_formats($samples, $candidate_formats);
        if (empty($valid_formats)) {
            return ['success' => \false, 'message' => 'Could not disambiguate format', 'sample' => $first_sample];
        }
        // Return the first valid format (d/m/Y preferred over m/d/Y by default in patterns)
        $best_format = $valid_formats[0];
        $d = \DateTime::createFromFormat($best_format, $first_sample);
        // Get alternatives (excluding the best one)
        $alternatives = \array_slice($valid_formats, 1);
        return ['success' => \true, 'format' => $best_format, 'description' => $this->get_format_description($best_format), 'sample' => $first_sample, 'readable' => $d ? $d->format('l, F j, Y g:i A') : '', 'alternatives' => $alternatives];
    }
    /**
     * Check if the value is a Unix timestamp
     *
     * @param string $value The value to check
     * @return bool
     */
    private function is_timestamp(string $value) : bool
    {
        // Must be numeric
        if (!\is_numeric($value)) {
            return \false;
        }
        $num = (int) $value;
        // Reasonable timestamp range: year 2000 to year 2100
        // 946684800 = 2000-01-01 00:00:00
        // 4102444800 = 2100-01-01 00:00:00
        return $num > 946684800 && $num < 4102444800;
    }
    /**
     * Find which pattern matches the sample string
     *
     * @param string $sample The date string to match
     * @return string|null Pattern key or null if no match
     */
    private function find_matching_pattern(string $sample) : ?string
    {
        foreach (self::FORMAT_PATTERNS as $key => $config) {
            if (\preg_match($config['regex'], $sample)) {
                return $key;
            }
        }
        return null;
    }
    /**
     * Validate a date string against a specific format
     *
     * Uses strict validation: the parsed date must format back to the exact same string
     *
     * @param string $sample The date string
     * @param string $format The PHP date format
     * @return bool
     */
    private function validate_format(string $sample, string $format) : bool
    {
        $d = \DateTime::createFromFormat($format, $sample);
        if (!$d) {
            return \false;
        }
        // Strict validation: must round-trip exactly
        return $d->format($format) === $sample;
    }
    /**
     * Disambiguate between candidate formats using multiple samples
     *
     * For formats like d/m/Y vs m/d/Y, if any sample has the first number > 12,
     * we can eliminate m/d/Y (since months can't be > 12)
     *
     * @param array<int,string>|object $samples Array of date strings or object(???)
     * @param array<int,string> $candidate_formats Possible formats to test
     * @return array<int,string> Formats that are valid for ALL samples
     */
    private function disambiguate_formats($samples, array $candidate_formats) : array
    {
        if (!\is_array($samples)) {
            return [];
        }
        $valid_formats = [];
        foreach ($candidate_formats as $format) {
            $is_valid_for_all = \true;
            foreach ($samples as $sample) {
                if (!$this->validate_format($sample, $format)) {
                    $is_valid_for_all = \false;
                    break;
                }
            }
            if ($is_valid_for_all) {
                $valid_formats[] = $format;
            }
        }
        return $valid_formats;
    }
    /**
     * Get human-readable description for a format
     *
     * @param string $format The PHP date format
     * @return string
     */
    private function get_format_description(string $format) : string
    {
        return self::FORMAT_DESCRIPTIONS[$format] ?? $format;
    }
}
