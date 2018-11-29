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

function stories_uninstall(&$content)
{
    $db = \phpws2\Database::getDB();
    $db->buildTable('storiesauthor')->drop(true);
    $db->buildTable('storiesentry')->drop(true);
    $db->buildTable('storiestag')->drop(true);
    $db->buildTable('storiesfeaturestory')->drop(true);
    $db->buildTable('storiespublish')->drop(true);
    $db->buildTable('storiesfeature')->drop(true);
    $db->buildTable('storiestagtoentry')->drop(true);
    $db->buildTable('storiestrack')->drop(true);
    $db->buildTable('storieshost')->drop(true);
    $db->buildTable('storiesshare')->drop(true);
    $db->buildTable('storiesguest')->drop(true);
    // From previous version
    $db->buildTable('storiesentrytofeature')->drop(true);
    $shortcuts = $db->addTable('access_shortcuts');
    $shortcuts->addFieldConditional('url', 'stories:%', 'like');
    $db->delete();
    return true;
}
