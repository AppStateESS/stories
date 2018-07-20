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

    /**
     * 
     * @param array $data
     * @return Resource
     */
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

    public function createAuthor($userId)
    {
        $user = new \PHPWS_User($userId);
        if (empty($user->getUsername())) {
            throw new \Exception('User not found');
        }
        $author = $this->build();
        $author->userId = $user->getId();
        $author->name = $user->getDisplayName();
        $author->email = $user->getEmail();
        $author->pic = null;
        return self::saveResource($author);
    }

    public function createFromCurrentUser()
    {
        return $this->createAuthor(\Current_User::getId());
    }

    public function getByUserId($userId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesauthor');
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
        $tbl = $db->addTable('storiesauthor');
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
            'param_name' => 'image',
            'max_width' => 150,
            'max_height' => 150,
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
        if (isset($result['files'][0]->error)) {
            return array('error' => $result['files'][0]->error);
        } else {
            $authorId = $request->pullPostInteger('authorId');
            $imageFile = $result['image'][0]->name;
            $imageDirectory = "images/stories/author/$authorId/";

            $author = $this->load($authorId);
            if (!empty($author->pic) && is_file($author->pic)) {
                unlink($author->pic);
            }
            $author->pic = $imageDirectory . $imageFile;
            self::saveResource($author);
            return $result;
        }
    }

    public function jsonSelectList()
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesauthor');
        $tbl->addField('id', 'value');
        $tbl->addField('name', 'label');
        $tbl->addOrderBy('name');
        return $db->select();
    }

    public function put($authorId, Request $request)
    {
        $author = $this->load($authorId);
        $author->name = $request->pullPutString('name');
        $author->email = $request->pullPutString('email');
        return self::saveResource($author);
    }

    private function getPermissionUsers()
    {
        $db = Database::getDB();

        $permissionTable = $db->addTable('stories_permissions', null, false);
        $permissionTable->addFieldConditional('permission_level', 2);

        $groupsTable = $db->addTable('users_groups');
        $groupsTable->addField('id');
        $groupsTable->addField('user_id');
        $conditional = $db->createConditional($permissionTable->getField('group_id'),
                $groupsTable->getField('id'));
        $join1 = $db->joinResources($groupsTable, $permissionTable,
                $conditional, 'left');
        $result = $db->select();

        $userList = array();
        foreach ($result as $group) {
            if ($group['user_id'] == '0') {
                $db2 = Database::getDB();
                $members = $db2->addTable('users_members', null, false);
                $groups = $db2->addTable('users_groups');
                $db2->joinResources($members, $groups,
                        $db2->createConditional($members->getField('member_id'),
                                $groups->getField('id')));
                $members->addFieldConditional('group_id', $group['id']);
                $groups->addField('user_id');
                while ($userId = $db2->selectColumn()) {
                    $userList[] = $userId;
                }
            } else {
                $userList[] = $group['user_id'];
            }
        }
        
        $db3 = Database::getDB();
        $users = $db3->addTable('users');
        $users->addField('id');
        $users->addFieldConditional('deity', 1);
        while ($userId = $db3->selectColumn()) {
            $userList[] = $userId;
        }
        return $userList;
    }

    /**
     * Returns an array of users who are not currently authors
     */
    public function getUnauthored()
    {
        $userList = $this->getPermissionUsers();

        $db = Database::getDB();
        $authorTable = $db->addTable('storiesauthor', null, false);
        $usersTable = $db->addTable('users');
        $usersTable->addField('id');
        $usersTable->addField('username');
        $usersTable->addField('display_name');
        $usersTable->addFieldConditional('id', $userList, 'in');
        $conditional = $db->createConditional($usersTable->getField('id'),
                $authorTable->getField('userId'));
        $db->joinResources($usersTable, $authorTable, $conditional, 'left');
        $authorTable->addFieldConditional('userId', null, 'is');
        $result = $db->select();
        return $result;
    }

}
