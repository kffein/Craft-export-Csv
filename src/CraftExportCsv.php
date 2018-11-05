<?php
/**
 * craft-export-csv plugin for Craft CMS 3.x
 *
 * Description
 *
 * @link      http://kffein.com
 * @copyright Copyright (c) 2018 KFFEIN
 *
 *
 */

namespace kffein\craftexportcsv;

use kffein\craftexportcsv\services\Reports;
use kffein\craftexportcsv\services\Exports;
use kffein\craftexportcsv\variables\CraftExportCsvVariable;
use kffein\craftexportcsv\models\Settings;
use Craft;
use craft\base\Plugin;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use craft\helpers\UrlHelper;
use craft\events\RegisterUrlRulesEvent;
use yii\base\Event;
use yii\db\Query;

/**
 * Craft Export Csv Plugin
 *
 * @author    KFFEIN
 * @package   CraftExportCsv
 * @since     1.0.1
 *
 * @property  Reports $reportsService
 * @property  Exports $exportsService
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class CraftExportCsv extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * CraftExportCsv::$plugin
     *
     * @var CraftExportCsv
     */
    public static $plugin;

    const FIELD_TYPE_HANDLE = 'handle';
    const FIELD_TYPE_CONCAT_HANDLE = 'concat-handle';
    const FIELD_TYPE_CUSTOM_QUERY = 'custom-query';

    // Public Properties
    // =========================================================================

    /**
     * @var bool
     */
    public $hasCpSection = true;

    /**
     * @var bool
     */
    public $hasCpSettings = true;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Craftexportcsv::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();

        self::$plugin = $this;

        $this->setComponents([
            'reportsService' => Reports::class,
            'exportsService' => Exports::class,
        ]);

        $this->verifyQueueStates();

        // Register our Control Panel routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['craft-export-csv'] = 'craft-export-csv/reports/index';
                $event->rules['craft-export-csv/download'] = 'craft-export-csv/reports/download';
                $event->rules['craft-export-csv/settings'] = 'craft-export-csv/settings';
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('craftExportCsv', CraftExportCsvVariable::class);
                $variable->set('exportsService', Exports::class);
            }
        );

        Craft::info(
            Craft::t(
                'craft-export-csv',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    public function getCpNavItem()
    {
        $menuItem = parent::getCpNavItem();
        $menuItem['subnav'] = [
            'reports' => [
                'label' => Craft::t('craft-export-csv', 'reports-label'),
                'url' => 'craft-export-csv'
            ],
            'settings' => [
                'label' => Craft::t('craft-export-csv', 'settings'),
                'url' => 'craft-export-csv/settings'
            ]
        ];

        return $menuItem;
    }

    /**
     * Redirect to Craft Export CSV Settings
     *
     * @inheritdoc
     */
    public function getSettingsResponse()
    {
        $url = UrlHelper::cpUrl('craft-export-csv/settings');

        return Craft::$app->getResponse()->redirect($url);
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    private function verifyQueueStates()
    {
        $query = (new Query())->from('{{%queue}}');
        $jobInfo = $query
        ->select(['id', 'job', 'fail', 'timeUpdated', 'description'])
        ->all();
        foreach ($jobInfo as $job) {
            if ($job['description'] == 'Exporting Csv' && !$job['fail'] && $job['timeUpdated']) {
                if (time() - $job['timeUpdated'] > 15) {
                    Craft::$app->queue->retry($job['id']);
                }
            }
        }
    }
}
