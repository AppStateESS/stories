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
use stories\Exception\ResourceNotFound;
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
    public function create()
    {
        $entry = $this->build();
        $entry->title = '';
        $entry->content = '';
        $authorFactory = new AuthorFactory;
        $this->loadAuthor($entry, $authorFactory->getByCurrentUser(true));
        return self::saveResource($entry);
    }

    public function form(Resource $entry)
    {
        $sourceHttp = PHPWS_SOURCE_HTTP;
        $insertSource = PHPWS_SOURCE_HTTP . 'mod/stories/javascript/MediumEditor/insert.js';
        $vars['cssOverride'] = $this->mediumCSSOverride();
        $entryId = $entry->id;
        $vars['home'] = $sourceHttp;
        $vars['MediumEditorPack'] = $this->scriptView('MediumEditorPack', false);
        $vars['EntryForm'] = $this->scriptView('EntryForm', false);
        $vars['title'] = $entry->title;
        $vars['content'] = $this->prepareFormContent($entry->content);
        $vars['insert'] = "<script src='$insertSource'></script>";
        $vars['entryId'] = "<script>let entryId=$entryId</script>";
        $template = new \phpws2\Template($vars);
        $template->setModuleTemplate('stories', 'Entry/Form.html');
        return $template->get();
    }

    public function mediumCSSOverride()
    {
        $homeHttp = PHPWS_SOURCE_HTTP;
        return <<<EOF
<link rel="stylesheet" type="text/css" href="{$homeHttp}mod/stories/css/MediumOverrides.css" />
EOF;
    }

    private function prepareFormContent($content)
    {
        $content = str_replace("\n", '\\n', $content);
        $suffix = '<p class="medium-insert-active"><br></p>';
        return $content . $suffix;
        $figure = '<figure contenteditable="false">';
        $content2 = preg_replace("/$figure/",
                '<div class="medium-insert-images medium-insert-active">' . $figure,
                $content);
        return preg_replace("/<\/figure>/", '</figure></div>', $content2) . $suffix;
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

    public function data($id)
    {
        $entry = $this->load($id);
        return $entry->getStringVars(true);
    }

    public function view($id)
    {
        try {
            $data = $this->data($id);
            $data['cssOverride'] = $this->mediumCSSOverride();
            $template = new \phpws2\Template($data);
            $template->setModuleTemplate('stories', 'Entry/View.html');
            return $template->get();
        } catch (ResourceNotFound $e) {
            return $this->notFound();
        }
    }

    public function notFound()
    {
        $template = new \phpws2\Template();
        $template->setModuleTemplate('stories', 'Entry/NotFound.html');
        return $template->get();
    }

}
