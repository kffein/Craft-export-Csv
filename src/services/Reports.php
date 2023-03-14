<?php
/**
 * craft-export-csv plugin for Craft CMS 3.x
 *
 * Description
 *
 * @link      http://kffein.com
 * @copyright Copyright (c) 2018 KFFEIN
 */

namespace kffein\craftexportcsv\services;

use kffein\craftexportcsv\CraftExportCsv;
use kffein\craftexportcsv\jobs\CsvRowsJob;
use \Datetime;
use Craft;
use craft\base\Component;
use craft\elements\Entry;

/**
 * Reports Service
 *
 * @author    KFFEIN
 * @package   CraftExportCsv
 * @since     1.0.1
 */
class Reports extends Component
{
    private $_entryFields;
    // Public Methods
    // =========================================================================

    /**
     * Get all entries not exported yet
     *
     * @return Array
     */
    public function getActiveEntriesForSection($sectionHandle, $limit = null)
    {
        return Entry::find()
            ->section($sectionHandle)
            ->status(Entry::STATUS_ENABLED)
            ->limit($limit)
            ->all();
    }

    /**
     * Return the filename
     *
     * @return string
     */
    public function getCsvFilename($exportSettings)
    {
        $now = new DateTime();

        $filename = $exportSettings['filename'];
        $filename = $filename = preg_replace('/{timestamp}/', $now->getTimestamp(), $filename);
        $filename = $filename = preg_replace('/{Y}/', $now->format('Y'), $filename);
        $filename = $filename = preg_replace('/{m}/', $now->format('m'), $filename);
        $filename = $filename = preg_replace('/{d}/', $now->format('d'), $filename);
        $filename = $filename = preg_replace('/{H}/', $now->format('H'), $filename);
        $filename = $filename = preg_replace('/{i}/', $now->format('i'), $filename);
        if ($exportSettings['sectionHandle']) {
            $filename = preg_replace('/{section-handle}/', $exportSettings['sectionHandle'], $filename);
        }

        return $filename;
    }

    /**
     * Get all fields for section handle (empty array if not section defined)
     *
     * @return array
     */
    public function getAllFields($sectionHandle)
    {
        $fields = [];

        $section = Craft::$app->getSections()->getSectionByHandle($sectionHandle);
        if (!$section) {
            return $fields;
        }

        $sectionFields = $section->getEntryTypes()[0]->getFieldLayout()->getCustomFields();

        foreach ($sectionFields as $field) {
            $fields[$field->handle] = $field->name;
        }

        return $fields;
    }

    /**
     * Generate array for csv
     *
     * @var array
     */
    public function generateCsvLines($export)
    {
        // Delete old file if exist
        $this->deleteOldReport($export);

        // Generate temporary filename
        $export = $this->setExportTemporaryName($export);

        // Get the number of rows the job should be divided
        $numberOfRows = $export['numberOfRows'] ? $export['numberOfRows'] : 100;

        // Get all id of all the entries that we want to export
        $entriesId = $this->getActiveEntriesId($export['sectionHandle'], null, $export['entryStatus']);

        // Overwrite file with just the header before adding rows
        $this->writeHeader($export['fields'], $export['lastSavedFilename']);

        // Dividing the jobs
        $numberOfJobs = floor(count($entriesId) / $numberOfRows);

        $rowStart = 0;
        for ($i = 0; $i <= $numberOfJobs; $i++) {
            // Loop parameters
            $last = false;
            $idsChunk = [];

            // only get the ids up to the number of rows limit
            for ($j = $rowStart; $j < ($rowStart + $numberOfRows); $j++) {
                if (isset($entriesId[$j])) {
                    $idsChunk[] = $entriesId[$j];
                }
            }

            // The job need to know if it's the last one
            if ($i >= $numberOfJobs) {
                $last = true;
            }

            // Pushing a new job in queue
            Craft::$app->queue->push(new CsvRowsJob([
                'entriesId' => $idsChunk,
                'export' => $export,
                'last' => $last,
            ]));
            $rowStart += $numberOfRows;
        }
    }

    /**
     * Return concatened string with categories name
     *
     * @return string
     */
    public function getTitles($entries)
    {
        $titles = [];

        foreach ($entries as $entry) {
            $titles[] = $entry->title;
        }

        return implode(', ', $titles);
    }

    /**
     * Return field types for select options
     *
     * @return array
     */
    public function getFieldTypeOptions()
    {
        return [
            [
                'label' => Craft::t('craft-export-csv', 'field-type-' . CraftExportCsv::FIELD_TYPE_HANDLE),
                'value' => CraftExportCsv::FIELD_TYPE_HANDLE,
            ],
            [
                'label' => Craft::t('craft-export-csv', 'field-type-' . CraftExportCsv::FIELD_TYPE_CONCAT_HANDLE),
                'value' => CraftExportCsv::FIELD_TYPE_CONCAT_HANDLE,
            ],
            // [
            //     'label' => Craft::t('craft-export-csv', 'field-type-' . CraftExportCsv::FIELD_TYPE_CUSTOM_QUERY),
            //     'value' => CraftExportCsv::FIELD_TYPE_CUSTOM_QUERY,
            // ]
        ];
    }

    /**
     * Replace field handle text with the field value
     *
     * @param string $string
     * @param string $section
     * @return string
     */
    public function replaceFieldsHandle($string, $section)
    {
        $formattedString = $string;

        foreach (array_keys($this->_entryFields) as $handle) {
            $formattedString = preg_replace(sprintf('/{%s}/', $handle), $section->{$handle}, $formattedString);
        }

        return $formattedString;
    }

    /**
     * Return array of ids
     *
     * @param string $sectionHandle
     * @param int $limit
     * @return array
     */
    public function getActiveEntriesId($sectionHandle, $limit = null, array $status)
    {
        return Entry::find()
            ->section($sectionHandle)
            ->status($status)
            ->limit($limit)
            ->ids();
    }

    /**
     * Return an array of entries
     *
     * @param array $ids
     * @return array
     */
    public function getEntriesById($ids)
    {
        return Entry::find()
            ->id($ids)
            ->status(null)
            ->limit(null)
            ->all();
    }

    /**
     * Set this class properties of fields to be used by other function
     *
     * @param string @sectionHandle
     * @return void
     */
    public function setEntryFields($sectionHandle)
    {
        $this->_entryFields = $this->getAllFields($sectionHandle);
    }

    /**
     * Write the head of all the field in a csv file
     *
     * @param array $fields
     * @param string $filename
     * @return void
     */
    public function writeHeader($fields, $filename)
    {
        $folder = CRAFT_BASE_PATH . '/storage/reports/';
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        $exportFile = fopen($folder . $filename, 'w', true);

        $headers = [];
        foreach ($fields as $field) {
            $headers[] = $field['name'];
        }
        fputs($exportFile, "\xEF\xBB\xBF"); // UTF-8 BOM !!!!!
        fputcsv($exportFile, $headers);

        fclose($exportFile);
    }

    /**
     * Convert the filename and save it in the exports settings. It need to be returned to use the latest config saved.
     *
     * @param array $generatedExport
     * @return array
     */
    public function setExportTemporaryName($generatedExport)
    {
        $plugin = CraftExportCsv::$plugin;
        $settings = $plugin->getSettings();
        foreach ($settings->exports as $key => $export) {
            // Find the same export settings and add a last saved name informations to
            // keep track of the file on the server.
            if ($export['id'] == $generatedExport['id']) {
                $settings->exports[$key]['lastSavedFilename'] = $this->getCsvFilename($generatedExport);
                if (!Craft::$app->getPlugins()->savePluginSettings($plugin, $settings->exports)) {
                    Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t save plugin settings.'));
                }
                return $settings->exports[$key];
            }
        }
        return $generatedExport;
    }

    /**
     * Delete file based on lastSavedFilename before updating the generated file.
     *
     * @param array $generatedExport
     * @return void
     */
    public function deleteOldReport($generatedExport)
    {
        $filepath = CRAFT_BASE_PATH . '/storage/reports/' . $generatedExport['lastSavedFilename'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}
