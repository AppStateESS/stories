<?php

/**
 * MIT License
 * Copyright (c) 2017 Electronic Student Services @ Appalachian State University
 * 
 * See LICENSE file in root directory for copyright and distribution permissions.
 * 
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @license https://opensource.org/licenses/MIT
 */

namespace stories\Factory;

use stories\Resource\EntryResource as Resource;
use stories\Factory\AuthorFactory;
use stories\Resource\AuthorResource;
use stories\Exception\MissingInput;
use phpws2\Database;
use Canopy\Request;

class EntryFactory extends BaseFactory
{

    public function build()
    {
        return new Resource;
    }
    
    /**
     * 
     * @param type $id
     * @return \stories\Resource\EntryResource
     */
    public function load($id)
    {
        return parent::load($id);
    }

    public function listing(Request $request)
    {
        return 'listing of entries';
    }

    /**
     * Creates the first entry. Should not be used outside its creation.
     * @param string $content
     * @param string $title
     * @return \stories\Resource\Entry
     */
    public function create($content = null, $title = null)
    {
        $entry = $this->build();
        $entry->title = $title;
        $entry->setContent($content);
        $authorFactory = new AuthorFactory;
        $this->loadAuthor($entry, $authorFactory->getByCurrentUser(true));
        return self::saveResource($entry);
    }

    public function form(Resource $entry)
    {
        $insertSource = PHPWS_SOURCE_HTTP . 'mod/stories/javascript/MediumEditor/insert.js';
        $entryId = $entry->id;
        $vars['home'] = PHPWS_SOURCE_HTTP;
        $vars['MediumEditorPack'] = $this->scriptView('MediumEditorPack', false);
        $vars['EntryForm'] = $this->scriptView('EntryForm', false);
        $vars['title'] = $entry->title;
        $vars['content'] = $entry->content;
        $vars['insert'] = "<script src='$insertSource'></script>";
        $vars['entryId'] = "<script>let entryId=$entryId</script>";
        $template = new \phpws2\Template($vars);
        $template->setModuleTemplate('stories', 'Entry/Form.html');
        return $template->get();
    }

    protected function loadAuthor(Resource $entry, AuthorResource $author)
    {
        $entry->authorEmail = $author->email;
        $entry->authorName = $author->name;
        $entry->authorPic = $author->pic;
        $entry->authorId = $author->id;
    }

    public function put($entryId, Request $request)
    {
        $entry = $this->load($entryId);
        $entry->title = $request->pullPutString('title');
        $entry->setContent($request->pullPutVar('content'));
        self::saveResource($entry);
        return $entry->id;
    }


    public function patch($entryId, Request $request)
    {
        $entry = $this->load($entryId);
        switch ($request->pullPatchString('param')) {
            default:
                throw new MissingInput;
        }

        self::saveResource($entry);
        return $entry->id;
    }

    public function post(Request $request)
    {
        $title = $request->pullPostString('title', true);
        $content = $request->postVarIsset('content') ? $request->pullPostVar('content') : null;

        // If we got here somehow without a title or content,
        // we don't create a new entry and return 0 for the entry id.
        if (empty($title) && empty($content)) {
            return 0;
        }
        $entry = $this->create($content, $title);
        return $entry->id;
    }
    
    public function view($id) {
        $entry = $this->load($id);
        return $entry->getStringVars(true);
    }

}
