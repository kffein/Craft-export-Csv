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
 * CraftExportCsv Export Model
 *
 * @author    KFFEIN
 * @package   CraftExportCsv
 * @since     1.0.1
 */
class Export extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $id;
    public $sectionHandle;
    public $filename = '{section-handle}-{timestamp}.csv';

    /**
     * Temporary porperty of the converted field name everytime the csv is generated.
     *
     * @var string
     */
    public $lastSavedFilename = '.csv';

    /**
     * @var int
     */
    public $numberOfRows;
    public $dateUpdated;

    /**
     * @var array
     */
    public $fields;

    // Public Methods
    // =========================================================================
    public function init()
    {
        parent::init();

        $this->id = uniqid();
    }

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
    public function rules()
    {
        return [
            [['sectionHandle', 'filename', 'fields'], 'required'],
            [['sectionHandle', 'filename'], 'string'],
            [['numberOfRows'], 'number'],
        ];
    }
}
