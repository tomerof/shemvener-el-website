<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\JsLocalization;

if (!\defined('ABSPATH')) {
    exit;
}
/**
 * Custom Editor Localized Strings
 *
 * Provides translatable strings for the Custom Editor JavaScript components.
 */
class CustomEditor extends \DynamicContentForElementor\JsLocalization\Base
{
    /** @var string */
    protected static string $script_handle = 'dce-custom-editor-app';
    /** @var string */
    protected static string $js_object_name = 'dceCustomEditorStrings';
    /**
     * Get all localized strings for the custom editor
     *
     * @return array<string,mixed>
     */
    protected static function get_strings() : array
    {
        return [
            // Dialog labels
            'detectedFormat' => __('Detected format:', 'dynamic-content-for-elementor'),
            'alternativeFormats' => __('Also possible:', 'dynamic-content-for-elementor'),
            'isFormatCorrect' => __('Is this format correct?', 'dynamic-content-for-elementor'),
            'analyzed' => __('analyzed', 'dynamic-content-for-elementor'),
            'samples' => __('samples', 'dynamic-content-for-elementor'),
            // Dialog header
            'dialogHeader' => __('Auto-Detect Date Format', 'dynamic-content-for-elementor'),
            // Dialog buttons
            'yesApply' => __('Yes, apply it', 'dynamic-content-for-elementor'),
            'noManual' => __("No, I'll enter manually", 'dynamic-content-for-elementor'),
            // Toast messages
            'selectMetaFirst' => __('Please select a meta field first', 'dynamic-content-for-elementor'),
            'analyzingFormat' => __('Analyzing date format...', 'dynamic-content-for-elementor'),
            'formatApplied' => __('Format "%s" applied automatically', 'dynamic-content-for-elementor'),
            'enterManually' => __('Please enter format manually', 'dynamic-content-for-elementor'),
            'couldNotDetect' => __('Could not auto-detect date format.', 'dynamic-content-for-elementor'),
            'couldNotDetectFor' => __('Could not detect format for value:', 'dynamic-content-for-elementor'),
            'pleaseEnterManually' => __('Please enter manually.', 'dynamic-content-for-elementor'),
            // FileBrowser custom titles
            'fileBrowser' => ['modalHeader' => __('Manage Custom Titles', 'dynamic-content-for-elementor'), 'foldersHeader' => __('Folders', 'dynamic-content-for-elementor'), 'filesHeader' => __('Files', 'dynamic-content-for-elementor'), 'folderPlaceholder' => __('Custom title (leave empty for default)', 'dynamic-content-for-elementor'), 'ftpPlaceholder' => __('Custom title (leave empty for filename)', 'dynamic-content-for-elementor'), 'mediaLibraryPlaceholder' => __('Custom title (leave empty for WP title)', 'dynamic-content-for-elementor'), 'mediaLibraryBadge' => __('Media Library', 'dynamic-content-for-elementor'), 'ftpBadge' => __('FTP', 'dynamic-content-for-elementor'), 'saveButton' => __('Save Titles', 'dynamic-content-for-elementor'), 'cancelButton' => __('Cancel', 'dynamic-content-for-elementor'), 'loading' => __('Loading existing titles...', 'dynamic-content-for-elementor'), 'saving' => __('Saving titles...', 'dynamic-content-for-elementor'), 'saved' => __('Titles saved successfully.', 'dynamic-content-for-elementor'), 'error' => __('Error', 'dynamic-content-for-elementor'), 'widgetNotFound' => __('Widget not found in preview. Please save and refresh.', 'dynamic-content-for-elementor'), 'noItems' => __('No files or folders found in the widget.', 'dynamic-content-for-elementor')],
        ];
    }
}
