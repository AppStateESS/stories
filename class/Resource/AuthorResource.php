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
    
    public function __construct()
    {
        parent::__construct();
        $this->name = new \phpws2\Variable\TextOnly(null, 'name');
        $this->pic = new \phpws2\Variable\FileVar(null, 'pic');
        $this->email = new \phpws2\Variable\Email(null, 'email');
    }
}
