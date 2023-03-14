<?php
/**
 * craft-export-csv plugin for Craft CMS 3.x
 *
 * Description
 *
 * @link      http://kffein.com
 * @copyright Copyright (c) 2018 KFFEIN
 */

namespace kffein\craftexportcsv\models;

use kffein\craftexportcsv\CraftExportCsv;
use craft\base\Model;

/**
 * CraftExportCsv Settings Model
 *
 * @author    KFFEIN
 * @package   CraftExportCsv
 * @since     1.0.1
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Array of kffein\craftexportcsv\Export
     *
     * @var array
     */
    public $exports;

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
