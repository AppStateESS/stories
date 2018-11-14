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

use phpws2\Database;
use phpws2\Settings;
use Canopy\Request;
use stories\Resource\AuthorResource;
use stories\Resource\EntryResource as Resource;
use stories\Resource\ThumbnailResource;
use stories\Factory\AuthorFactory;
use stories\Factory\TagFactory;
use stories\Factory\PublishFactory;
use stories\Exception\MissingInput;
use stories\Exception\ResourceNotFound;

require_once PHPWS_SOURCE_DIR . 'mod/access/class/Shortcut.php';

if (!defined('STORIES_HARD_LIMIT')) {
    define('STORIES_HARD_LIMIT', 100);
}

if (!defined('STORIES_SUMMARY_CHARACTER_LIMIT')) {
    define('STORIES_SUMMARY_CHARACTER_LIMIT', 500);
}

class EntryFactory extends BaseFactory
{

    public $more_rows = false;

    public function build()
    {
        return new Resource;
    }

    /**
     * 
     * @param type $id
     * @return \stories\Resource\EntryResource
     */
    public function load($id, $allowDeleted = false)
    {
        $db = Database::getDB();
        $entryTbl = $db->addTable('storiesentry');
        $authorTbl = $db->addTable('storiesauthor');
        $authorTbl->addField('name', 'authorName');
        $authorTbl->addField('pic', 'authorPic');
        $authorTbl->addField('email', 'authorEmail');
        $db->joinResources($entryTbl, $authorTbl,
                $db->createConditional($entryTbl->getField('authorId'),
                        $authorTbl->getField('id')), 'left');
        $entryTbl->addFieldConditional(('id'), $id);
        $entry = $this->build();
        $db->selectInto($entry);

        if (!$allowDeleted && $entry->deleted) {
            throw new ResourceNotFound;
        }
        $tagFactory = new TagFactory;
        $entry->tags = $tagFactory->getTagsByEntryId($id);

        $authorFactory = new AuthorFactory;
        return $entry;
    }

    private function defaultListOptions()
    {
        return array(
            'hideExpired' => true,
            'sortBy' => 'publishDate',
            'limit' => 10,
            'includeContent' => true,
            'publishedOnly' => true,
            'showAuthor' => false,
            'offset' => 0,
            'page' => 1,
            'tag' => null,
            'vars' => null,
            'titleRequired' => false,
            'mustHaveThumbnail' => false,
            'showTagLinks' => true);
    }

    /**
     * Return a list of entries based on options
     * 
     * Options:
     * hideExpired: [true] Don't show expired entries
     * sortBy: [publishDate] Which column to order by
     * limit: [6] Total number of entries to pull
     * includeContent: [true] Include content in the pull 
     * publishedOnly: [true] Only show published entries
     * showAuthor: [false] Show the author information
     * offset: [0] Current number of offsets using limit
     * tag: [null] Limit by tag association
     * vars: [null] If array, only pull these variables
     * mustHaveThumbnail: [false] Only pull entries that have an associate thumbnail
     * showTagLinks: [true] Pull tags links for entries
     * page: page number of rows. Translated to offset
     * 
     * 
     * @param array $options
     * @return array
     */
    public function pullList(array $options = null)
    {
        $db = Database::getDB();
        // if true, don't add fields to the query
        $limitedVars = false;
        $now = time();
        $defaultOptions = $this->defaultListOptions();

        if (is_array($options)) {
            $options = array_merge($defaultOptions, $options);
        } else {
            $options = $defaultOptions;
        }

        $tbl = $db->addTable('storiesentry');
        if (!empty($options['vars'])) {
            $limitedVars = true;
            foreach ($options['vars'] as $field) {
                $tbl->addField($field);
            }
        } else {
            $tbl->addField('id');
            $tbl->addField('imageOrientation');
            $tbl->addField('createDate');
            $tbl->addField('expirationDate');
            $tbl->addField('publishDate');
            $tbl->addField('published');
            $tbl->addField('summary');
            $tbl->addField('thumbnail');
            $tbl->addField('title');
            $tbl->addField('updateDate');
            $tbl->addField('urlTitle');
            $tbl->addField('leadImage');
            $tbl->addField('authorId');
            if ($options['includeContent']) {
                $tbl->addField('content');
            }
        }

        if ($options['titleRequired']) {
            $tbl->addFieldConditional('title', '', '!=');
        }

        //conditionals
        if ($options['mustHaveThumbnail'] === true) {
            $tbl->addFieldConditional('thumbnail', null, 'is not');
        }

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
            $db->addConditional($db->createConditional($expire1, $expire2, 'or'));
        }

        if ($options['tag']) {
            $tagIdTable = $db->addTable('storiestagtoentry', null, false);
            $tagTable = $db->addTable('storiestag', null, false);
            $tagTable->addFieldConditional('title', $options['tag']);
            $db->addConditional($db->createConditional($tagTable->getField('id'),
                            $tagIdTable->getField('tagId'), '='));
            $db->addConditional($db->createConditional($tbl->getField('id'),
                            $tagIdTable->getField('entryId'), '='));
        }

        if (!$limitedVars && $options['showAuthor']) {
            $tbl2 = $db->addTable('storiesauthor');

            $tbl2->addField('name', 'authorName');
            $tbl2->addField('email', 'authorEmail');
            $tbl2->addField('pic', 'authorPic');
            $db->joinResources($tbl, $tbl2,
                    $db->createConditional($tbl->getField('authorId'),
                            $tbl2->getField('id')), 'left');
        }
        if (isset($options['sortBy'])) {
            $tbl->addOrderBy($options['sortBy'],
                    $options['sortBy'] === 'title' ? 'asc' : 'desc');
        }

        if ($options['offset'] < 1 && (int) $options['page'] > 1) {
            $options['offset'] = ((int) $options['page'] - 1) * $options['limit'];
        }

        /**
         * To get an accurate test to see if there are more entries for 
         * a Next page button, we ask for one more row than the current limit
         */
        if ($options['limit'] != 0) {
            if ($options['limit'] > STORIES_HARD_LIMIT) {
                $limit = STORIES_HARD_LIMIT;
            } else {
                $limit = ((int) $options['limit']) + 1;
                if (isset($options['offset'])) {
                    $db->setLimit($limit, $options['offset']);
                } else {
                    $db->setLimit($limit);
                }
            }
        }


        // limitedVars was not well thought out. used mostly for features
        // story choice which only uses title and id
        if ($limitedVars) {
            return $db->select();
        }
        $objectList = $db->selectAsResources('\stories\Resource\EntryResource');

        if (empty($objectList)) {
            return null;
        }

        $totalRows = count($objectList);
        if ($totalRows > $options['limit']) {
            // if there are more rows than the options[limit], we set more_rows
            // to true and pop the extra (remember we asked for one extra row)
            // off the end.
            //
            $this->more_rows = true;
            array_pop($objectList);
        } else {
            $this->more_rows = false;
        }

        $tagFactory = new TagFactory;
        $address = \Canopy\Server::getSiteUrl();
        foreach ($objectList as $entry) {
            $row = $entry->getStringVars();

            $row['currentUrl'] = $address . 'stories/Entry/' . $row['urlTitle'];
            if ($options['showTagLinks']) {
                $row['tagLinks'] = $tagFactory->getTagLinks($row['tags'],
                        $row['id'], $options['tag']);
            }
            $listing[] = $row;
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
        $entry->createStamp();
        $entry->publishStamp();
        $authorFactory = new AuthorFactory;
        $this->loadAuthor($entry, $authorFactory->getByCurrentUser(true));
        return self::saveResource($entry);
    }

    /**
     * Pulls an entry by the urlTitle or null if not found
     * @param string $urlTitle
     * @return \stories\Resource\EntryResource
     */
    public function getByUrlTitle($urlTitle)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesentry');
        $tbl->addFieldConditional('urlTitle', $urlTitle);
        $data = $db->selectOneRow();
        if (empty($data)) {
            return null;
        }
        $entry = $this->build();
        $entry->setVars($data);
        return $entry;
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
        $content = $this->filterMedium($content);

        if (empty($content)) {
            $this->clearOutEntry($entry);
        } else {
            $entry->content = $content;
            $this->siftContent($entry);
        }

        // Empty title does not allow a published story.
        if (empty($entry->title)) {
            $entry->published = false;
        }
        return $this->save($entry);
    }

    private function relativeImages($content)
    {
        return preg_replace('@src="https?://[\w:/]+(images/stories/\d+/[^"]+)"@',
                'src="./$1"', $content);
    }

    private function clearOutEntry(Resource $entry)
    {
        $this->title = '';
        $this->content = '';
        $this->summary = '';
        $photoFactory = new EntryPhotoFactory();
        $photoFactory->purgeEntry($entry->id);
        $this->leadImage = null;
        $this->thumbnail = null;
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
        $url = 'stories:' . $entry->id;
        $db = Database::getDB();
        $tbl = $db->addTable('access_shortcuts');
        $tbl->addFieldConditional('url', $url);
        $tbl->addField('id');
        $shortcutId = $db->selectColumn();
        $db->clearConditional();
        if (empty($shortcutId)) {
            $tbl->usePearSequence(true);
            $tbl->addValue('keyword', $entry->urlTitle);
            $tbl->addValue('url', 'stories:' . $entry->id);
            $tbl->addValue('active', 1);
            return $db->insert();
        } else {
            $tbl->addValue('keyword', $entry->urlTitle);
            $tbl->addFieldConditional('id', $shortcutId);
            return $db->update();
        }
    }

    /**
     * Checks for duplicate urlTitle entry or a shortcut with the same keyword.
     * 
     * @param Resource $entry
     * @return boolean
     */
    private function checkUrlTitle(Resource $entry)
    {
        $duplicate = $this->getByUrlTitle($entry->urlTitle);
        $shortcut = $this->getShortcutByKeyword($entry->urlTitle);
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
            $entry->urlTitle = $entry->urlTitle . '-' . $entry->id;
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

    public function getShortcutByKeyword($keyword)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('access_shortcuts');
        $tbl->addFieldConditional('keyword', $keyword);
        return $db->selectOneRow();
    }

    /**
     * Tries to extract the title, first image, and summary from the entry's 
     * content variable.  If first image is not found but a youtube video is
     * used, the video thumbnail will get extracted.
     * 
     * @param Resource $entry
     */
    public function siftContent(Resource $entry)
    {
        $photoFactory = new EntryPhotoFactory();
        $content = $entry->content;
        $content = str_replace('<br>', "\r\n", $content);
        libxml_use_internal_errors(true);
        $doc = new \DomDocument;
        $doc->loadHtml(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        $doc->preserveWhiteSpace = false;

        $image = $doc->getElementsByTagName('img');
        $h3 = $doc->getElementsByTagName('h3');
        $h4 = $doc->getElementsByTagName('h4');
        $p = $doc->getElementsByTagName('p');
        $iframe = $doc->getElementsByTagName('iframe');

        $pStart = 0;

        if ($image->length > 0) {
            $imgNode = $image->item(0);
            $src = $imgNode->getAttribute('src');
            $imageDirectory = $photoFactory->getImagePath($entry->id);
            $imageFile = $photoFactory->getImageFilename($src);
            $entry->leadImage = $imageDirectory . $imageFile;
            if ($entry->leadImage) {
                $thumbnail = $photoFactory->createThumbnail($imageDirectory,
                        $imageFile);
                if ($thumbnail !== false) {
                    $entry->thumbnail = $thumbnail;
                }
            }
        } elseif ($iframe->length > 0) {
            $iframeNode = $iframe->item(0);
            $src = $iframeNode->getAttribute('src');
        }

        $titleFound = false;
        // Give up after searching 5 blank tags
        $titleCount = 0;
        while (!$titleFound && $titleCount < 5) {
            if ($h3->length > 0) {
                $h3Node = $h3->item($titleCount);
                if ($h3Node) {
                    $title = substr(trim($h3Node->textContent), 0, 100);
                }
            } elseif ($h4->length > 0) {
                $h4Node = $h4->item($titleCount);
                if ($h4Node) {
                    $title = substr(trim($h4Node->textContent), 0, 100);
                }
            } elseif ($p->length > 0) {
                $pNode = $p->item($titleCount);
                if ($pNode) {
                    $title = substr(trim($pNode->textContent), 0, 100);
                    $pStart = $titleCount + 1;
                }
            }
            if (!empty($title)) {
                $titleFound = true;
                $entry->title = $title;
            }
            $titleCount++;
        }
        if ($titleFound == false) {
            $entry->title = '';
        }

        $summaryFound = false;
        $summaryCount = $pStart;
        $summaryLimit = $p->length - $pStart;

        $totalCharacters = 0;
        while (!$summaryFound && $summaryCount <= $summaryLimit) {
            if ($p->length > $summaryCount) {
                $pNode = $p->item($summaryCount);
                if (preg_match('/^::summary/', $pNode->textContent)) {
                    $summaryFound = true;
                    break;
                }
                $totalCharacters += strlen($pNode->textContent);
                $pContent = $pNode->C14N();
                if (!empty($pContent)) {
                    $summary[] = $pContent;
                }
                if ($totalCharacters > STORIES_SUMMARY_CHARACTER_LIMIT) {
                    $summaryFound = true;
                    break;
                }
            }
            $summaryCount++;
        }
        if (empty($summary)) {
            $entry->summary = '';
        } else {
            $entry->summary = implode('', $summary);
        }
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
                $this->patchEntry($entry, $val['param'], $val['value']);
            }
        } else {
            $param = $request->pullPatchString('param');
            $value = $request->pullPatchVar('value');
            $this->patchEntry($entry, $param, $value);
        }
        $entry->stamp();
        self::saveResource($entry);
        return $entry->id;
    }

    private function patchEntry(Resource $entry, $param, $value)
    {
        $publishFactory = new PublishFactory;
        switch ($param) {
            case 'published':
                if ($value == '0') {
                    $publishFactory->unpublishEntry($entry->id);
                } else {
                    $publishFactory->publishEntry($entry->id,
                            $entry->publishDate);
                }

            default:
                $entry->$param = $value;
        }
    }

    /**
     * An abridged array of information about an entry used for sharing.
     * @param Resource $entry
     * @return array
     */
    public function shareData(Resource $entry)
    {
        return $entry->getStringVars(true,
                        ['authorEmail', 'authorId', 'content', 'deleted', 'expirationDate', 'leadImage', 'updateDate', 'createDateRelative', 'createDate', 'published']);
    }

    public function data(Resource $entry, $publishOnly = true)
    {
        if ($publishOnly && (!$entry->published && $entry->publishDate < time())) {
            return null;
        }
        return $entry->getStringVars(true);
    }

    /**
     * Flips the deleted flag on the entry. Tags are not touched as the 
     * entry will not be pulled. Features will be purged however.
     * @param integer $id
     */
    public function delete($id)
    {
        $publishFactory = new PublishFactory;
        $featureStoryFactory = new FeatureStoryFactory;
        
        $entry = $this->load($id);
        $entry->deleted = true;

        self::saveResource($entry);
        $publishFactory->unpublishEntry($entry->id);
    }

    public function purge($id)
    {
        $entry = $this->load($id, true);
        if (!$entry->deleted) {
            throw new \stories\Exception\CannotPurge;
        }
        $db = Database::getDB();
        $tbl = $db->addTable('storiesentry');
        $tbl->addFieldConditional('id', $id);
        $db->delete();
        $photoFactory = new EntryPhotoFactory();
        $photoFactory->purgeEntry($id);
        $tagFactory = new TagFactory;
        $tagFactory->purgeEntry($id);
        $this->removeShortcut($entry);
    }

    private function removeShortcut($entry)
    {
        $shortcutUrl = 'stories:' . $entry->id;

        $db = Database::getDB();
        $tbl = $db->addTable('access_shortcuts');
        $tbl->addFieldConditional('url', $shortcutUrl);
        $db->delete();
    }

    private function tooltipScript()
    {
        return <<<EOF
<script src="mod/stories/javascript/Tooltip/index.js"></script>
EOF;
    }

    /**
     * Removes the media overlay that prevents the video from working
     */
    private function removeMediumOverlay($content)
    {
        return str_replace('<div class="medium-insert-embeds-overlay"></div>',
                '', $content);
    }


    /**
     * The Flickr oEmbed calls a script when pulled down. This script instantly
     * creates an iframe to replace the anchor tag. The iframe is useless;
     * the script and the anchor tag called prior are the important components.
     * 
     * The true code is stuck in the data-embed-code div. This script grabs the 
     * embed code and saves it as content
     * before it is flushed. This only happens when Flickr is present. All other
     * oembed scripts behave.
     * @param string $content
     */
    private function cleanFlickr($content)
    {
        $matches = [];
        $embedString = '/<div data-embed-code="(&amp;lt;a data-flickr-embed=.*)"><iframe/';
        preg_match($embedString, $content, $matches);
        //Flickr not found
        if (empty($matches)) {
            return $content;
        }
        $embedCode = html_entity_decode(html_entity_decode($matches[1]));
        $final = preg_replace('/<div data-embed-code=".*<\/div>/',
                "<div class=\"medium-insert-active medium-flickr-embed\">$embedCode</div>",
                $content);
        return $final;
    }


    private function removeExtraParagraphs($content)
    {
        return preg_replace('/(<p class="">(<br>)?<\/p>)\n?|(<p class="medium-insert-active">(<br>)?<\/p>|<p>(<br>)?<\/p>)\n?/',
                '', $content);
    }

    /**
     * Removes contenteditable="true" attribute from any elements.
     * @param string $content
     * @return string
     */
    private function removeEditable($content)
    {
        return str_replace(' contenteditable="true"', '', $content);
    }

    /**
     * Cleans up the content string that is imported from Medium Editor.
     * Medium editor content contains remnants of its controls.
     * @param string $content
     */
    public function filterMedium($content)
    {
        // Removes medium buttons
        $content = trim(preg_replace('/<(div|p) class="medium-insert-buttons".*/s',
                        '', $content));
        $content = str_replace(' class=""', '', $content);

        $content = str_replace('Type caption for image (optional)', '', $content);
        $content = str_replace('Type caption (optional)', '', $content);
        // Removes extra medium paragraphs padded to end of content
        $content = $this->removeExtraParagraphs($content);
        // Removes extra headers sometimes padded on end
        $content = preg_replace('/<h[34]>\s+<\/h[34]>/', '', $content);
        // Removes the overlay left on embeds (e.g. youtube)
        $content = $this->removeMediumOverlay($content);
        // Removed http:// url from images making them relative
        $content = $this->relativeImages($content);
        $content = $this->cleanFlickr($content);
        //$content = $this->cleanTwitter($content);
        // Removed the contenteditable="true" left in the figcaption
        // (or anywhere else)
        $content = $this->removeEditable($content);
        // clear extra spaces
        $content = preg_replace('/>\s{2,}</', '> <', $content);
        $content = str_replace("\n", '', $content);
        return $content;
    }

}
