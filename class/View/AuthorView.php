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

namespace stories\View;

use stories\Factory\AuthorFactory as Factory;

class AuthorView extends View
{

    /**
     * @var stories\Factory\AuthorFactory
     */
    protected $factory;

    public function __construct()
    {
        $this->factory = new Factory;
    }

}
