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
use stories\Resource\ThumbnailResource;
use stories\Factory\AuthorFactory;
use stories\Factory\TagFactory;
use stories\Resource\AuthorResource;
use stories\Exception\MissingInput;
use stories\Exception\ResourceNotFound;
use phpws2\Database;
use phpws2\Settings;
use Canopy\Request;

require_once PHPWS_SOURCE_DIR . 'mod/access/class/Shortcut.php';

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
        return array('publishedOnly' => false,
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
        $limit = ((int) $options['limit']) + 1;

        if (isset($options['offset'])) {
            $db->setLimit($limit, $options['offset']);
        } else {
            $db->setLimit($limit);
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

    public function changeOrientation(int $entryId, int $orientation)
    {
        $entry = $this->load($entryId);
        $entry->stamp();
        $entry->imageOrientation = $orientation;
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
            if ($src) {
                $imgResult = $photoFactory->saveYouTubeImage($entry->id, $src);
                if (!empty($imgResult)) {
                    $entry->leadImage = $imgResult['image'];
                    $entry->thumbnail = $imgResult['thumbnail'];
                }
            }
        }

        $titleFound = false;
        // Give up after searching 5 blank tags
        $titleCount = 0;
        while (!$titleFound && $titleCount < 5) {
            if ($h3->length > 0) {
                $h3Node = $h3->item($titleCount);
                if ($h3Node) {
                    $title = substr(trim($h3Node->textContent), 0, 255);
                }
            } elseif ($h4->length > 0) {
                $h4Node = $h4->item($titleCount);
                if ($h4Node) {
                    $title = substr(trim($h4Node->textContent), 0, 255);
                }
            } elseif ($p->length > 0) {
                $pNode = $p->item($titleCount);
                if ($pNode) {
                    $title = substr(trim($pNode->textContent), 0, 255);
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

        while (!$summaryFound && $summaryCount <= $summaryLimit) {
            if ($p->length > $summaryCount) {
                $pNode = $p->item($summaryCount);
                $pContent = trim($pNode->textContent);
                if (!empty($pContent)) {
                    $pContent = nl2br($pContent);
                    $entry->summary = "<p>$pContent</p>";
                    $summaryFound = true;
                }
            }
            $summaryCount++;
        }
        if ($summaryFound == false) {
            $entry->summary = '';
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

        self::saveResource($entry);
        return $entry->id;
    }

    private function patchEntry(Resource $entry, $param, $value)
    {
        switch ($param) {
            case 'published':
                if ($value == '0') {
                    $featureFactory = new FeatureFactory();
                    $featureFactory->removeEntryFromAll($entry->id);
                }

            default:
                $entry->$param = $value;
        }
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
        $entry = $this->load($id);
        $entry->deleted = true;

        // Feature will bug out if the entry is deleted
        $featureFactory = new FeatureFactory;
        $featureFactory->removeEntryFromAll($id);
        self::saveResource($entry);
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

    public function notFound()
    {
        $template = new \phpws2\Template();
        $template->setModuleTemplate('stories', 'Entry/NotFound.html');
        return $template->get();
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
     * Removed the data-embed-code div medium editor adds.
     * @param string $content
     * @return string
     */
    private function cleanEmbed($content)
    {
        return preg_replace('/<div data-embed-code="[^"]+">/s',
                '<div class="medium-insert-active">', $content);
    }

    /**
     * Facebook does zeroes out the container_width
     * @param string $content
     */
    private function cleanFacebook($content)
    {
        return preg_replace('/container_width=0/s', 'container_width=500px',
                $content);
    }

    private function cleanTwitter($content)
    {
        return str_replace('<script async="" src="//platform.twitter.com/widgets.js" charset="utf-8"></script>',
                '', $content);
    }

    private function removeExtraParagraphs($content)
    {
        return preg_replace('/(<p class="">(<br>)?<\/p>)\n?|(<p class="medium-insert-active">(<br>)?<\/p>|<p>(<br>)?<\/p>)\n/',
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
        $content = str_replace('Type caption for image (optional)', '', $content);
        // Removes extra medium paragraphs padded to end of content
        $content = $this->removeExtraParagraphs($content);
        // Removes extra headers sometimes padded on end
        $content = preg_replace('/<h[34]>\s+<\/h[34]>/', '', $content);
        // Removes the overlay left on embeds (e.g. youtube)
        $content = $this->removeMediumOverlay($content);
        // Removed http:// url from images making them relative
        $content = $this->relativeImages($content);
        $content = $this->cleanEmbed($content);
        $content = $this->cleanFacebook($content);
        $content = $this->cleanTwitter($content);
        // Removed the contenteditable="true" left in the figcaption
        // (or anywhere else)
        $content = $this->removeEditable($content);
        // clear extra spaces
        $content = preg_replace('/>\s{2,}</', '> <', $content);
        $content = str_replace("\n", '', $content);
        return $content;
    }

}
