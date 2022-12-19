<?php
/**
 * craft-export-csv plugin for Craft CMS 3.x
 *
 * Description
 *
 * @link      http://kffein.com
 * @copyright Copyright (c) 2018 KFFEIN
 */

/**
 * craft-export-csv en Translation
 *
 * Returns an array with the string to be translated (as passed to `Craft::t('craft-export-csv', '...')`) as
 * the key, and the translation as the value.
 *
 * http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html
 *
 * @author    KFFEIN
 * @package   CraftExportCsv
 * @since     1.0.1
 */
return [
    // Nav
    'reports-label' => 'Report',
    'settings' => 'Settings',

    // Form
    'numberOfRows-label' => 'Number of rows',
    'numberOfRows-instructions' => 'Enter the number of rows limit for each query',
    'section-handle-label' => 'Entries to export',
    'sites-handle-label' => 'Sites',
    'entryStatus-handle-label' => 'Entries status',
    'expireEntries-label' => 'Expire exported entries',
    'expireEntries-instructions' => 'Choose whether to set the status of entries to expired once they\'ve been exported.',
    'section-handle-instructions' => 'Choose the entry type you want to export.',
    'name-label' => 'Name of the export',
    'name-instructions' => 'Choose a descriptive name for the export',
    'filename-label' => 'File Name',
    'filename-instructions' => 'Keys available: {batch}, {timestamp}, {Y}, {d}, {m}, {H}, {i}, {section-handle}',
    'fields-label' => 'Columns',
    'fields-instructions' => 'Describe columns to export.',
    'field-name' => 'Column Title',
    'field-type' => 'Type',
    'field-type-handle' => 'Handle',
    'field-type-concat-handle' => 'Concat Handle',
    'field-type-custom-query' => 'Custom Query ! Be very careful, can crash the export !',
    'field-value' => 'Value',

    // Reports
    'generate-heading' => 'Generate export for section: {sectionName}',
    'generate' => 'Generate csv file {filename}',
    'download' => 'Download csv file {filename}',
    'no-reports' => 'No report has been configured.',
    'configure-report' => 'Configure a report',
    'no-result' => 'No entry to download.',
    'reports-list' => 'List of reports',
];
