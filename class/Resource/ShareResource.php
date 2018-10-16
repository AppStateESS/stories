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
    
    protected $publishDate;
    
    protected $approved;
    
    protected $table = 'storiesshare';
    
    public function __construct()
    {
        parent::__construct();
        $this->guestId = new \phpws2\Variable\IntegerVar(null, 'guestId');
        $this->entryId = new \phpws2\Variable\IntegerVar(null, 'entryId');
        $this->publishDate = new \phpws2\Variable\DateTime(null, 'publishDate');
        $this->url = new \phpws2\Variable\StringVar(null, 'url');
        $this->url->setLimit(255);
        $this->approved = new \phpws2\Variable\BooleanVar(false, 'approved');
    }
    
}
