<?php

/*
 * The MIT License
 *
 * Copyright 2017 Matthew McNaney <mcnaneym@appstate.edu>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
/**
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 */

namespace stories\Factory;

use stories\Resource\EntryResource as Resource;
use phpws2\Database;
use Canopy\Request;

/**
 * Description of SettingsFactory
 *
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 */
class SettingsFactory extends BaseFactory
{

    public function build()
    {
        
    }

    public function post(Request $request)
    {
        $param = $request->pullPostString('param');
        $value = $request->pullPostVar('value');
        if ($param == 'listStoryAmount') {
            if ($value > 20) {
                $value = 20;
            } elseif ($value == 0) {
                $value = 1;
            }
        }
        \phpws2\Settings::set('stories', $param, $value);
    }

    /**
     * All setting pulled from phpws2\Settings for stories
     * @see stories\Module::getSettingDefaults
     * 
     * @staticvar type $settingList
     * @return type
     */
    public function listing()
    {
        static $settingList;
        if (empty($settingList)) {
            $module = new \stories\Module;
            $defaults = $module->getSettingDefaults();
            $keys = array_keys($defaults);
            foreach ($keys as $settingName) {
                $settingList[$settingName] = \phpws2\Settings::get('stories',
                                $settingName);
            }
        }
        return $settingList;
    }

    public function needPurging()
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesentry');
        $tbl->addFieldConditional('deleted', 1);
        $id = $tbl->addField('id', 'count');
        $id->showCount();
        $deleted = $db->selectColumn();
        return $deleted;
    }

    public function purgeDeleted()
    {
        $entryFactory = new EntryFactory;
        $db = Database::getDB();
        $tbl = $db->addTable('storiesentry');
        $tbl->addField('id');
        $tbl->addFieldConditional('deleted', 1);
        while ($id = $db->selectColumn()) {
            $entryFactory->purge($id);
        }
        return true;
    }

}
