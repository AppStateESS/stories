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

namespace stories\Factory;

use stories\Resource\AuthorResource as Resource;
use phpws2\Database;
use Canopy\Request;

/**
 * Description of AuthorFactory
 *
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 */
class AuthorFactory extends BaseFactory
{

    public $moreRows = false;

    public function build($data = null)
    {
        $resource = new Resource();
        if ($data) {
            $resource->setVars($data);
        }
        return $resource;
    }

    public function getByCurrentUser($buildIfEmpty = false)
    {
        $userId = \Current_User::getId();
        $author = $this->getByUserId($userId);
        if (empty($author)) {
            if ($buildIfEmpty) {
                return $this->createFromCurrentUser();
            } else {
                return null;
            }
        } else {
            return $author;
        }
    }

    public function createFromCurrentUser()
    {
        $author = $this->build();
        $author->userId = \Current_User::getId();
        $author->name = \Current_User::getDisplayName();
        $author->pic = null;
        $author->email = \Current_User::getEmail();
        return self::saveResource($author);
    }

    public function getByUserId($userId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesAuthor');
        $tbl->addFieldConditional('userId', $userId);
        $data = $db->selectOneRow();
        return !empty($data) ? $this->build($data) : null;
    }

    public function listing(Request $request)
    {
        $offset = (int) $request->pullGetString('offset', true);
        $offsetSize = 10 * $offset;

        $search = $request->pullGetString('search', true);

        $db = Database::getDB();
        $tbl = $db->addTable('storiesAuthor');
        $userTbl = $db->addTable('users', null, false);
        $userTbl->addField('last_logged');
        $db->joinResources($tbl, $userTbl,
                $db->createConditional($tbl->getField('userId'),
                        $userTbl->getField('id')));
        $tbl->addOrderBy('name');
        $db->setLimit(10, $offset);
        if ($search) {
            $tbl->addFieldConditional('name', "%$search%", 'like');
        }
        $result = $db->select();
        if (count($result) == 10) {
            $this->moreRows = true;
        }
        return $result;
    }

    private function getImageOptions($request)
    {
        $authorId = $request->pullPostInteger('authorId');
        $imageDirectory = "images/stories/author/$authorId/";
        $imagePath = PHPWS_HOME_DIR . $imageDirectory;
        $options = array(
            'max_width' => 200,
            'max_height' => 300,
            'current_image_extensions' => true,
            'upload_dir' => $imagePath,
            'upload_url' => \Canopy\Server::getSiteUrl(true) . $imageDirectory,
            'image_versions' => array()
        );
        return $options;
    }
    
    public function savePhoto(Request $request)
    {
        if (!is_dir(PHPWS_HOME_DIR . 'images/stories/author/')) {
            mkdir(PHPWS_HOME_DIR . 'images/stories/author/');
        }
        $options = $this->getImageOptions($request);
        $upload_handler = new \UploadHandler($options, false);
        $result = $upload_handler->post(false);

        return $result;
    }

}
