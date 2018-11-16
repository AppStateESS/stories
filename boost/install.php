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

require_once PHPWS_SOURCE_DIR . 'mod/stories/boost/StoriesTables.php';

function stories_install(&$content)
{
    $db = Database::getDB();
    $db->begin();

    try {
        $storiesTables = new StoriesTables;
        
        $entryTable = $storiesTables->createEntry();
        $authorTable = $storiesTables->createAuthor();
        $guestTable = $storiesTables->createGuest();
        $hostTable = $storiesTables->createHost();
        $shareTable = $storiesTables->createShare();
        $trackTable = $storiesTables->createTrack();
        $publishTable = $storiesTables->createPublish();
        $featureTable = $storiesTables->createFeature();
        $featureStoryTable = $storiesTables->createFeatureStory();
        $tagTable = $storiesTables->createTag();
        $tagToEntryTable = $storiesTables->createTagToEntry();
        
    } catch (\Exception $e) {
        \phpws2\Error::log($e);
        $db->rollback();
        if (isset($entryTable)) {
            $entryTable->drop(true);
        }
        if (isset($authorTable)) {
            $authorTable->drop(true);
        }
        if (isset($guestTable)) {
            $guestTable->drop(true);
        }
        if (isset($hostTable)) {
            $hostTable->drop(true);
        }
        if (isset($shareTable)) {
            $shareTable->drop(true);
        }

        if (isset($trackTable)) {
            $trackTable->drop(true);
        }
        
        if (isset($publishTable)) {
            $publishTable->drop(true);
        }
        if (isset($featureTable)) {
            $featureTable->drop(true);
        }
        if (isset($tagTable)) {
            $tagTable->drop(true);
        }
        if (isset($tagToEntryTable)) {
            $tagToEntryTable->drop(true);
        }
        throw $e;
    }
    $db->commit();

    $content[] = 'Tables created';
    return true;
}
