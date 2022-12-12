<?php

namespace kffein\craftexportcsv\controllers;

use kffein\craftexportcsv\CraftExportCsv;
use kffein\craftexportcsv\models\Export;
use Craft;
use craft\base\Plugin;
use craft\errors\InvalidPluginException;
use craft\web\Controller as BaseController;
use yii\web\BadRequestHttpException;

/**
 *
 * @author    Camille Boissel
 * @package   CraftExportCsv
 * @since     1.0.1
 */
class SettingsController extends BaseController
{
    /**
     * The active Plugin class
     *
     * @var Plugin
     */
    public $plugin;

    /**
     * The plugin settings
     *
     * @var kffein\craftexportcsv\models\Settings
     */
    public $settings;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        /**
         * For unknow reason Craft Controller set request + response to string
         * Manually reset the value fix the problem
         */
        $this->request = Craft::$app->request;
        $this->response = Craft::$app->response;

        $this->plugin = CraftExportCsv::$plugin;
        $this->settings = $this->plugin->getSettings();
    }

    /**
     * Prepare plugin settings for output
     *
     * @return \yii\web\Response
     * @throws InvalidPluginException
     */
    public function actionIndex()
    {
        if (!$this->plugin) {
            throw new InvalidPluginException($this->plugin->handle);
        }

        $sections = Craft::$app->getSections()->getAllSections();
        $sectionsOptions = [];
        foreach ($sections as $section) {
            $sectionsOptions[] = [
            'label' => $section->name,
            'value' => $section->handle,
          ];
        }

        $sites = Craft::$app->sites->allSites;
        $sitesOptions = array_map(function ($site) {
            return [
                'label' => $site->name,
                'value' => $site->id
            ];
        }, $sites);

        $status_list = ['STATUS_ARCHIVED', 'STATUS_DISABLED', 'STATUS_ENABLED', 'STATUS_EXPIRED', 'STATUS_LIVE', 'STATUS_PENDING'];

        $statusOptions = array_map(function ($status) {
            return [
                'label' => ucfirst(strtolower(explode('_', $status)[1])),
                'value' => ucfirst(strtolower(explode('_', $status)[1]))
            ];
        }, $status_list);

        // If the user has selected a particular export, find the details
        $selectedExport = null;
        $id = Craft::$app->request->getParam('id');

        if ($id) {
          foreach ($this->settings->exports as $export) {
            if ($id = $export['id']) {
              $selectedExport = $export;
            }
          }
        }

        return $this->renderTemplate(
            'craft-export-csv/settings',
            [
                'settings' => $this->settings,
                'sectionsOptions' => $sectionsOptions,
                'sitesOptions' => $sitesOptions,
                'statusOptions' => $statusOptions,
                'fieldTypeOptions' => $this->plugin->reportsService->getFieldTypeOptions(),
                'selectedExport' => $selectedExport,
            ]
        );
    }

    /**
     * Saves plugin settings
     *
     * @return null|\yii\web\Response
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     */
    public function actionSaveSettings()
    {
        $this->requirePostRequest();

        // Get param value for a new export.
        $exportValue = Craft::$app->getRequest()->getBodyParam('settings', []);

        if (empty($exportValue['filename'])) {
            Craft::$app->getSession()->setError(Craft::t('app', 'A file name is required'));

            // Send the plugin back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'plugin' => $this->plugin
            ]);
            return null;
        }

        // Validate that the section belong to siteId settings
        $sectionData = Craft::$app->sections->getSectionByHandle($exportValue['sectionHandle']);

        $sectionSiteEnabled = array_map(function ($sectionSetting) {
            return $sectionSetting->siteId;
        }, Craft::$app->sections->getSectionSiteSettings($sectionData->id));

        if (!in_array($exportValue['siteId'], $sectionSiteEnabled)) {
            Craft::$app->getSession()->setError(Craft::t('app', 'The section is invalid for selected site'));
            // Send the plugin back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                 'plugin' => $this->plugin
             ]);
            return null;
        }

        $id = $exportValue['id'] ?? null;

        if ($id) {
          // Update existing export using model
          $export = $this->plugin->exportsService->getExportById($id);
          $existingExport = new Export();
          $existingExport->setAttributes($exportValue);
          foreach ($this->settings->exports as $key => $export) {
            if ($id == $export['id']) {
              $this->settings->exports[$key] = $existingExport;
            }
          }
        } else {
          // Create export from model and set all required value.
          $newExport = new Export();
          $newExport->setAttributes($exportValue);
          // Add new export to settings array
          $this->settings->exports[] = $newExport;
        }

        if (!Craft::$app->getPlugins()->savePluginSettings($this->plugin, $this->settings->exports)) {
            Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t save plugin settings.'));

            // Send the plugin back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'plugin' => $this->plugin
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('app', 'Plugin settings updated.'));

        // return $this->redirectToPostedUrl();
        return $this->redirect('craft-export-csv/settings');
    }

    /**
     * Duplicate an export
     *
     * @return null|\yii\web\Response
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     */
    public function actionDuplicateExport()
    {
        $id = Craft::$app->request->getParam('id');

        $this->plugin->exportsService->duplicateExportById($id);

        if (!Craft::$app->getPlugins()->savePluginSettings($this->plugin, $this->settings->exports)) {
            Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t save plugin settings.'));
        } else {
            Craft::$app->getSession()->setNotice(Craft::t('app', 'Plugin settings updated.'));
        }
        return $this->redirect('craft-export-csv/settings');
    }

    /**
     * Delete an export from array
     *
     * @return null|\yii\web\Response
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     */
    public function actionDeleteExport()
    {
        $id = Craft::$app->request->getParam('id');

        $this->plugin->exportsService->deleteExportById($id);

        if (!Craft::$app->getPlugins()->savePluginSettings($this->plugin, $this->settings->exports)) {
            Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t save plugin settings.'));
        } else {
            Craft::$app->getSession()->setNotice(Craft::t('app', 'Plugin settings updated.'));
        }
        return $this->redirect('craft-export-csv/settings');
    }
}
