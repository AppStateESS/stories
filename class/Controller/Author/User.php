<?php

/*
 * The MIT License
 *
 * Copyright 2017 Matthew McNaney <mcnaneym@appstate.edu>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 */

namespace stories\Controller\Author;

use Canopy\Request;
use stories\Factory\AuthorFactory as Factory;
use stories\View\AuthorView as View;
use stories\Controller\RoleController;

class User extends RoleController
{

    /**
     * @var stories\Factory\AuthorFactory Factory
     */
    protected $factory;

    public function loadFactory()
    {
        $this->factory = new Factory;
    }

    public function loadView()
    {
        $this->view = new View;
    }

    protected function listHtmlCommand(Request $request)
    {
        \Current_User::requireLogin();
    }
    
    protected function listJsonCommand(Request $request)
    {
        \phpws2\Error::errorPage(401);
    }

}
