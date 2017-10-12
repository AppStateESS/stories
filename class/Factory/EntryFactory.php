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

require_once PHPWS_SOURCE_DIR . 'mod/access/class/Shortcut.php';

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
        $entry = parent::load($id);
        if ($entry->deleted) {
            throw new ResourceNotFound;
        }
        $tagFactory = new TagFactory;
        $entry->tags = $tagFactory->getTagsByEntryId($id);
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
            'hideExpired' => true,
            'orderBy' => 'publishDate',
            'limit' => 3,
            'includeContent' => true,
            'publishedOnly' => true,
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
        $tbl->addField('updateDate');
        $tbl->addField('urlTitle');
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
        $entry->content = '<p class="medium-insert-active"><p>';
        $authorFactory = new AuthorFactory;
        $this->loadAuthor($entry, $authorFactory->getByCurrentUser(true));
        return self::saveResource($entry);
    }

    public function form(Resource $entry, $new = false)
    {
        $tagFactory = new TagFactory();

        $sourceHttp = PHPWS_SOURCE_HTTP;
        $insertSource = PHPWS_SOURCE_HTTP . 'mod/stories/javascript/MediumEditor/insert.js';
        $entryVars = $entry->getStringVars();
        $vars['cssOverride'] = $this->mediumCSSOverride();
        $entryId = $entry->id;
        $vars['home'] = $sourceHttp;
        $vars['entry'] = json_encode($entryVars);
        $vars['publishBar'] = $this->scriptView('PublishBar');
        $vars['tagBar'] = $this->scriptView('TagBar');
        $vars['MediumEditorPack'] = $this->scriptView('MediumEditorPack', false);
        $vars['EntryForm'] = $this->scriptView('EntryForm', false);
        $vars['content'] = $this->prepareFormContent($entry->content);
        $vars['insert'] = "<script src='$insertSource'></script>";
        $vars['tags'] = json_encode($tagFactory->listTags(true));

        $vars['status'] = $new ? 'Draft' : 'Last updated ' . $this->relativeTime($entry->updateDate);
        $template = new \phpws2\Template($vars);
        $template->setModuleTemplate('stories', 'Entry/Form.html');
        return $template->get();
    }

    public function getByUrlTitle($title)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesEntry');
        $tbl->addFieldConditional('urlTitle', $title);
        $data = $db->selectOneRow();
        if (empty($data)) {
            return null;
        }
        $entry = $this->build();
        $entry->setVars($data);
        return $entry;
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
        $this->siftContent($entry);
        return $this->save($entry);
    }

    public function save(Resource $entry)
    {
        $this->checkUrlTitle($entry);
        $this->saveShortcut($entry);
        self::saveResource($entry);
        return $entry->id;
    }

    /**
     * Access module is really old, but we need it so we can forward stories.
     * Shortcut doesn't have a factory so we tinker a little to get our results.
     * 1 load a shortcut with the matching url.
     * 2 a. If it exists, update the keyword.
     *   b. Not exists, create keyword
     * 
     * @param Resource $entry
     */
    private function saveShortcut(Resource $entry)
    {
        $shortcut = $this->getShortcutByUrl($entry);
        $db = Database::getDB();
        $tbl = $db->addTable('access_shortcuts');
        $tbl->usePearSequence(true);
        $tbl->addValue('keyword', $entry->urlTitle);
        if (empty($shortcut)) {
            $tbl->addValue('url', 'stories:' . $entry->id);
            return $db->insert();
        } else {
            $tbl->addFieldConditional('id', $shortcut['id']);
            return $db->update();
        }
    }

    private function checkUrlTitle(Resource $entry)
    {
        $duplicate = $this->getByUrlTitle($entry->urlTitle);
        $shortcut = $this->getShortcutByKeyword($entry);
        /**
         * no duplicate found or duplicate id is same as entry id
         * AND
         * no shortcut with matching urlTitle or shortcut url does not match the
         * entry id
         * 
         */
        if ((empty($duplicate) || $duplicate->id === $entry->id) &&
                (empty($shortcut) || $shortcut['url'] == "stories:{$entry->id}")) {
            return;
        } else {
            // duplicate found, update title with timestamp
            $entry->urlTitle = $entry->urlTitle . '-' . time();
        }
    }

    public function getShortcutByUrl($entry)
    {
        $url = 'stories:' . $entry->id;
        $db = Database::getDB();
        $tbl = $db->addTable('access_shortcuts');
        $tbl->addFieldConditional('url', $url);
        return $db->selectOneRow();
    }

    public function getShortcutByKeyword($entry)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('access_shortcuts');
        $tbl->addFieldConditional('keyword', $entry->urlTitle);
        return $db->selectOneRow();
    }

    /**
     * Tries to extract the title, first image, and summary from the entry's 
     * content variable. 
     * 
     * @param Resource $entry
     */
    private function siftContent(Resource $entry)
    {
        $photoFactory = new EntryPhotoFactory();
        $content = $entry->content;
        libxml_use_internal_errors(true);
        $doc = new \DomDocument;
        $doc->loadHtml($content);
        $doc->preserveWhiteSpace = false;

        $image = $doc->getElementsByTagName('img');
        $h3 = $doc->getElementsByTagName('h3');
        $h4 = $doc->getElementsByTagName('h4');
        $p = $doc->getElementsByTagName('p');

        $pStart = 0;

        if ($image->length > 0) {
            $imgNode = $image->item(0);
            $src = $imgNode->getAttribute('src');
            $entry->leadImage = $src;
            $entry->thumbnail = $this->createThumbnailUrl($src);
        }

        if ($h3->length > 0) {
            $h3Node = $h3->item(0);
            $entry->title = $h3Node->textContent;
        } elseif ($h4->length > 0) {
            $h4Node = $h3->item(0);
            $entry->title = $h4Node->textContent;
        } elseif ($p->length > 0) {
            $pNode = $p->item(0);
            $entry->title = $pNode->textContent;
            $pStart = 1;
        }

        if ($p->length > $pStart) {
            $pNode = $p->item($pStart);
            $pHtml = $doc->saveHtml($pNode);
            $entry->summary = $pHtml;
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
                $param = $value = null;
                $this->patchEntry($entry, $param, $value);
            }
        } else {
            $param = $request->pullPatchString('param');
            $value = $request->pullPatchVar('value');
            $this->patchEntry($entry, $param, $value);
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

    public function data($id, $publishOnly = true)
    {
        $entry = $this->load($id);
        if ($publishOnly && (!$entry->published && $entry->publishDate < time())) {
            return null;
        }
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

    public function view($id, $isAdmin = false)
    {
        try {
            $data = $this->data($id, !$isAdmin);
            if (empty($data)) {
                throw new ResourceNotFound;
            }
            $data['cssOverride'] = $this->mediumCSSOverride();
            $data['isAdmin'] = $isAdmin;
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

    public function showStories(Request $request)
    {
        $list = $this->pullList();
        if (empty($list)) {
            return null;
        }

        $settings = new \phpws2\Setting;
        //listStoryFormat  - 0 is summary, 1 full
        $templateFile = $settings->get('stories', 'listStoryFormat') ? 'FrontPageFull.html' : 'FrontPageSummary';

        \Layout::addToStyleList('mod/stories/css/front-page.css');
        $data['list'] = $list;
        $data['style'] = StoryMenu::mediumCSSLink() . $this->mediumCSSOverride();
        $template = new \phpws2\Template($data);

        $template->setModuleTemplate('stories', 'FrontPageSummary.html');
        return $template->get();
    }

    public function showFeatures(Request $request)
    {
        \Layout::addToStyleList('mod/stories/css/front-page.css');
        $options = array('includeContent' => false);
        $list = $this->pullList($options);
        $template = new \phpws2\Template(array('list' => $list));
        $template->setModuleTemplate('stories', 'FeatureList.html');
        return $template->get();
    }

}
