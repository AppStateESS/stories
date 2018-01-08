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

class TagResource extends BaseResource
{
    protected $title;
    
    protected $table = 'storiestag';
    
    public function __construct() {
        parent::__construct();
        $this->title = new \phpws2\Variable\TextOnly(null, 'title');
    }
}
