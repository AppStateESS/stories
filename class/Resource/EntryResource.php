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
     * Pulled from author table. Not saved in storiesEntry
     * @var phpws2\Variable\Email
     */
    protected $authorEmail;

    /**
     * Profile id of author
     * @var phpws2\Variable\IntegerVar 
     */
    protected $authorId;

    /**
     * Pulled from author table. Not saved in storiesEntry
     * @var phpws2\Variable\StringVar 
     */
    protected $authorName;

    /**
     * Pulled from author table. Not saved in storiesEntry
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
     * @var phpws2\Variable\IntegerVar 
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
     * Time after the story may be published.
     * @var phpws2\Variable\IntegerVar 
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
     * Image shown in feature box.
     * @var phpws2\Variable\FileVar 
     */
    protected $thumbnail;

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
     * @var string
     */
    protected $table = 'storiesEntry';

    public function __construct()
    {
        parent::__construct();
        $this->authorEmail = new \phpws2\Variable\Email(null, 'authorEmail');
        $this->authorEmail->allowEmpty(true);
        $this->authorEmail->setIsTableColumn(false);
        $this->authorId = new \phpws2\Variable\IntegerVar(0, 'authorId');
        $this->authorName = new \phpws2\Variable\StringVar(null, 'authorName');
        $this->authorName->allowEmpty(true);
        $this->authorName->setIsTableColumn(false);
        $this->authorPic = new \phpws2\Variable\FileVar(null, 'authorPic');
        $this->authorPic->allowNull(true);
        $this->authorPic->setIsTableColumn(false);
        $this->content = new \phpws2\Variable\StringVar(null, 'content');
        $this->content->addAllowedTags(STORIES_CONTENT_TAGS);
        $this->createDate = new \phpws2\Variable\DateTime(0, 'createDate');
        $this->createDate->stamp();
        $this->updateDate = new \phpws2\Variable\DateTime(0, 'updateDate');
        $this->updateDate->stamp();
        $this->deleted = new \phpws2\Variable\BooleanVar(false, 'deleted');
        $this->expirationDate = new \phpws2\Variable\DateTime(0,
                'expirationDate');
        $this->expirationDate->setPrintEmpty(false);
        $this->publishDate = new \phpws2\Variable\DateTime(0, 'publishDate');
        $this->publishDate->stamp();
        $this->publishDate->setFormat(null);
        $this->publishDate->setPrintEmpty(false);
        $this->published = new \phpws2\Variable\BooleanVar(false, 'published');
        $this->summary = new \phpws2\Variable\StringVar(null, 'summary');
        $this->summary->addAllowedTags(STORIES_SUMMARY_TAGS);
        $this->thumbnail = new \phpws2\Variable\FileVar(null, 'thumbnail');
        $this->thumbnail->allowNull(true);
        $this->title = new \phpws2\Variable\TextOnly(null, 'title', 255);

        $this->doNotSave(array('authorName', 'authorEmail', 'authorPic'));
    }

    /**
     * Runs the filter before setting the content.
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content->set($this->filterContent($content));
    }

    /**
     * Cleans up the content string that is imported from Medium Editor.
     * Medium editor content contains remnants of its controls.
     * @param string $content
     */
    public function filterContent($content)
    {
        $noControls = trim(preg_replace('/<(div|p) class="medium-insert-buttons".*/s', '', $content));
        $noExtraParagraphs = preg_replace('/(<p class=""><\/p>){2,}|(<p class="medium-insert-active"><\/p>)$/s', '<p class="medium-insert-active"></p>', $noControls);
        return $noExtraParagraphs;
    }
    
    public function getStringVars($return_null = false, $hide = null)
    {
        $vars = parent::getStringVars($return_null, $hide);
        $vars['createDateRelative'] = $this->relativeTime($this->createDate->get());
        $vars['publishDateRelative'] = $this->relativeTime($this->publishDate->get());
        return $vars;
    }
    
    public function stamp()
    {
        $this->updateDate->stamp();
    }

}
