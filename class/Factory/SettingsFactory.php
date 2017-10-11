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

namespace stories\Factory;

use stories\Resource\EntryResource as Resource;
use phpws2\Database;
use Canopy\Request;
use phpws2\Template;

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

        $settings = new \phpws2\Settings();
        if ($param == 'listStories' || $param == 'showFeatures') {
            $value = $value == 'true' ? 1 : 0;
        }
        $settings->set('stories', $param, $value);
    }

}
