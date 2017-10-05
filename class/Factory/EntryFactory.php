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
use stories\Factory\TagFactory;
use stories\Resource\AuthorResource;
use stories\Exception\MissingInput;
use stories\Exception\ResourceNotFound;
use phpws2\Database;
use Canopy\Request;
use phpws2\Template;

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
    public function load($id, $withTags=true)
    {
        $entry = parent::load($id);
        if ($withTags) {
            $tagFactory = new TagFactory;
            $entry->tags = $tagFactory->getTagsByEntryId($id);
        }
        return $entry;
    }

    public function adminListView(Request $request)
    {
        $segmentSize = \phpws2\Settings::get('stories', 'segmentSize');
        // if offset not set, default 0
        $offset = (int) $request->pullGetString('offset', true);
        $offsetSize = $segmentSize * $offset;

        $orderBy = $request->pullGetString('sortBy', true);
        if (!in_array($orderBy, array('publishDate', 'title', 'updateDate'))) {
            $orderBy = 'publishDate';
        }

        $search = $request->pullGetString('search', true);

        $options = array(
            'search' => $search,
            'orderBy' => $orderBy,
            'publishedOnly' => false,
            'hideExpired' => true,
            'limit' => $segmentSize,
            'offset' => $offsetSize
        );

        return $this->pullList($options);
    }

    public function pullList(array $options = null)
    {
        $db = Database::getDB();
        $now = time();

        $defaultOptions = array('publishedOnly' => false,
            'hideExpired' => false,
            'orderBy' => 'publishDate',
            'limit' => 30,
            'includeContent' => true,
            'offset' => 0);

        if (is_array($options)) {
            $options = array_merge($defaultOptions, $options);
        } else {
            $options = $defaultOptions;
        }

        $tbl = $db->addTable('storiesEntry');

        $tbl->addField('id');
        $tbl->addField('createDate');
        $tbl->addField('expirationDate');
        $tbl->addField('publishDate');
        $tbl->addField('published');
        $tbl->addField('summary');
        $tbl->addField('thumbnail');
        $tbl->addField('title');
        if ($options['includeContent']) {
            $tbl->addField('content');
        }

        //conditionals
        $tbl->addFieldConditional('deleted', 0);
        if ($options['publishedOnly']) {
            $tbl->addFieldConditional('published', 1);
            $tbl->addFieldConditional('publishDate', $now, '<');
        }

        if (isset($options['search']) && strlen($options['search']) >= 3) {
            $s1 = $db->createConditional($tbl->getField('title'),
                    '%' . $options['search'] . '%', 'like');
            $db->addConditional($s1);
        }

        if (!$options['hideExpired']) {
            $expire1 = $db->createConditional($tbl->getField('expirationDate'),
                    0);
            $expire2 = $db->createConditional($tbl->getField('expirationDate'),
                    $now, '>');
            $db->addConditional($db->createConditional($expire1, $expire1, 'or'));
        }

        $tbl2 = $db->addTable('storiesAuthor');
        $tbl2->addField('name', 'authorName');
        $tbl2->addField('email', 'authorEmail');
        $db->joinResources($tbl, $tbl2,
                $db->createConditional($tbl->getField('authorId'),
                        $tbl2->getField('id')), 'left');
        if (isset($options['orderBy'])) {
            $tbl->addOrderBy($options['orderBy'],
                    $options['orderBy'] === 'title' ? 'asc' : 'desc');
        }

        if (isset($options['limit'])) {
            if (isset($options['offset'])) {
                $db->setLimit($options['limit'], $options['offset']);
            } else {
                $db->setLimit($options['limit']);
            }
        }

        $objectList = $db->selectAsResources('\stories\Resource\EntryResource');
        if (empty($objectList)) {
            return null;
        }
        foreach ($objectList as $entry) {
            $listing[] = $entry->getStringVars();
        }
        return $listing;
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

    public function form(Resource $entry, $new = false)
    {
        $tagFactory = new TagFactory();
        
        $sourceHttp = PHPWS_SOURCE_HTTP;
        $insertSource = PHPWS_SOURCE_HTTP . 'mod/stories/javascript/MediumEditor/insert.js';
        $vars['cssOverride'] = $this->mediumCSSOverride();
        $entryId = $entry->id;
        $vars['home'] = $sourceHttp;
        $vars['publishBar'] = $this->scriptView('PublishBar');
        $vars['MediumEditorPack'] = $this->scriptView('MediumEditorPack', false);
        $vars['EntryForm'] = $this->scriptView('EntryForm', false);
        $vars['content'] = $this->prepareFormContent($entry->content);
        $vars['insert'] = "<script src='$insertSource'></script>";
        $vars['published'] = $entry->published ? 1 : 0;
        $vars['publishDate'] = $entry->publishDate;
        $vars['entryId'] = $entryId;
        $vars['title'] = $entry->title;
        $vars['tags'] = $entry->tags;
        
        $vars['status'] = $new ? 'Draft' : 'Last updated ' . $this->relativeTime($entry->updateDate);
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
        $entry->stamp();
        $content = $request->pullPutVar('content');
        $entry->setContent($content);

        $updateSummary = $entry->isEmpty('summary');
        $updateThumbnail = $entry->isEmpty('thumbnail');

        if (!$entry->isEmpty('content')) {
            $this->siftContent($entry, $updateSummary, $updateThumbnail);
        }

        self::saveResource($entry);
        return $entry->id;
    }

    /**
     * Tries to extract the title, first image, and summary from the entry's 
     * content variable. Title will always be updated but the thumbnail and summary
     * may be switched on or off by parameters.
     * 
     * @param Resource $entry
     * @param bool $updateSummary
     * @param bool $updateThumbnail
     */
    private function siftContent(Resource $entry, $updateSummary = true,
            $updateThumbnail = true)
    {
        $photoFactory = new EntryPhotoFactory();
        $content = $entry->content;
        libxml_use_internal_errors(true);
        $doc = new \DomDocument;
        $doc->loadHtml($content);
        $domlist = $doc->getElementsByTagName('*');
        $imageFound = false;
        $titleFound = false;
        $summaryFound = false;
        foreach ($domlist as $dom) {
            // we found everything we need. Stop looking.
            if ($imageFound && $titleFound && $summaryFound) {
                break;
            }
            switch ($dom->tagName) {
                case 'img':
                    if (!$updateThumbnail || $imageFound) {
                        break;
                    }
                    $entry->thumbnail = $this->createThumbnailUrl($dom->getAttribute('src'));
                    $imageFound = true;
                    break;

                case 'h3':
                    if (!$titleFound) {
                        $entry->title = $dom->textContent;
                        $titleFound = true;
                    }
                    break;

                case 'p':
                    if (!$titleFound) {
                        $entry->title = $dom->textContent;
                        $titleFound = true;
                    } elseif ($updateSummary && !$summaryFound) {
                        $entry->summary = $dom->textContent;
                        $summaryFound = true;
                    }
                    break;
            }
        }
    }

    private function createThumbnailUrl($url)
    {
        $urlArray = explode('/', $url);
        $filename = array_pop($urlArray);
        return implode('/', $urlArray) . '/thumbnail/' . $filename;
    }

    public function patch($entryId, Request $request)
    {
        $param = null;
        $value = null;

        $entry = $this->load($entryId);
        if ($request->patchVarIsset('values')) {
            $values = $request->pullPatchArray('values');
            foreach ($values as $val) {
                extract($val);
                $this->patchEntry($entry, $param, $value);
            }
        } else {
            $this->patchEntry($entry, $request->pullPatchString('param'),
                    $request->pullPatchVar('value'));
        }

        self::saveResource($entry);
        return $entry->id;
    }

    private function patchEntry(Resource $entry, $param, $value)
    {
        switch ($param) {
            default:
                $entry->$param = $value;
        }
    }

    public function data($id)
    {
        $entry = $this->load($id);
        return $entry->getStringVars(true);
    }

    public function delete($id)
    {
        $entry = $this->load($id);
        $entry->deleted = true;
        self::saveResource($entry);
    }

    public function purge($id)
    {
        $entry = $this->load($id);
        if (!$entry->deleted) {
            throw new \stories\Exception\CannotPurge;
        }
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
