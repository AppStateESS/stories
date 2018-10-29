<?php

/**
 * MIT License
 * Copyright (c) 2018 Electronic Student Services @ Appalachian State University
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

class PublishFactory
{

    public $more_rows;
    
    public function publishEntry(int $entryId, int $publishDate)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        $tbl->addValue('entryId', $entryId);
        $tbl->addValue('publishDate', $publishDate);
        $db->insert();
    }

    public function publishShare(int $shareId, int $publishDate)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        $tbl->addValue('shareId', $shareId);
        $tbl->addValue('publishDate', $publishDate);
        $db->insert();
    }

    public function unpublishEntry(int $entryId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        $tbl->addFieldConditional('entryId', $entryId);
        $db->delete();
    }

    public function unpublishShare(int $shareId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        $tbl->addFieldConditional('shareId', $shareId);
        $db->delete();
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
    
    public function deleteByShareId($shareId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiespublish');
        $tbl->addFieldConditional('shareId', $shareId);
        $db->delete();
    }

}
