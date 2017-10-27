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
use stories\Resource\EntryResource;

/**
 * Description of Menu
 *
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 */
class StoryMenu
{

    public static function addStoryLink()
    {
        $link = '<a href="./stories/Entry/create"><i class="fa fa-pencil"></i> Add story</a>';
        \MiniAdmin::add('stories', $link);
    }

    public static function editStoryLink($entryId)
    {
        $link = '<a href="./stories/Entry/' . $entryId . '/edit"><i class="fa fa-edit"></i> Edit story</a>';
        \MiniAdmin::add('stories', $link);
    }

    public static function viewStoryLink(EntryResource $entry)
    {
        
        $link = <<<EOF
<a href="./stories/Entry/{$entry->id}/{$entry->urlTitle}"><i class="fa fa-book"></i> View story</a>
EOF;
        \MiniAdmin::add('stories', $link);
    }

    public static function listStoryLink()
    {
        $link = '<a href="./stories/Listing/"><i class="fa fa-list"></i> List</a>';
        \MiniAdmin::add('stories', $link);
    }
    
    public static function featureLink()
    {
        $link = '<a href="./stories/Feature/"><i class="fa fa-exclamation-circle"></i> Features</a>';
        \MiniAdmin::add('stories', $link);
    }

    public static function mediumCSSLink()
    {
        $homeHttp = PHPWS_HOME_HTTP;
        return <<<EOF
<link type="text/css" rel="stylesheet" href="{$homeHttp}mod/stories/css/medium-editor-insert-plugin.min.css" />
EOF;
    }
    
    public static function adminDisplayLink()
    {
        $link = '<a href="./stories/Settings"><i class="fa fa-gear"></i> Settings</a>';
        \MiniAdmin::add('stories', $link);
        
    }

}
