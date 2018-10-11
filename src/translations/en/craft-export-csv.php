<?php
/**
 * craft-export-csv plugin for Craft CMS 3.x
 *
 * Description
 *
 * @link      http://kffein.com
 * @copyright Copyright (c) 2018 Joel Lachance
 */

/**
 * craft-export-csv en Translation
 *
 * Returns an array with the string to be translated (as passed to `Craft::t('craft-export-csv', '...')`) as
 * the key, and the translation as the value.
 *
 * http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html
 *
 * @author    Joel Lachance
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
    'section-handle-instructions' => 'Choose the entry type you want to export.',
    'filename-label' => 'File Name',
    'filename-instructions' => 'Keys available: {timestamp}, {Y}, {d}, {m}, {H}, {i}, {section-handle}',
    'fields-label' => 'Columns',
    'fields-instructions' => 'Describe columns to export.',
    'field-name' => 'Column Title',
    'field-type' => 'Type',
    'field-type-handle' => 'Handle',
    'field-type-concat-handle' => 'Concat Handle',
    'field-type-custom-query' => 'Custom Query ! Be very careful, can crash the export !',
    'field-value' => 'Value',

    // Reports
    'generate' => 'Generate file : {filename} for section : {sectionName}',
    'download' => 'Download {filename}',
    'no-reports' => 'No report has been configured.',
    'configure-report' => 'Configure a report',
    'no-result' => 'No entry to download.',
];
