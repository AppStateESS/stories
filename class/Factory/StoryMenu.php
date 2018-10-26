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
        $link = '<a href="./stories/Entry/create"><i class="fas fa-pencil-alt"></i> Add story</a>';
        \MiniAdmin::add('stories', $link);
    }
    
    public static function addShareLink()
    {
        $link = '<a href="./stories/Host"><i class="fas fa-share-alt"></i> Share</a>';
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
<a href="./stories/Entry/{$entry->id}/{$entry->urlTitle}"><i class="fas fa-book"></i> View story</a>
EOF;
        \MiniAdmin::add('stories', $link);
    }

    public static function listStoryLink()
    {
        $link = '<a href="./stories/Listing/admin"><i class="fa fa-list"></i> List</a>';
        \MiniAdmin::add('stories', $link);
    }
    
    public static function authorLink()
    {
        $link = '<a href="./stories/Author/"><i class="fa fa-user"></i> Authors</a>';
        \MiniAdmin::add('stories', $link);
    }
    
    public static function featureLink()
    {
        $link = '<a href="./stories/Feature/"><i class="fa fa-exclamation-circle"></i> Features</a>';
        \MiniAdmin::add('stories', $link);
    }

    public static function adminDisplayLink()
    {
        $link = '<a href="./stories/Settings"><i class="fas fa-cog"></i> Settings</a>';
        \MiniAdmin::add('stories', $link);
        
    }

}
