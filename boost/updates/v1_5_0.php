<?php

/**
 * MIT License
 * Copyright (c) 2019 Electronic Student Services @ Appalachian State University
 * 
 * See LICENSE file in root directory for copyright and distribution permissions.
 * 
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @license https://opensource.org/licenses/MIT
 */
use phpws2\Database;

require_once PHPWS_SOURCE_DIR . 'mod/stories/boost/StoriesTables.php';

class storiesUpdate_1_5_0
{

    private $changes = array();
    private $authors;
    private $tableMaker;

    public function __construct()
    {
        $this->loadAuthors();
        $this->tableMaker = new StoriesTables;
    }

    private function loadAuthors()
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesauthor');
        $authors = $db->select();
        foreach ($authors as $a) {
            $this->authors[$a['id']] = ['authorName' => $a['name'], 'authorPic' => $a['pic']];
        }
    }

    public function run()
    {
        $this->createGuestTable();
        $this->createHostTable();
        $this->createShareTable();
        $this->createTrackTable();
        $this->createPublishTable();
        $this->createFeatureStoryTable();

        $this->publishCurrentEntries();
        $this->combineEntryToFeature();

        $this->updateEntry();
        $this->updateFeature();
        return $this->changes;
    }

    private function updateEntry()
    {
        $db = Database::getDB();
        $entryTable = $db->addTable('storiesentry');
        $listView = new \phpws2\Database\Datatype\Smallint($entryTable,
                'listView');
        $listView->add();
        $this->changes[] = 'Added listView column to storiesentry.';
    }

    private function updateFeature()
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesfeature');
        $tbl->dropColumn('columns');
        $this->changes[] = 'Dropped columns column from storiesfeature.';
    }

    private function createTrackTable()
    {
        $this->tableMaker->createTrack();
        $this->changes[] = 'Created track table.';
    }

    private function createShareTable()
    {
        $this->tableMaker->createShare();
        $this->changes[] = 'Created share table.';
    }

    private function createGuestTable()
    {
        $this->tableMaker->createGuest();
        $this->changes[] = 'Created guest table.';
    }

    private function createHostTable()
    {
        $this->tableMaker->createHost();
        $this->changes[] = 'Created host table.';
    }

    private function getCurrentEntries()
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesentry');
        $tbl->addFieldConditional('deleted', 0);
        $tbl->addFieldConditional('published', 1);
        $entryList = $db->select();
        return $entryList;
    }

    private function createPublishTable()
    {
        $db = Database::getDB();
        $publish = new \stories\Resource\PublishResource();
        $publish->createTable($db);
        $this->changes[] = 'Create publish table.';
    }

    private function publishCurrentEntries()
    {
        $currentEntries = $this->getCurrentEntries();
        if (empty($currentEntries)) {
            return;
        }
        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        foreach ($currentEntries as $entry) {
            $tbl->addValue('entryId', $entry['id']);
            $tbl->addValue('shareId', 0);
            $tbl->addValue('publishDate', $entry['publishDate']);
            $tbl->insert();
            $tbl->resetValues();
        }
        $this->changes[] = 'Filled in publish table.';
    }

    private function createFeatureStoryTable()
    {
        // Feature was made with unsigned which was phased out.
        $db = Database::getDB();
        if (preg_match('/mysqli?/', $db->getDatabaseType())) {
            $db->exec('alter table storiesfeature modify id int(11) auto_increment default null');
        }
        $this->tableMaker->createFeatureStory();
        $this->changes[] = 'Created feature story table.';
    }

    private function combineEntryToFeature()
    {
        $entryToFeatureRows = $this->getEntryCombines();
        if (empty($entryToFeatureRows)) {
            return;
        }

        $db = Database::getDB();
        foreach ($entryToFeatureRows as $row) {
            $featureStory = new \stories\Resource\FeatureStoryResource;
            $featureRow = $this->readyEntryValues($row);
            $featureStory->setVars($featureRow);
            \stories\Factory\FeatureStoryFactory::saveResource($featureStory);
        }
        $this->changes[] = 'Copied over feature stories.';
    }

    private function readyEntryValues($row)
    {
        unset($row['entryId']);
        $row['authorName'] = $this->authors[$row['authorId']]['authorName'];
        $row['authorPic'] = $this->authors[$row['authorId']]['authorPic'];
        unset($row['authorId']);
        unset($row['content']);
        unset($row['createDate']);
        unset($row['deleted']);
        unset($row['expirationDate']);
        unset($row['published']);
        unset($row['leadImage']);
        unset($row['updateDate']);
        unset($row['imageOrientation']);
        $row['url'] = './stories/' . $row['urlTitle'];
        unset($row['urlTitle']);
        $row['summary'] = substr(strip_tags($row['summary']), 0, 299);
        $row['eventDate'] = 0;
        $row['shareId'] = 0;
        $row['id'] = 0;
        return $row;
    }

    private function getEntryCombines()
    {
        $db = \phpws2\Database::getDB();
        $entryToFeatureTable = $db->addTable('storiesentrytofeature');
        $entryTable = $db->addTable('storiesentry');
        $publishTable = $db->addTable('storiespublish');
        $publishTable->addField('id', 'publishId');
        $conditional1 = $db->createConditional($entryToFeatureTable->getField('entryId'),
                $entryTable->getField('id'), '=');
        $join = $db->joinResources($entryToFeatureTable, $entryTable,
                $conditional1);
        $conditional2 = $db->createConditional($entryTable->getField('id'),
                $publishTable->getField('entryId'));
        $db->joinResources($entryTable, $publishTable, $conditional2);
        $entryToFeatureRows = $db->select();
        return $entryToFeatureRows;
    }

}
