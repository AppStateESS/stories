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
use phpws2\Database\ForeignKey;

class StoriesTables
{

    public function createEntry()
    {
        $db = Database::getDB();
        $entry = new \stories\Resource\EntryResource;
        return $entry->createTable($db);
    }

    public function createAuthor()
    {
        $db = Database::getDB();
        $author = new \stories\Resource\AuthorResource;
        return $author->createTable($db);
    }

    public function createGuest()
    {
        $db = Database::getDB();
        $guest = new \stories\Resource\GuestResource;
        $guestTable = $guest->createTable($db);
        $guestUnique = new \phpws2\Database\Unique($guestTable->getDataType('authkey'));
        $guestUnique->add();
        $urlUnique = new \phpws2\Database\Unique($guestTable->getDataType('url'));
        $urlUnique->add();
        return $guestTable;
    }

    public function createHost()
    {
        $db = Database::getDB();
        $host = new \stories\Resource\HostResource;
        $hostTable = $host->createTable($db);
        $url = $hostTable->getDataType('url');
        $unique = new \phpws2\Database\Unique($url);
        $unique->add();
        return $hostTable;
    }

    public function createShare()
    {
        $db = Database::getDB();
        $guestTable = $db->addTable('storiesguest');
        $share = new \stories\Resource\ShareResource;
        $shareTable = $share->createTable($db);
        $shareGuestId = $shareTable->getDataType('guestId');
        $shareEntryId = $shareTable->getDataType('entryId');
        $shareUnique = new \phpws2\Database\Unique([$shareGuestId, $shareEntryId]);
        $shareUnique->add();

        $shareForeign = new ForeignKey($shareGuestId,
                $guestTable->getDataType('id'), ForeignKey::CASCADE);
        $shareForeign->add();
        return $shareTable;
    }

    public function createFeature()
    {
        $db = Database::getDB();
        $feature = new \stories\Resource\FeatureResource;
        return $feature->createTable($db);
    }

    public function createFeatureStory()
    {
        $db = Database::getDB();
        $publishTable = $db->addTable('storiespublish');
        $featureTable = $db->addTable('storiesfeature');
        $featureStory = new \stories\Resource\FeatureStoryResource;
        $featureStoryTable = $featureStory->createTable($db);

        $featureForeign = new ForeignKey($featureStoryTable->getDataType('featureId'),
                $featureTable->getDataType('id'), ForeignKey::CASCADE);
        $featureForeign->add();
        $publishForeign = new ForeignKey($featureStoryTable->getDataType('publishId'),
                $publishTable->getDataType('id'), ForeignKey::CASCADE);
        $publishForeign->add();

        return $featureStoryTable;
    }

    public function createTrack()
    {
        $db = Database::getDB();
        $hostTable = $db->addTable('storieshost');
        $trackTable = $db->buildTable('storiestrack');
        $trackEntryId = $trackTable->addDataType('entryId', 'int');
        $trackHostId = $trackTable->addDataType('hostId', 'int');
        $trackTable->create();
        $trackUnique = new \phpws2\Database\Unique([$trackEntryId, $trackHostId]);
        $trackUnique->add();

        $trackForeign = new ForeignKey($trackTable->getDataType('hostId'),
                $hostTable->getDataType('id'), ForeignKey::CASCADE);
        $trackForeign->add();
        return $trackTable;
    }

    public function createPublish()
    {
        $db = Database::getDB();
        $publishResource = new \stories\Resource\PublishResource;
        $publishTable = $publishResource->createTable($db);
        $publishUnique = new \phpws2\Database\Unique([$publishTable->getDataType('entryId'), $publishTable->getDataType('shareId')]);
        $publishUnique->add();
        return $publishTable;
    }

    public function createTag()
    {
        $db = Database::getDB();
        $tag = new \stories\Resource\TagResource;
        return $tag->createTable($db);
    }

    public function createTagToEntry()
    {
        $db = Database::getDB();
        $tagToEntry = $db->buildTable('storiestagtoentry');
        $entryId = $tagToEntry->addDataType('entryId', 'int');
        $tagId = $tagToEntry->addDataType('tagId', 'int');
        $tagUnique = new \phpws2\Database\Unique(array($tagId, $entryId));
        $tagToEntry->addUnique($tagUnique);
        return $tagToEntry->create();
    }

}
