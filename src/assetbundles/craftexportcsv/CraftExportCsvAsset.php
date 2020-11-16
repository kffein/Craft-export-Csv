<?php
/**
 * craft-export-csv plugin for Craft CMS 3.x
 *
 * Description
 *
 * @link      http://kffein.com
 * @copyright Copyright (c) 2018 KFFEIN
 */

namespace kffein\craftexportcsv\assetbundles\craftexportcsv;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * CraftexportcsvAsset AssetBundle
 *
 * @author    KFFEIN
 * @package   CraftExportCsv
 * @since     1.0.1
 */
class CraftExportCsvAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@kffein/craftexportcsv/assetbundles/craftexportcsv/dist';

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'js/CraftExportCsv.js',
        ];

        $this->css = [
            'css/CraftExportCsv.css',
        ];

        parent::init();
    }
}
