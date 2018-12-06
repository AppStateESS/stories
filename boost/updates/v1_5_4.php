<?php

/**
 * MIT License
 * Copyright (c) 2018 Electronic Student Services @ Appalachian State University
 * 
 * See LICENSE file in root directory for copyright and distribution permissions.
 * 
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @license https://opensource.org/licenses/MIT
 */
use phpws2\Database;

require_once PHPWS_SOURCE_DIR . 'mod/stories/boost/StoriesTables.php';

class storiesUpdate_1_5_4
{

    private $changes = array();
    private $tableMaker;

    public function __construct()
    {
        $this->tableMaker = new StoriesTables;
    }

    private function updateFeatureStoryTable()
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesfeaturestory');
        $newDataType = new \phpws2\Database\Datatype\Varchar($tbl, 'thumbnail',
                300);
        $tbl->alter($tbl->getDataType('thumbnail'), $newDataType);
        $this->changes[] = 'Increased feature story thumbnail url size.';
    }

    public function run()
    {
        $this->changes[] = 'Fixed feature add link.';
        $this->changes[] = 'Fixed feature listing not showing empty features.';
        $this->changes[] = 'Fixed features listing that were not active.';
        $this->updateFeatureStoryTable();
        return $this->changes;
    }

}
