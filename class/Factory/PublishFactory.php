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

use phpws2\Database;
use stories\Factory\EntryFactory;
use stories\Factory\ShareFactory;
use stories\Factory\FeatureFactory;
use stories\Resource\PublishResource as Resource;

class PublishFactory extends BaseFactory
{

    public $more_rows;

    public function build(array $data = null)
    {
        $resource = new Resource;
        if ($data) {
            $resource->setVars($data);
        }
        return $resource;
    }

    public function getSource(Resource $publish)
    {
        $entryFactory = new EntryFactory;
        $shareFactory = new ShareFactory;

        if ($publish->entryId > 0) {
            return $entryFactory->load($publish->entryId);
        } elseif ($publish->shareId > 0) {
            return $shareFactory->pullShareData($publish->shareId);
        } else {
            throw new \Exception('Bad publish row');
        }
    }

    public function publishEntry(int $entryId, int $publishDate)
    {
        $publishId = $this->getPublishIdByEntryId($entryId);
        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        $tbl->addValue('entryId', $entryId);
        $tbl->addValue('publishDate', $publishDate);
        if ($publishId) {
            $tbl->addFieldConditional('entryId', $entryId);
            $db->update();
        } else {
            $db->insert();
        }
    }

    /**
     * Changes the publish date of an entry only. Unpublished entries are
     * ignored.
     * @param int $entryId
     * @param type $publishDate
     * @return type
     */
    public function updatePublishDateByEntryId(int $entryId, $publishDate)
    {
        $publishId = $this->getPublishIdByEntryId($entryId);
        if (empty($publishId)) {
            return;
        }
        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        $tbl->addValue('entryId', $entryId);
        $tbl->addValue('publishDate', $publishDate);
        $tbl->addFieldConditional('entryId', $entryId);
        $db->update();
    }

    public function publishShare(int $shareId, int $publishDate,
            int $showInList = 1)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        $tbl->addValue('shareId', $shareId);
        $tbl->addValue('publishDate', $publishDate);
        $tbl->addValue('showInList', $showInList);
        $db->insert();
    }

    public function delete(int $publishId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        $tbl->addFieldConditional('id', $publishId);
        return $db->delete();
    }

    /**
     * Unpublish local entries and inform any hosts to unpublish
     * Should perform the following:
     * 1. Remove row from storiespublish
     * 2. Remove from features
     * 3. Check storiestrack for host share
     *    a. If found, inform host to remove the share
     * @param int $entryId
     */
    public function unpublishEntry(int $entryId)
    {
        $publishId = $this->getPublishIdByEntryId($entryId);

        if ($publishId) {
            $featureStoryFactory = new FeatureStoryFactory;
            $featureStoryFactory->deleteByPublishId($publishId);
            $this->delete($publishId);
        }

        $this->unpublishHosts($entryId);
        $this->deleteTrackHosts($entryId);
    }

    /**
     * Remove a published share from the publish table
     * @param int $shareId
     */
    public function unpublishShare(int $shareId)
    {
        $featureStoryFactory = new \stories\Factory\FeatureStoryFactory();
        $featureStoryFactory->deleteByShareId($shareId);

        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        $tbl->addFieldConditional('shareId', $shareId);
        $db->delete();
    }

    public function getPublishIdByEntryId(int $entryId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        $tbl->addField('id');
        $tbl->addFieldConditional('entryId', $entryId);
        return $db->selectColumn();
    }

    /**
     * Check storiestrack to see if the entry was shared. If so,
     * tell share factory to tell each host to remove the unpublish.
     * When done, remove the host track.
     * @param int $entryId
     * @return type
     */
    private function unpublishHosts(int $entryId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiestrack');
        $tbl->addFieldConditional('entryId', $entryId);
        $result = $db->select();
        if (empty($result)) {
            return;
        }

        $shareFactory = new ShareFactory;
        foreach ($result as $row) {
            $shareFactory->removeFromHost($entryId, $row['hostId']);
        }
        $db->delete();
    }

    /**
     * Deletes all tracking of entries sent to hosts.
     * @param int $entryId
     * @return int
     */
    private function deleteTrackHosts(int $entryId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiestrack');
        $tbl->addFieldConditional('entryId', $entryId);
        return $db->delete();
    }

    public function patchByEntry($entryId, $varname, $value)
    {
        $publish = $this->loadByEntryId($entryId);
        $publish->$varname = $value;
        $this->saveResource($publish);
        return true;
    }

    public function patchByShare($shareId, $varname, $value)
    {
        $publish = $this->loadByShareId($shareId);
        $publish->$varname = $value;
        $this->saveResource($publish);
        return true;
    }

    public function listing(array $options = null)
    {
        $defaultOptions = $this->defaultListOptions();

        if (is_array($options)) {
            $options = array_merge($defaultOptions, $options);
        } else {
            $options = $defaultOptions;
        }

        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        $tbl->addOrderBy('publishDate', 'desc');
        $tbl->addFieldConditional('showInList', 1);
        $tbl->addFieldConditional('publishDate', time(), '<=');

        /**
         * To get an accurate test to see if there are more entries for 
         * a Next page button, we ask for one more row than the current limit
         */
        if ($options['limit'] != 0) {
            if ($options['limit'] > STORIES_HARD_LIMIT) {
                $limit = STORIES_HARD_LIMIT;
            } else {
                $limit = ((int) $options['limit']) + 1;
                if (isset($options['offset'])) {
                    $db->setLimit($limit, $options['offset']);
                } else {
                    $db->setLimit($limit);
                }
            }
        }

        if ($options['tag']) {
            $tagIdTable = $db->addTable('storiestagtoentry', null, false);
            $tagTable = $db->addTable('storiestag', null, false);
            $tagTable->addFieldConditional('title', $options['tag']);
            $db->addConditional($db->createConditional($tagTable->getField('id'),
                            $tagIdTable->getField('tagId'), '='));
            $db->addConditional($db->createConditional($tbl->getField('entryId'),
                            $tagIdTable->getField('entryId'), '='));
        }

        $result = $db->select();

        $totalRows = count($result);
        if ($totalRows > $options['limit']) {
            // if there are more rows than the options[limit], we set more_rows
            // to true and pop the extra (remember we asked for one extra row)
            // off the end.

            $this->more_rows = true;
            array_pop($result);
        } else {
            $this->more_rows = false;
        }
        return $result;
    }

    private function defaultListOptions()
    {
        return array(
            'limit' => 10,
            'offset' => 0,
            'page' => 1
        );
    }

    private function getCutOffTime()
    {
        $featureCutOff = \phpws2\Settings::get('stories', 'featureCutOff');

        // months * 30 days * seconds in a day
        $cutOffSeconds = (int) $featureCutOff * 30 * 86400;

        return time() - $cutOffSeconds;
    }

    /**
     * List of publishable stories with just id and title
     */
    public function featureList()
    {
        $featureCutOff = $this->getCutOffTime();
        $options = [];
        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        $tbl->addFieldConditional('publishDate', $featureCutOff, '>');
        $tbl->addOrderBy('publishDate', 'desc');
        $result = $db->select();
        foreach ($result as $row) {
            $pObj = $this->build($row);
            $story = $this->getSource($pObj);
            if (isset($story->error) || empty($story->thumbnail) || empty($story->title)) {
                continue;
            }
            $publishDate = strftime('%b %e', $story->publishDate);
            if ($pObj->shareId > 0) {
                $title = "$story->siteName: $story->title - $publishDate";
            } else {
                $title = "$story->title - $publishDate";
            }

            $options[] = ['id' => $pObj->id, 'title' => $title];
        }
        return $options;
    }

    public function loadByShareId($shareId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        $tbl->addFieldConditional('shareId', $shareId);
        $row = $db->selectOneRow();
        if (empty($row)) {
            return null;
        }
        $publish = $this->build($row);
        return $publish;
    }

    public function loadByEntryId($entryId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        $tbl->addFieldConditional('entryId', $entryId);
        $row = $db->selectOneRow();
        if (empty($row)) {
            return null;
        }
        $publish = $this->build($row);
        return $publish;
    }

}
