<?php

/**
 * MIT License
 * Copyright (c) 2017 Electronic Student Services @ Appalachian State University
 * 
 * See LICENSE file in root directory for copyright and distribution permissions.
 * 
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 */

namespace stories\Resource;

if (!defined('STORIES_CONTENT_TAGS')) {
    require_once PHPWS_SOURCE_DIR . 'mod/stories/config/defines.dist.php';
}

class EntryResource extends BaseResource
{

    /**
     * Pulled from author table. Not saved in storiesentry
     * @var phpws2\Variable\Email
     */
    protected $authorEmail;

    /**
     * Profile id of author
     * @var phpws2\Variable\IntegerVar 
     */
    protected $authorId;

    /**
     * Pulled from author table. Not saved in storiesentry
     * @var phpws2\Variable\StringVar 
     */
    protected $authorName;

    /**
     * Pulled from author table. Not saved in storiesentry
     * @var phpws2\Variable\FileVar
     */
    protected $authorPic;

    /**
     * Main body content of story.
     * @var phpws2\Variable\StringVar 
     */
    protected $content;

    /**
     * Timestamp story was first created.
     * @var phpws2\Variable\DateTime 
     */
    protected $createDate;

    /**
     * Deletion flag. Should prevent display outside of administrative screen
     * @var phpws2\Variable\BooleanVar 
     */
    protected $deleted;

    /**
     * Last day to show story.
     * @var phpws2\Variable\IntegerVar 
     */
    protected $expirationDate;

    /**
     * Give precedence to this entry on feature block
     * @var phpws2\Variable\BooleanVar
     */
    protected $forceFeature;

    /**
     *
     * @var phpws2\Variable\SmallInteger
     */
    protected $imageOrientation;

    /**
     * Time after the story may be published.
     * @var phpws2\Variable\DateTime 
     */
    protected $publishDate;

    /**
     * Must be true for story to be shown.
     * @var phpws2\Variable\BooleanVar 
     */
    protected $published;

    /**
     * Summary text shown in feature box.
     * @var phpws2\Variable\StringVar 
     */
    protected $summary;

    /**
     * Smaller version of leadImage
     * @var phpws2\Variable\FileVar 
     */
    protected $thumbnail;

    /**
     * @var phpws2\Variable\FileVar
     */
    protected $leadImage;

    /**
     * Title of story.
     * @var phpws2\Variable\TextOnly
     */
    protected $title;

    /**
     * Last updated
     * @var phpws2\Variable\DateTime
     */
    protected $updateDate;

    /**
     *
     * @var phpws2\Variable\StringVar
     */
    protected $tags;

    /**
     * @var phpws2\Variable\TextOnly
     */
    protected $urlTitle;

    /**
     * 0 = summary view on list
     * 1 = full view on list
     * @var phpws2\Variable\SmallInteger
     */
    protected $listView;
    protected $url;
    protected $strippedSummary;
    
    protected $showInList;

    /**
     * @var string
     */
    protected $table = 'storiesentry';

    public function __construct()
    {
        parent::__construct();
        $this->authorEmail = new \phpws2\Variable\Email(null, 'authorEmail');
        $this->authorEmail->allowNull(true);
        $this->authorEmail->setIsTableColumn(false);
        $this->authorId = new \phpws2\Variable\IntegerVar(0, 'authorId');
        $this->authorName = new \phpws2\Variable\StringVar(null, 'authorName');
        $this->authorName->allowNull(true);
        $this->authorName->setIsTableColumn(false);
        $this->authorPic = new \phpws2\Variable\FileVar(null, 'authorPic');
        $this->authorPic->allowNull(true);
        $this->authorPic->setIsTableColumn(false);
        $this->content = new \phpws2\Variable\StringVar(null, 'content');
        $this->content->addAllowedTags(STORIES_CONTENT_TAGS);
        $this->createDate = new \phpws2\Variable\DateTime(0, 'createDate');
        $this->imageOrientation = new \phpws2\Variable\SmallInteger(0,
                'imageOrientation');
        $this->updateDate = new \phpws2\Variable\DateTime(0, 'updateDate');
        $this->deleted = new \phpws2\Variable\BooleanVar(false, 'deleted');
        $this->expirationDate = new \phpws2\Variable\DateTime(0,
                'expirationDate');
        $this->expirationDate->setPrintEmpty(false);
        $this->leadImage = new \phpws2\Variable\FileVar(null, 'leadImage');
        $this->leadImage->allowNull(true);
        $this->publishDate = new \phpws2\Variable\DateTime(0, 'publishDate');
        $this->publishDate->setFormat(null);
        $this->publishDate->setPrintEmpty(false);
        $this->published = new \phpws2\Variable\BooleanVar(false, 'published');
        $this->summary = new \phpws2\Variable\StringVar(null, 'summary');
        $this->summary->addAllowedTags(STORIES_SUMMARY_TAGS);
        $this->thumbnail = new \phpws2\Variable\FileVar(null, 'thumbnail');
        $this->thumbnail->allowNull(true);
        $this->title = new \phpws2\Variable\TextOnly(null, 'title', 100);
        $this->tags = new \phpws2\Variable\ArrayVar(null, 'tags');
        $this->tags->allowNull(true);
        $this->tags->setIsTableColumn(false);
        $this->urlTitle = new \phpws2\Variable\TextOnly(null, 'urlTitle', 100);
        $this->listView = new \phpws2\Variable\SmallInteger(0, 'listView');
        $this->url = new \phpws2\Variable\Url;
        $this->strippedSummary = new \phpws2\Variable\TextOnly;
        $this->showInList = new \phpws2\Variable\BooleanVar;
        $this->showInList->allowNull(true);

        $this->doNotSave(array('authorName', 'authorEmail', 'authorPic', 'tags', 'url', 'strippedSummary', 'showInList'));
    }

    public function setTitle($title)
    {
        $title = substr($title, 0, 99);
        $this->title->set($title);
        $this->urlTitle->set($this->processTitle($title));
    }

    public function setUrlTitle($title)
    {
        $title = substr($title, 0, 99);
        $this->urlTitle->set($this->processTitle($title));
    }

    public function getStringVars($return_null = null, $hide = null)
    {
        $holdSummary = false;
        $factory = new \stories\Factory\EntryFactory;
        $tagFactory = new \stories\Factory\TagFactory;
        if (is_array($hide) && in_array('summary', $hide)) {
            $holdSummary = true;
            unset($hide[array_search('summary', $hide)]);
        }
        $vars = parent::getStringVars($return_null, $hide);
        $vars['strippedSummary'] = strip_tags($vars['summary']);
        if ($holdSummary) {
            unset($vars['summary']);
        }

        if (empty($hide) || (is_array($hide) && !in_array('createDate', $hide))) {
            $vars['createDateRelative'] = $this->relativeTime($this->createDate->get());
        }
        
        if ($this->publishDate->get()) {
            $vars['publishDateRelative'] = $this->relativeTime($this->publishDate->get());
        }
        if (!is_array($hide) || !in_array('tags', $hide)) {
            $vars['tags'] = $tagFactory->getTagsByEntryId($this->id, true);
        }
        return $vars;
    }

    public function stamp()
    {
        $this->updateDate->stamp();
    }

    public function getPublishDate($format = null)
    {
        return $this->publishDate->get($format);
    }

    public function createStamp()
    {
        $this->createDate->stamp();
    }

    public function publishStamp()
    {
        $this->publishDate->stamp();
    }

    public function pastPublishDate()
    {
        return $this->publishDate->get(false) < time();
    }

    /**
     * Prepare the title for a url. Shortened to 240 characters to allow room for 
     * timestamped duplicates.
     * @param string $title
     * @return string
     */
    private function processTitle($title)
    {
        $title = preg_replace('/\s/', '-',
                strtolower(str_replace(' - ', '-', trim($title))));
        return substr(preg_replace('/[^\w\-]/', '', $title), 0, 240);
    }

    public function getUrl(bool $short = true, $relative = true)
    {
        $prefix = $relative ? './stories/' : PHPWS_HOME_HTTP . 'stories/';
        return $short ?
                $prefix . $this->urlTitle->get() :
                $prefix . 'Entry/' . $this->getId() . '/' . $this->urlTitle->get();
    }

    public function getStrippedSummary()
    {
        return strip_tags($this->summary->get());
    }

}
