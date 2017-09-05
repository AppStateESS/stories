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
     * Give precedence to this entry on feature block
     * @var phpws2\Variable\BooleanVar
     */
    protected $forceFeature;

    /**
     * Title of story.
     * @var phpws2\Variable\TextOnly
     */
    protected $title;

    /**
     * @var string
     */
    protected $table = 'storiesEntry';

    public function __construct()
    {
        parent::__construct();
        $this->authorEmail = new \phpws2\Variable\Email(null, 'authorEmail');
        $this->authorEmail->allowEmpty(true);
        $this->authorId = new \phpws2\Variable\IntegerVar(0, 'authorId');
        $this->authorName = new \phpws2\Variable\StringVar(null, 'authorName');
        $this->authorName->allowEmpty(true);
        $this->authorPic = new \phpws2\Variable\FileVar(null, 'authorPic');
        $this->authorPic->allowEmpty(true);
        $this->content = new \phpws2\Variable\StringVar(null, 'content');
        $this->content->addAllowedTags(STORIES_CONTENT_TAGS);
        $this->createDate = new \phpws2\Variable\DateTime(0, 'createDate');
        $this->deleted = new \phpws2\Variable\BooleanVar(false, 'deleted');
        $this->expirationDate = new \phpws2\Variable\DateTime(0,
                'expirationDate');
        $this->publishDate = new \phpws2\Variable\DateTime(0, 'publishDate');
        $this->published = new \phpws2\Variable\BooleanVar(false, 'published');
        $this->summary = new \phpws2\Variable\StringVar(null, 'summary');
        $this->summary->addAllowedTags(STORIES_SUMMARY_TAGS);
        $this->thumbnail = new \phpws2\Variable\FileVar(null, 'thumbnail');
        $this->thumbnail->allowNull(true);
        $this->title = new \phpws2\Variable\TextOnly(null, 'title', 255);

        $this->doNotSave(array('authorName', 'authorEmail', 'authorPic'));
    }

}
