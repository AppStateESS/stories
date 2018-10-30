<?php

/*
 * Copyright (C) 2017 Matthew McNaney <mcnaneym@appstate.edu>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

use phpws2\Database;
use phpws2\Database\ForeignKey;

function stories_install(&$content)
{
    $db = Database::getDB();
    $db->begin();

    try {
        $entry = new \stories\Resource\EntryResource;
        $entryTable = $entry->createTable($db);

        $author = new \stories\Resource\AuthorResource;
        $authorTable = $author->createTable($db);

        $guest = new \stories\Resource\GuestResource;
        $guestTable = $guest->createTable($db);
        $guestUnique = new \phpws2\Database\Unique($guestTable->getDataType('authkey'));
        $guestUnique->add();

        $share = new \stories\Resource\ShareResource;
        $shareTable = $share->createTable($db);
        $shareGuestId = $shareTable->getDataType('guestId');
        $shareEntryId = $shareTable->getDataType('entryId');
        $shareUnique = new \phpws2\Database\Unique([$shareGuestId, $shareEntryId]);
        $shareUnique->add();
        $shareForeign = new ForeignKey($shareGuestId,
                $guestTable->getDataType('id'), ForeignKey::CASCADE);
        $shareForeign->add();

        $featureStory = new \stories\Resource\FeatureStoryResource;
        $featureStory->createTable($db);

        $trackTable = $db->buildTable('storiestrack');
        $trackEntryId = $trackTable->addDataType('entryId', 'int');
        $trackHostId = $trackTable->addDataType('hostId', 'int');
        $trackTable->create();
        $trackUnique = new \phpws2\Database\Unique([$trackEntryId, $trackHostId]);
        $trackUnique->add();

        $publishResource = new \stories\Resource\PublishResource;
        $publishTable = $publishResource->createTable($db);
        $publishUnique = new \phpws2\Database\Unique([$publishTable->getDataType('entryId'), $publishTable->getDataType('shareId')]);
        $publishUnique->add();

        $feature = new \stories\Resource\FeatureResource;
        $featureTable = $feature->createTable($db);

        $tag = new \stories\Resource\TagResource;
        $tagTable = $tag->createTable($db);

        $tagToEntry = $db->buildTable('storiestagtoentry');
        $entryId = $tagToEntry->addDataType('entryId', 'int');
        $tagId = $tagToEntry->addDataType('tagId', 'int');
        $tagUnique = new \phpws2\Database\Unique(array($tagId, $entryId));
        $tagToEntry->addUnique($tagUnique);
        $tagToEntry->create();

        $host = new \stories\Resource\HostResource;
        $hostTable = $host->createTable($db);
        $url = $hostTable->getDataType('url');
        $unique2 = new \phpws2\Database\Unique($url);
        $unique2->add();
    } catch (\Exception $e) {
        \phpws2\Error::log($e);
        $db->rollback();
        if (isset($entryTable)) {
            $entryTable->drop(true);
        }
        if (isset($authorTable)) {
            $authorTable->drop(true);
        }
        if (isset($shareTable)) {
            $shareTable->drop(true);
        }
        if (isset($publishTable)) {
            $publishTable->drop(true);
        }
        if (isset($guestTable)) {
            $guestTable->drop(true);
        }
        if (isset($featureTable)) {
            $featureTable->drop(true);
        }
        if (isset($tagTable)) {
            $tagTable->drop(true);
        }
        if (isset($tagToEntry)) {
            $tagToEntry->drop(true);
        }
        if (isset($entryToFeature)) {
            $entryToFeature->drop(true);
        }
        if (isset($hostTable)) {
            $hostTable->drop(true);
        }
        throw $e;
    }
    $db->commit();

    $content[] = 'Tables created';
    return true;
}
