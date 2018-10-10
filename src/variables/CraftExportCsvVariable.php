<?php
/**
 * craft-export-csv Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.craftexportcsv }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    KFFEIN
 * @package   CraftExportCsv
 * @since     1.0.1
 */

namespace kffein\craftexportcsv\variables;

use Craft;

use kffein\craftexportcsv\CraftExportCsv;

class CraftExportCsvVariable
{
    // Public Methods
    // =========================================================================
    public function isExportable() {
        return CraftExportCsv::$plugin->craftExportCsvService->isExportable();
    }
}
