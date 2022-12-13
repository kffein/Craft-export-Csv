<?php

/**
 * Job that serve to divide entries into group to be exported and compile together in one Csv.
 *
 * @author    Pierre-Luc Laurin
 * @package   CraftExportCsv
 * @since     1.0.1
 */

namespace kffein\craftexportcsv\jobs;

use kffein\craftexportcsv\CraftExportCsv;
use \Datetime;
use craft\queue\BaseJob;
use craft\elements\Entry;
use craft\elements\db\CategoryQuery;
use craft\elements\db\EntryQuery;
use Craft;

class CsvRowsJob extends BaseJob
{
    // Public properties
    ////////////////////////////////////////////////////

    /**
     * @var array
     */
    public $entriesId;

    /**
     * @var array
     */
    public $export;

    /**
     * @var bool
     */
    public $last;

    /**
     * @inheritdoc
     */
    public function execute($queue): void
    {
        // Open the file with append as parameters
        $folder = CRAFT_BASE_PATH . '/storage/reports/';
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        $exportFile = fopen($folder . $this->export['lastSavedFilename'], 'a');

        $optionSiteId = isset($this->export['siteId']) ? $this->export['siteId'] : null;
        $requestSite = Craft::$app->sites->getSiteById((int) $optionSiteId) !== null
            ? Craft::$app->sites->getSiteById((int) $optionSiteId)->id
            : Craft::$app->sites->primarySite->id;

        Craft::$app->sites->setCurrentSite($requestSite);

        // Prepare the properties for the loop
        $lines = [];
        $jobDone = 0;

        // Get all the fields from entries once
        CraftExportCsv::getInstance()->reportsService->setEntryFields($this->export['sectionHandle']);

        // Get all entries from the array of ids
        $entries = CraftExportCsv::getInstance()->reportsService->getEntriesById($this->entriesId);

        foreach ($entries as $entry) {
            // Is the entry empty?
            if (empty($entry)) {
                continue;
            }

            $entryData = [];
            foreach ($this->export['fields'] as $field) {
                // Add a row of data if it's one field or multiple.
                switch ($field['type']) {
                    case CraftExportCsv::FIELD_TYPE_CONCAT_HANDLE:
                        $entryData[] = CraftExportCsv::getInstance()->reportsService->replaceFieldsHandle($field['value'], $entry, $this->export['sectionHandle']);
                        break;
                    case CraftExportCsv::FIELD_TYPE_HANDLE:
                    default:
                        // Some field data contains different object.
                        if (!$field['value']) {
                          $entryData[] = '';
                        } elseif ($entry->{$field['value']} instanceof CategoryQuery || $entry->{$field['value']} instanceof EntryQuery) {
                            $entryData[] = CraftExportCsv::getInstance()->reportsService->getTitles($entry->{$field['value']}->all());
                        } elseif (is_object($entry->{$field['value']}) && get_class($entry->{$field['value']}) === 'craft\elements\db\AssetQuery') {
                            $entryData[] = $entry->{$field['value']}->one() !== null ? $entry->{$field['value']}->one()->url : null;
                        } elseif ($entry->{$field['value']} instanceof DateTime) {
                            $entryData[] = $entry->{$field['value']}->format('Y-m-d H:i:s');
                        } elseif (is_array($entry->{$field['value']}) || is_object($entry->{$field['value']})) {
                            $entryData[] = json_encode($entry->{$field['value']});
                        } else {
                            $entryData[] = $entry->{$field['value']};
                        }
                }
            }
            // One entry is done so we make the bar progress
            $jobDone++;
            $this->setProgress($queue, ($jobDone / count($this->entriesId)));

            // Add all the info array as one line
            $lines[] = $entryData;
        }

        //throw new \Exception($lines);
        // Loop all the lines to insert in the csv file
        // fputs($exportFile, "\xEF\xBB\xBF"); // UTF-8 BOM !!!!!
        foreach ($lines as $line) {
            fputcsv($exportFile, $line);
        }

        // Closing the file
        fclose($exportFile);

        // If it's the last job in the queue, we update the export date in the settings
        if ($this->last) {
            CraftExportCsv::getInstance()->exportsService->updateExportDate($this->export['id']);
        }

        // Job done
        $this->setProgress($queue, 1);
    }

    protected function defaultDescription(): ?string
    {
        return 'Exporting Csv';
    }
}
