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

namespace stories\Resource;

class AuthorResource extends BaseResource
{
    protected $name;
    protected $pic;
    protected $email;
    protected $userId;
    protected $twitterUsername;
    protected $deleted;
    
    protected $table = 'storiesauthor';
    
    public function __construct()
    {
        parent::__construct();
        $this->name = new \phpws2\Variable\TextOnly(null, 'name');
        $this->name->setLimit('100');
        $this->twitterUsername = new \phpws2\Variable\TextOnly(null, 'twitterUsername');
        $this->twitterUsername->setLimit('50');
        $this->pic = new \phpws2\Variable\FileVar(null, 'pic');
        $this->pic->allowNull(true);
        $this->email = new \phpws2\Variable\Email(null, 'email');
        $this->userId = new \phpws2\Variable\IntegerVar(0, 'userId');
        $this->deleted = new \phpws2\Variable\SmallInteger(0, 'deleted');
    }
}
