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
 * Manages Tags Resources
 *
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 */

namespace stories\Factory;

use stories\Resource\TagResource as Resource;
use phpws2\Database;
use stories\Exception\ResourceNotFound;

class TagFactory extends BaseFactory
{

    public function build()
    {
        return new Resource;
    }

    public function save($entryId, $tags)
    {
        if (!is_array($tags)) {
            $tagArray = explode(',', $tags);
        } else {
            $tagArray = $tags;
        }

        // remove current tags for entry
        $this->clearEntryTags($entryId);

        // save new tags and apply to entry
        foreach ($tagArray as $tag) {
            $tagId = $this->saveTag($tag);
            $this->applyTagToEntry($entryId, $tagId);
        }
    }

    private function prepareTag($tag)
    {
        return strtolower(trim($tag));
    }

    private function applyTagToEntry($entryId, $tagId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesTagToEntry');
        $tbl->addValue('entryId', $entryId);
        $tbl->addValue('tagId', $tagId);
        return $db->insert();
    }

    /**
     * 
     * @param string $tag
     * @return integer Id of tag
     */
    public function saveTag($tag)
    {
        $tag = $this->prepareTag($tag);
        $tag = $this->getTagByTitle($tag);
        if (empty($tag)) {
            $tag = $this->build();
            $tag->title = $tag;
            self::saveResource($tag);
        }

        return $tag->id;
    }

    public function getTagsByEntryId($entryId)
    {
        $db = Database::getDB();
        $tagTbl = $db->addTable('storiesTag');
        $tteTbl = $db->addTable('storiesTagToEntry');
        $tagTbl->addField('title');
        $cond = $db->createConditional($tteTbl->getField('tagId'),
                $tagTbl->getField('id'), '=');
        $db->joinResources($tagTbl, $tteTbl, $cond);
        $tteTbl->addFieldConditional('entryId', $entryId);
        $db->select();
    }

    public function clearEntryTags($entryId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesTagToEntry');
        $tbl->addFieldConditional('entryId', $entryId);
        $db->delete();
    }

    public function getTagByTitle($title)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesTag');
        $tbl->addFieldConditional('title', $title);
        $tagVars = $db->selectOneRow();
        if (empty($tagVars)) {
            return null;
        }
        $tag = new Resource;
        $tag->setVars($tagVars);
        return $tag;
    }

    public function delete($id)
    {
        $this->deleteTagEntry($id);
        $db = Database::getDB();
        $tbl = $db->addTable('storiesTag');
        $tbl->addFieldConditional('id', $id);
        $db->delete();
    }

    public function deleteTagEntry($id)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesTagToEntry');
        $tbl->addFieldConditional('tagId', $id);
        $db->delete();
    }

}
