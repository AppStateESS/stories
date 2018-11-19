<?php

/**
 * MIT License
 * Copyright (c) 2018 Electronic Student Services @ Appalachian State University
 * 
 * See LICENSE file in root directory for copyright and distribution permissions.
 * 
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @license https://opensource.org/licenses/MIT
 */

namespace stories\Resource;

class FeatureStoryResource extends BaseResource
{

    /**
     * @var \phpws2\Variable\TextOnly
     */
    protected $title;

    /**
     * @var \phpws2\Variable\TextOnly
     */
    protected $summary;

    /**
     * @var \phpws2\Variable\TextOnly
     */
    protected $thumbnail;

    /**
     * @var \phpws2\Variable\Integer
     */
    protected $publishId;

    /**
     * @var \phpws2\Variable\DateTime
     */
    protected $eventDate;

    /**
     * @var \phpws2\Variable\StringVar
     */
    protected $url;

    /**
     * @var \phpws2\Variable\IntegerVar
     */
    protected $featureId;

    /**
     * @var \phpws2\Variable\SmallInteger
     */
    protected $x;

    /**
     * @var \phpws2\Variable\SmallInteger
     */
    protected $y;

    /**
     * @var \phpws2\Variable\SmallInteger
     */
    protected $zoom;

    /**
     * @var \phpws2\Variable\SmallInteger
     */
    protected $sorting;

    /**
     * @var phpws2\Variable\StringVar
     */
    protected $authorPic;

    /**
     * @var phpws2\Variable\StringVar
     */
    protected $authorName;

    /**
     * @var phpws2\Variable\DateTime
     */
    protected $publishDate;
    
    /**
     * @var phpws2\Variable\IntegerVar
     */
    protected $shareId;
    
    protected $table = 'storiesfeaturestory';

    public function __construct()
    {
        parent::__construct();
        $this->title = new \phpws2\Variable\TextOnly(null, 'title', 100);
        $this->summary = new \phpws2\Variable\TextOnly(null, 'summary', 300);
        $this->thumbnail = new \phpws2\Variable\TextOnly(null, 'thumbnail', 100);
        $this->url = new \phpws2\Variable\StringVar(null, 'url', 200);
        $this->authorPic = new \phpws2\Variable\StringVar(null, 'authorPic', 100);
        $this->authorPic->allowNull(true);
        $this->authorName = new \phpws2\Variable\StringVar(null, 'authorName',
                100);
        $this->authorName->allowNull(true);
        $this->eventDate = new \phpws2\Variable\DateTime(0, 'eventDate');
        $this->eventDate->setFormat(null);
        $this->featureId = new \phpws2\Variable\IntegerVar(0, 'featureId');
        $this->publishId = new \phpws2\Variable\IntegerVar(0, 'publishId');
        $this->publishDate = new \phpws2\Variable\DateTime(0, 'publishDate');
        $this->publishDate->setFormat(null);
        $this->shareId = new \phpws2\Variable\IntegerVar(0, 'shareId');
        $this->x = new \phpws2\Variable\SmallInteger(0, 'x');
        $this->y = new \phpws2\Variable\SmallInteger(0, 'y');
        $this->zoom = new \phpws2\Variable\SmallInteger(100, 'zoom');
        $this->sorting = new \phpws2\Variable\SmallInteger(0, 'sorting');
    }
    
    public function setSummary(string $summary) {
        $this->summary->set(substr(strip_tags($summary), 0, 300));
    }
    
    public function setTitle(string $title) {
        $this->title->set(substr($title, 0, 100));
    }

}
