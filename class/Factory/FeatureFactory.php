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

use stories\Resource\FeatureResource as Resource;
use stories\Resource\FeatureStoryResource;
use stories\Factory\EntryFactory;
use stories\Factory\PublishFactory;
use stories\View\EntryView;
use phpws2\Database;
use phpws2\Settings;
use Canopy\Request;
use phpws2\Template;

/**
 * Description of FeatureFactory
 *
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 */
class FeatureFactory extends BaseFactory
{

    const SUMMARY_TRIM = 150;

    public function build()
    {
        return new Resource;
    }

    public function post(Request $request)
    {
        $db = Database::getDB();
        $feature = $this->build();
        self::saveResource($feature);
        return $feature->id;
    }

    public function listing(bool $activeOnly = true)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesfeature');
        if ($activeOnly) {
            $tbl->addFieldConditional('active', 1);
        }
        $tbl->addOrderBy('sorting');
        $result = $db->select();
        if (empty($result)) {
            return null;
        }

        return $result;
    }

    private function trimCharactersCount($format, $columns, $title)
    {
        $titleLength = round(strlen($title) * 1.1);

        switch ($columns) {
            case 2:
                $columnAllowed = 220;
                break;

            case 3:
                $columnAllowed = 140;
                break;

            default:
                $columnAllowed = 0;
                break;
        }

        $baseCutoff = $columnAllowed - $titleLength;

        switch ($format) {
            case 'landscape':
                $finalCount = $baseCutoff + 70;

            case 'topbottom':
                $finalCount = $baseCutoff + 70;

            case 'leftright':
                $finalCount = $baseCutoff + 110;
        }
        return $finalCount < 0 ? 0 : $finalCount;
    }

    private function trimSummary($summary, $sTrim = self::SUMMARY_TRIM)
    {
        $sLength = strlen($summary);

        if ($sLength < $sTrim) {
            return $summary;
        }
        $tooLong = true;
        $count = 0;
        $firstPop = false;
        while ($tooLong && $count < 100) {
            $sArray = preg_split('/([\?\.\!]\s?)/', $summary, null,
                    PREG_SPLIT_DELIM_CAPTURE);
            if (count($sArray) == 1) {
                return $this->lastSpaceEllipsis($sArray[0]);
            }
            //end space
            array_pop($sArray);
            //punctuation
            array_pop($sArray);
            //sentence
            array_pop($sArray);
            if (empty($sArray)) {
                return $this->lastSpaceEllipsis($summary);
            }
            $summary = implode('', $sArray);
            if (strlen($summary) < $sTrim) {
                $tooLong = false;
            }
            $count++;
        }
        return $summary;
    }

    private function lastSpaceEllipsis($content)
    {
        $lastSpace = strrpos($content, ' ');
        return substr($content, 0, $lastSpace) . '...';
    }


    public function delete($featureId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesfeature');
        $tbl->addFieldConditional('id', $featureId);
        $db->delete();
        $this->deleteStoryList($featureId);
    }

    public function deleteStoryList($featureId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesfeaturestory');
        $tbl->addFieldConditional('featureId', $featureId);
        $db->delete();
    }

    public function put(Resource $feature, Request $request)
    {
        $feature->loadPutByType($request, array('id'));
        self::saveResource($feature);
    }

    
    public function deleteByPublishId(int $publishId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesfeaturestory');
        $tbl->addFieldConditional('publishId', $publishId);
        $tbl->addField('featureId');
        while ($featureId = $db->selectColumn()) {
            $allFeatures[] = $featureId;
        }
        if (empty($allFeatures)) {
            return;
        }
        $db->delete();
        foreach ($allFeatures as $id) {
            $this->reorder($id);
        }
    }

    public function reorder($featureId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesfeaturestory');
        $tbl->addFieldConditional('featureId', $featureId);
        $tbl->addOrderBy('sorting');
        $features = $db->select();
        if (empty($features)) {
            return;
        }
        $count = 1;
        $tbl->resetOrderBy();
        foreach ($features as $row) {
            $db->clearConditional();
            $tbl->addValue('sorting', $count);
            $tbl->addFieldConditional('publishId', $row['publishId']);
            $tbl->addFieldConditional('featureId', $row['featureId']);
            $db->update();
            $count++;
        }
    }

}
