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

    public function saveToEntry($entryId, $tags)
    {
        if (!is_array($tags)) {
            $tagArray = explode(',', $tags);
        } else {
            $tagArray = $tags;
        }

        // remove current tags for entry
        $this->clearEntryTags($entryId);
        if (empty($tagArray) || empty($tagArray[0])) {
            return;
        }
        // save new tags and apply to entry
        foreach ($tagArray as $tag) {
            $this->applyTagToEntry($entryId, $tag['value']);
        }
    }

    private function prepareTag($tag)
    {
        return strtolower(trim($tag));
    }

    private function applyTagToEntry($entryId, $tagId)
    {
        if (!is_numeric($tagId)) {
            throw new \Exception('Bad tag id');
        }
        $db = Database::getDB();
        $tbl = $db->addTable('storiestagtoentry');
        $tbl->addValue('entryId', $entryId);
        $tbl->addValue('tagId', $tagId);
        return $db->insert();
    }

    /**
     * 
     * @param string $tag
     * @return integer Id of tag
     */
    public function saveTag($tagTitle)
    {
        $tagTitle = $this->prepareTag($tagTitle);
        if (empty($tagTitle)) {
            return;
        }
        $tag = $this->getTagByTitle($tagTitle);
        if (empty($tag)) {
            $tag = $this->build();
            $tag->title = $tagTitle;
            self::saveResource($tag);
        }

        return $tag->id;
    }

    public function getTagsByEntryId($entryId, $selectValues = false)
    {
        $db = Database::getDB();
        $tagTbl = $db->addTable('storiestag');
        $tteTbl = $db->addTable('storiestagtoentry', null, false);
        //$tagTbl->addField('title');
        $cond = $db->createConditional($tteTbl->getField('tagId'),
                $tagTbl->getField('id'), '=');
        $db->joinResources($tagTbl, $tteTbl, $cond);
        $tteTbl->addFieldConditional('entryId', $entryId);
        $tags = $db->select();

        if (empty($tags)) {
            return array();
        }
        if ($selectValues) {
            foreach ($tags as $row) {
                $newTagList[] = $this->selectValuesFromArray($row);
            }
            return $newTagList;
        } else {
            return $tags;
        }
    }

    public function clearEntryTags($entryId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiestagtoentry');
        $tbl->addFieldConditional('entryId', $entryId);
        $db->delete();
    }

    public function getTagByTitle($title)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiestag');
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
        $tbl = $db->addTable('storiestag');
        $tbl->addFieldConditional('id', $id);
        $db->delete();
    }

    public function deleteTagEntry($id)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiestagtoentry');
        $tbl->addFieldConditional('tagId', $id);
        $db->delete();
    }

    private function selectValuesFromArray($val)
    {
        return array('value' => $val['id'], 'label' => $val['title']);
    }

    public function listTags($selectValues = false)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiestag');
        $tbl->addOrderBy('title');
        $result = $db->select();
        if (empty($result)) {
            return;
        }
        if (!$selectValues) {
            return $result;
        } else {
            return $this->changeToSelectValues($result);
        }
    }

    public function changeToSelectValues($values)
    {
        $result = array();
        foreach ($values as $row) {
            $result[] = $this->selectValuesFromArray($row);
        }
        return $result;
    }

    public function getTagLinks($tags, $entryId, $currentTag=null)
    {
        if (empty($tags)) {
            return null;
        }
        if (!empty($currentTag)) {
            $firstTag = $currentTag;
            foreach ($tags as $k=>$v) {
                if ($v['label'] == $currentTag) {
                    unset($tags[$k]);
                    break;
                }
            }
        } else {
            $firstTag = $tags[0]['label'];
            unset($tags[0]);
        }
        
        if (count($tags) == 0) {
            return <<<EOF
<span class="tagged" data-entry-id="$entryId">Filed under: <a href="./stories/Tag/$firstTag">$firstTag</a></span>
EOF;
        }
        
        foreach ($tags as $tag) {
            $options[] = <<<EOF
<li><a class="tag-link pointer" href="./stories/Tag/{$tag['label']}">{$tag['label']}</a></li>
EOF;
        }
        $tags = implode('', $options);
        $content = <<<EOF
    <span class="tagged pointer" data-entry-id="$entryId">Filed under: <a href="./stories/Tag/$firstTag">$firstTag <i class="fas fa-caret-down"></i></a></span>
    <div class="invisible">
         <div id="entry-$entryId" class="tag-list">
                <ul class="list-unstyled">
                $tags
                </ul>
         </div>
    </div>
EOF;
        return $content;
    }

    public function purgeEntry($entryId)
    {
        $db = \phpws2\Database::getDB();
        $tbl = $db->addTable('storiestagtoentry');
        $tbl->addFieldConditional('entryId', $entryId);
        $db->delete();
    }
    
}
