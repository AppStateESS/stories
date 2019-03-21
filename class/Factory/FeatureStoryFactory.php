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

namespace stories\Factory;

use stories\Factory\EntryPhotoFactory;
use stories\Factory\PublishFactory;
use stories\Factory\AuthorFactory;
use stories\Factory\EntryFactory;
use phpws2\Database;
use stories\Resource\FeatureStoryResource as Resource;
use Canopy\Request;

class FeatureStoryFactory extends BaseFactory
{

    /**
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

    private function getLastSort(int $featureId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesfeaturestory', null, false);
        $tbl->addFieldConditional('featureId', $featureId);
        $max = $tbl->getField('sorting');
        $db->addExpression('max(' . $max . ')', 'max');
        return $db->selectColumn();
    }

    public function put($featureStoryId, $publishId)
    {
        $publishFactory = new PublishFactory;
        $publishObj = $publishFactory->load($publishId);
        // $story will be share or entry
        $storyObj = $publishFactory->getSource($publishObj);
        $featureStory = $this->copyIntoFeatureStory($storyObj, $featureStoryId);
        $featureStory->publishId = $publishObj->id;
        self::saveResource($featureStory);
    }

    public function post(int $featureId, int $publishId)
    {
        $publishFactory = new PublishFactory;
        $publishObj = $publishFactory->load($publishId);
        // $story will be share or entry
        $storyObj = $publishFactory->getSource($publishObj);
        $featureStory = $this->copyIntoFeatureStory($storyObj);
        $featureStory->shareId = $publishObj->shareId;
        $featureStory->publishId = $publishObj->id;
        $featureStory->featureId = $featureId;
        $featureStory->sorting = $this->getLastSort($featureId) + 1;
        self::saveResource($featureStory);
    }

    /**
     * Receives a share or entry object and creates a FeatureStory
     * @param object $storyObj
     */
    private function copyIntoFeatureStory($storyObj,
            int $featureStoryId = 0)
    {
        if ($featureStoryId > 0) {
            $featureStory = $this->load($featureStoryId);
        } else {
            $featureStory = $this->build();
        }
        $featureStory->title = $storyObj->title;
        $featureStory->summary = $storyObj->strippedSummary;
        $featureStory->thumbnail = $storyObj->thumbnail;
        $featureStory->url = $storyObj->url;
        $featureStory->publishDate = $storyObj->publishDate;
        if (isset($storyObj->authorName)) {
            $featureStory->authorName = $storyObj->authorName;
            $featureStory->authorPic = $storyObj->authorPic;
        }
        return $featureStory;
    }

    public function listing(int $featureId, $asResource = true)
    {
        if (!$featureId) {
            throw new \Exception('Zero feature id');
        }
        $db = Database::getDB();
        $tbl = $db->addTable('storiesfeaturestory');
        $tbl->addOrderBy('sorting');
        $tbl->addFieldConditional('featureId', $featureId);

        $result = $db->selectAsResources('\stories\Resource\FeatureStoryResource');

        if (empty($result)) {
            return null;
        }
        if ($asResource) {
            return $result;
        } else {
            foreach ($result as $fsr) {
                $row = $fsr->getStringVars();
                $row['publishDateRelative'] = $fsr->relativeTime($fsr->publishDate);
                $listing[] = $row;
            }
            return $listing;
        }
    }

    /**
     * Delete a feature story, then resort the feature.
     * @param int $featureStoryId
     * @throws \Exception
     */
    public function delete(int $featureStoryId)
    {
        if (!$featureStoryId) {
            throw new \Exception('Zero story id');
        }
        $db = Database::getDB();
        $tbl = $db->addTable('storiesfeaturestory');
        $tbl->addFieldConditional('id', $featureStoryId);
        $row = $db->selectOneRow();
        $db->delete();

        $sortable = new \phpws2\Sortable('storiesfeaturestory', 'sorting');
        $sortable->setAnchor('featureId', $row['featureId']);
        $sortable->reorder();
    }

    public function patch(int $featureStoryId, Request $request)
    {
        $featureStory = $this->load($featureStoryId);
        $paramList = $request->pullPatchArray('params');
        foreach ($paramList as $param) {
            switch ($param) {
                case 'x':
                    $featureStory->x = $request->pullPatchInteger('x');
                    break;
                case 'y':
                    $featureStory->y = $request->pullPatchInteger('y');
                    break;
                case 'zoom':
                    $featureStory->zoom = $request->pullPatchInteger('zoom');
                    break;
            }
        }
        self::saveResource($featureStory);
    }

    public function updateEntryThumbnail(Request $request)
    {
        $entryPhotoFactory = new EntryPhotoFactory;
        $publishFactory = new PublishFactory;
        $story = $this->load($request->pullPostInteger('storyId'));
        $publish = $publishFactory->load($story->publishId);
        if ($publish->entryId === 0) {
            throw new \Exception('Feature story thumbnail must be an entry.');
        }
        $thumbnailUrl = $entryPhotoFactory->postThumbnail($publish->entryId,
                $request);
        $story->thumbnail = $thumbnailUrl;
        self::saveResource($story);
    }

    /**
     * Pulls feature stories by the publish id and removes them.
     * 
     * @param int $publishId
     * @return null
     * @throws \Exception
     */
    public function deleteByPublishId(int $publishId)
    {
        if (!$publishId) {
            throw new \Exception('Zero publish id');
        }
        $db = Database::getDB();
        $tbl = $db->addTable('storiesfeaturestory');
        $tbl->addField('id');
        $tbl->addFieldConditional('publishId', $publishId);
        $rows = $db->select();
        if (empty($rows)) {
            return;
        }
        foreach ($rows as $row) {
            $this->delete($row['id']);
        }
    }

    /**
     * Deletes feature stories according to share id.
     * @param int $shareId
     * @return type
     * @throws \Exception
     */
    public function deleteByShareId(int $shareId)
    {
        if (!$shareId) {
            throw new \Exception('Zero share id');
        }
        $db = Database::getDB();
        $tbl = $db->addTable('storiesfeaturestory');
        $tbl->addField('id');
        $tbl->addFieldConditional('shareId', $shareId);
        $rows = $db->select();
        if (empty($rows)) {
            return;
        }
        foreach ($rows as $row) {
            $this->delete($row['id']);
        }
    }

    public function listByPublishId(int $publishId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesfeaturestory');
        $tbl->addFieldConditional('publishId', $publishId);
        return $db->selectAsResources('\stories\Resource\FeatureStoryResource');
    }

    /**
     * Updates any feature stories based on this entry.
     * @param integer $entryId
     */
    public function refreshEntry(int $entryId)
    {
        $entryFactory = new EntryFactory;
        $publishFactory = new PublishFactory;
        $authorFactory = new AuthorFactory;
        $publishId = $publishFactory->getPublishIdByEntryId($entryId);
        $stories = $this->listByPublishId($publishId);
        if (empty($stories)) {
            return;
        }
        $entry = $entryFactory->load($entryId);
        foreach ($stories as $fs) {
            $authorFactory = new AuthorFactory;
            $author = $authorFactory->load($entry->authorId);

            $fs->title = $entry->title;
            $fs->summary = $entry->getStrippedSummary();
            $fs->thumbnail = $entry->thumbnail;
            $fs->url = $entry->url;
            $fs->x = 0;
            $fs->y = 0;
            $fs->zoom = 100;
            $fs->publishDate = $entry->publishDate;
            $fs->authorPic = $author->pic;
            $fs->authorName = $author->name;
            self::saveResource($fs);
        }
    }

}
