<?php

/**
 * MIT License
 * Copyright (c) 2019 Electronic Student Services @ Appalachian State University
 * 
 * See LICENSE file in root directory for copyright and distribution permissions.
 * 
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @license https://opensource.org/licenses/MIT
 */

namespace stories\Resource;

class ShareResource extends BaseResource
{

    /**
     * GuestResource id of site sharing the story
     * @var phpws2\Variable\IntegerVar
     */
    protected $guestId;

    /**
     * Id of story on sending site
     * @var phpws2\Variable\IntegerVar
     */
    protected $entryId;

    /**
     * Address of source story
     * @var phpws2\Variable\Url
     */
    protected $url;

    /**
     * @var \phpws2\Variable\DateTime
     */
    protected $publishDate;

    /**
     * @var \phpws2\Variable\BooleanVar
     */
    protected $approved;

    /**
     * @var \phpws2\Variable\DateTime
     */
    protected $submitDate;

    /**
     * @var \phpws2\Variable\SmallInteger
     */
    protected $inaccessible;
    protected $showInList;

    /**
     * @var string     * 
     */
    protected $table = 'storiesshare';

    public function __construct()
    {
        parent::__construct();
        $this->guestId = new \phpws2\Variable\IntegerVar(null, 'guestId');
        $this->entryId = new \phpws2\Variable\IntegerVar(null, 'entryId');
        $this->publishDate = new \phpws2\Variable\DateTime(0, 'publishDate');
        $this->submitDate = new \phpws2\Variable\DateTime(0, 'submitDate');
        $this->inaccessible = new \phpws2\Variable\SmallInteger(0,
                'inaccessible');
        $this->url = new \phpws2\Variable\StringVar(null, 'url');
        $this->url->setLimit(200);
        $this->approved = new \phpws2\Variable\BooleanVar(false, 'approved');
        $this->showInList = new \phpws2\Variable\BooleanVar;
        $this->showInList->allowNull(true);
        $this->doNotSave(array('showInList'));
    }

    public function stampSubmit()
    {
        $this->submitDate->stamp();
    }

    public function stampPublish()
    {
        $this->publishDate->stamp();
    }

    public function incrementInaccessible()
    {
        $this->inaccessible->increase();
    }

}
