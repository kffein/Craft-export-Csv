<?php
/**
 * craft-export-csv plugin for Craft CMS 3.x
 *
 * Description
 *
 * @link      http://kffein.com
 * @copyright Copyright (c) 2018 Joel Lachance
 */

namespace kffein\craftexportcsv\assetbundles\CraftExportCsv;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * CraftexportcsvAsset AssetBundle
 *
 * @author    Joel Lachance
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
        $this->sourcePath = "@kffein/craftexportcsv/assetbundles/craftexportcsv/dist";

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
