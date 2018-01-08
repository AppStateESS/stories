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

function stories_install(&$content)
{
    $db = \phpws2\Database::getDB();
    $db->begin();

    try {
        $entry = new \stories\Resource\EntryResource;
        $entry->createTable($db);
        $db->clearTables();

        $author = new \stories\Resource\AuthorResource;
        $author->createTable($db);
        $db->clearTables();

        $feature = new \stories\Resource\FeatureResource;
        $feature->createTable($db);
        $db->clearTables();

        $tag = new \stories\Resource\TagResource;
        $tag->createTable($db);
        $db->clearTables();

        $tagToEntry = $db->buildTable('storiestagtoentry');
        $entryId = $tagToEntry->addDataType('entryId', 'int');
        $tagId = $tagToEntry->addDataType('tagId', 'int');
        $unique = new \phpws2\Database\Unique(array($tagId, $entryId));
        $tagToEntry->addUnique($unique);
        $tagToEntry->create();

        $entryToFeature = $db->buildTable('storiesentrytofeature');
        $entryToFeature->addDataType('entryId', 'int');
        $entryToFeature->addDataType('featureId', 'int');
        $entryToFeature->addDataType('x', 'smallint');
        $entryToFeature->addDataType('y', 'smallint');
        $entryToFeature->addDataType('sorting', 'smallint');
        $entryToFeature->create();
    } catch (\Exception $e) {
        \phpws2\Error::log($e);
        $db->rollback();
        throw $e;
    }
    $db->commit();

    $content[] = 'Tables created';
    return true;
}
