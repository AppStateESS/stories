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
 * Description of User
 *
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 */

namespace stories\Controller\Tag;

use Canopy\Request;
use stories\Factory\TagFactory as Factory;
use stories\Factory\EntryFactory;
use stories\Controller\RoleController;

class User extends RoleController
{

    /**
     * @var stories\Factory\TagFactory Factory
     */
    protected $factory;

    protected function loadFactory()
    {
        $this->factory = new Factory;
    }
    
    protected function entryJsonCommand(Request $request)
    {
        return $this->factory->getTagsByEntryId($request->pullGetInteger('entryId'), true);
    }
    
    protected function listHtmlCommand(Request $request)
    {   
        $entryFactory = new EntryFactory;
        $vars['tag'] = $this->id;
        $request->setGetVars($vars);
        $title = "Stories for tag <strong>{$this->id}</strong>";
        $content = $entryFactory->showStories($request, $title);
        if (empty($content)) {
            return '<p>No stories found for this tag.</p>';
        }
        
        return $content;
    }
    
    public function getHtml(Request $request)
    {
        $this->id = $request->shiftCommand();
        return parent::getHtml($request);
    }
    
}
