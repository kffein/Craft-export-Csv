<?php
/**
 * craft-export-csv plugin for Craft CMS 3.x
 *
 * Description
 *
 * @link      http://kffein.com
 * @copyright Copyright (c) 2018 Joel Lachance
 */

namespace kffein\craftexportcsv\services;

use kffein\craftexportcsv\CraftExportCsv;
use kffein\craftexportcsv\jobs\CsvRowsJob;

use \Datetime;
use Craft;
use craft\base\Component;
use craft\elements\Entry;
use craft\elements\db\CategoryQuery;
use craft\elements\db\EntryQuery;

/**
 * Reports Service
 *
 * @author    Joel Lachance
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
    public function getActiveEntriesForSection($sectionHandle, $limit = null) {
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
    public function getCsvFilename($exportSettings) {

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
    public function getAllFields($sectionHandle) {
        $fields = [];

        $section = Craft::$app->getSections()->getSectionByHandle($sectionHandle);
        if (! $section) {
            return $fields;
        }

        $sectionFields = $section->getEntryTypes()[0]->getFieldLayout()->getFields();
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
    public function generateCsvLines($export) {
        // Get the number of rows the job should be divided
        $numberOfRows = $export['numberOfRows'];

        // Get all id of all the entries that we want to export
        $entriesId = $this->getActiveEntriesId($export['sectionHandle']);

        // Overwrite file with just the header before adding rows
        $this->writeHeader($export['fields'],$export['filename']);

        // Dividing the jobs
        $numberOfJobs = floor(count($entriesId) / $numberOfRows);

        $rowStart = 0;
        for ($i=0; $i <= $numberOfJobs; $i++) {
            // Loop parameters
            $last = false;
            $idsChunk = [];

            // only get the ids up to the number of rows limit
            for ($j=$rowStart; $j < ($rowStart + $numberOfRows); $j++) {
                if(isset($entriesId[$j])){
                    $idsChunk[] = $entriesId[$j];
                }
            }

            // The job need to know if it's the last one
            if($i >= $numberOfJobs){
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
    public function getTitles($entries) {
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
    public function getFieldTypeOptions() {
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
     */
    public function replaceFieldsHandle($string, $section) {
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
    public function getActiveEntriesId($sectionHandle, $limit = null) {
        return Entry::find()
            ->section($sectionHandle)
            ->status(Entry::STATUS_ENABLED)
            ->limit($limit)
            ->ids();
    }

    /**
     * Return an array of entries
     * 
     * @param array $ids
     * @return array
     */
    public function getEntriesById($ids){
        return Entry::find()
            ->id($ids)
            ->status(Entry::STATUS_ENABLED)
            ->limit(null)
            ->all();
    }

    /**
     * Set this class properties of fields to be used by other function
     * 
     * @param string @sectionHandle
     */
    public function setEntryFields($sectionHandle){
        $this->_entryFields = $this->getAllFields($sectionHandle);
    }

    /**
     * Write the head of all the field in a csv file
     * 
     * @param array $fields
     * @param string $filename
     */
    public function writeHeader($fields,$filename){
        $exportFile = fopen('uploads/'.$filename, "w",true);
        $headers = [];
        foreach ($fields as $field) {
            $headers[] = $field['name'];
        }
        fputs($exportFile, "\xEF\xBB\xBF" ); // UTF-8 BOM !!!!!
        fputcsv($exportFile, $headers);

        fclose($exportFile);
    }
    
}
