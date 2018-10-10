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

use Craft;
use craft\base\Component;

/**
 * Exports Service
 *
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    KFFEIN
 * @package   CraftExportCsv
 * @since     1.0.1
 */
class Exports extends Component
{
    // Private properties
    // =========================================================================

    /**
     * @var kffein\craftexportcsv\models\Settings
     */
    private $settings;

    /**
     * The active Plugin class
     *
     * @var Plugin
     */
    public $plugin;

    

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->settings = CraftExportCsv::$plugin->getSettings();
        $this->plugin = CraftExportCsv::$plugin;
    }

    /**
     * Return export by id
     *
     * @param string $id
     * @return kffein\craftexportcsv\models\Export
     */
    public function getExportById($id){
        // Loop all export in settings
        foreach($this->settings->exports as $export){
            if($export['id'] === $id){
                // Found one that match the id
                return $export;
            }
        }
        // Nothing found;
        return null;
    }

    /**
     * Delete from settings an export by id WITHOUT SAVING
     * @param string $id
     * @return bool
     */
    public function deleteExportById($id){
        $oneDeleted = false;
        // Loop all export and delete from settings by keys found
        foreach ($this->settings->exports as $key => $export) {
            if($export['id'] == $id){
                unset($this->settings->exports[$key]);
                $oneDeleted = true;
            }
        }
        return $oneDeleted;
    }

    /**
     * Update export date if report has been generated
     * @param string $id
     */
    public function updateExportDate($id){
        foreach ($this->settings->exports as $key => $export) {
            if($export['id'] == $id){
                $this->settings->exports[$key]['dateUpdated'] = time();
            }
        }
        Craft::$app->getPlugins()->savePluginSettings($this->plugin, $this->settings->exports);
    }

    /**
     * Return formated date
     * @param int $timestamp
     */
    public function getDateUpdatedFormated($timestamp){
        return date("F j, Y, g:i a",$timestamp);
    }

    /**
     * Set dateUpdated data of export with specified value
     * @param string $id
     * @param int $value
     */
    public function setExportDate($id,$value){
        foreach ($this->settings->exports as $key => $export) {
            if($export['id'] == $id){
                $this->settings->exports[$key]['dateUpdated'] = $value;
            }
        }
        Craft::$app->getPlugins()->savePluginSettings($this->plugin, $this->settings->exports);
    }
}