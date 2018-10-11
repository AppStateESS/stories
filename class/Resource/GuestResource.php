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

class GuestResource extends BaseResource
{
    /**
     *
     * @var phpws2\Variable\TextOnly
     */
    protected $siteName;
    
    /**
     * Destination of shared stories
     * @var phpws2\Variable\Url
     */
    protected $url;
    
    /**
     * 0 - no action
     * 1 - accepted
     * 2 - rejected
     * @var \phpws2\Variable\IntegerVar
     */
    protected $status;
    
    /**
     * Key used to communicate with destination
     * @var \phpws2\Variable\Alphanumeric
     */
    protected $authkey;
    
    protected $email;
    
    protected $submitDate;
    
    protected $acceptDate;
    
    protected $table = 'storiesguest';
    
    public function __construct()
    {
        parent::__construct();
        $this->siteName = new \phpws2\Variable\TextOnly(null, 'siteName');
        $this->siteName->setLimit(255);
        //$this->url = new \phpws2\Variable\Url(null, 'url');
        $this->url = new \phpws2\Variable\StringVar(null, 'url');
        $this->url->setLimit(255);
        $this->status = new \phpws2\Variable\SmallInteger(0, 'status');
        $this->authkey = new \phpws2\Variable\HashVar(null, 'authkey');
        $this->authkey->setLimit(40);
        $this->email = new \phpws2\Variable\Email(null, 'email');
        $this->email->setLimit(255);
        $this->submitDate = new \phpws2\Variable\DateTime(0, 'submitDate');
        $this->acceptDate = new \phpws2\Variable\DateTime(0, 'acceptDate');
    }
}
