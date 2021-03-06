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

use Canopy\Request,
    stories\Factory\AuthorFactory;

class Admin extends User
{

    /**
     * @var stories\Factory\AuthorFactory
     */
    protected $factory;

    protected function listHtmlCommand(Request $request)
    {
        \Menu::disableMenu();
        return $this->view->scriptView('AuthorList');
    }

    protected function listJsonCommand(Request $request)
    {
        $data['listing'] = $this->factory->listing($request);
        $data['moreRows'] = $this->factory->moreRows;
        return $data;
    }

    protected function selectJsonCommand(Request $request)
    {
        return array('listing' => $this->factory->jsonSelectList());
    }

    protected function photoPostCommand(Request $request)
    {
        return $this->factory->savePhoto($request);
    }

    protected function photoDeleteCommand(Request $request)
    {
        return ['success' => (bool) $this->factory->removePhoto($this->id)];
    }

    protected function putCommand(Request $request)
    {
        return $this->factory->put($this->id, $request);
    }

    protected function restorePatchCommand(Request $request)
    {
        return $this->factory->restore($this->id);
    }

    protected function unauthoredJsonCommand(Request $request)
    {
        return $this->factory->getUnauthored();
    }

    protected function createPostCommand(Request $request)
    {
        return $this->factory->createAuthor($request->pullPostInteger('userId'));
    }

    protected function deleteCommand(Request $request)
    {
        return $this->factory->delete($this->id);
    }

}
