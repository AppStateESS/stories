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

namespace stories\Controller\Listing;

use Canopy\Request;
use stories\Factory\EntryFactory as Factory;
use stories\Factory\TagFactory;
use stories\Factory\StoryMenu;
use stories\Factory\HostFactory;
use stories\Controller\RoleController;

class Admin extends User
{

    /**
     * @var stories\Factory\EntryFactory Factory
     */
    protected $factory;

    protected function loadFactory()
    {
        $this->factory = new Factory;
    }

    public function getHtml(Request $request)
    {
        StoryMenu::addStoryLink();
        return parent::getHtml($request);
    }

    protected function adminHtmlCommand(Request $request)
    {
        if (class_exists('\Menu')) {
            \Menu::disableMenu();
        }
        \Layout::hideDefault(true);
        $hostFactory = new HostFactory;
        $shareList = $hostFactory->getHostsSelect();

        return $this->view->scriptView('EntryList', true,
                        ['shareList' => $shareList]);
    }

    protected function adminJsonCommand(Request $request)
    {
        $tagFactory = new TagFactory;
        $result = $this->view->adminListView($request);
        $result['tags'] = $tagFactory->listTags(true);
        return $result;
    }

}
