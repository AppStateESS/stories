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
use stories\Factory\EntryFactory;
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

    private function defaultHiddenEntryVars()
    {
        return array(
            'content', 'tags', 'deleted', 'expirationDate', 'leadImage', 'summary');
    }

    public function listing($options = null)
    {
        $defaultOptions = array(
            'hiddenVars' => $this->defaultHiddenEntryVars(),
            'activeOnly' => true
        );

        if (is_array($options)) {
            $options = array_merge($defaultOptions, $options);
        } else {
            $options = $defaultOptions;
        }

        $db = Database::getDB();
        $tbl = $db->addTable('storiesfeature');
        if ($options['activeOnly']) {
            $tbl->addFieldConditional('active', 1);
        }
        $tbl->addOrderBy('sorting');
        $result = $db->select('\stories\Resource\FeatureResource');
        if (empty($result)) {
            return null;
        }

        array_walk($result, array($this, 'addEntries'), $options['hiddenVars']);
        return $result;
    }

    /**
     * 
     * @param array $feature
     * @param type $key Not used
     * @param type $hiddenVars
     */
    private function addEntries(&$feature, $key, $hiddenVars)
    {
        $entryFactory = new EntryFactory;
        $db = Database::getDB();
        $tbl = $db->addTable('storiesentrytofeature');
        $tbl->addFieldConditional('featureId', $feature['id']);
        $tbl->addOrderBy('sorting');
        $entries = $db->select();
        if (!empty($entries)) {
            foreach ($entries as $k => $entry) {
                $entryObj = $entryFactory->load($entry['entryId']);
                if (!$entryObj->published) {
                    continue;
                }
                $vars = $entryObj->getStringVars(true, $hiddenVars);
                $trimCharacters = $this->trimCharactersCount($feature['format'],
                        $feature['columns'], $entryObj->title);
                $vars['strippedSummary'] = $this->trimSummary($vars['strippedSummary'],
                        $trimCharacters);
                $entries[$k]['story'] = $vars;
            }
        }
        $feature['entries'] = $entries;
    }

    private function featureColumn($entry, $format, $columns)
    {
        $vars = $entry['story'];
        $entryFactory = new EntryFactory;
        $vars['publishInfo'] = $entryFactory->publishBlock($vars);
        $vars['thumbnailStyle'] = $this->thumbnailStyle($entry);
        return $vars;
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

            case 4:
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

    private function thumbnailStyle($entry)
    {
        $thumbnail = $entry['story']['thumbnail'];
        $x = $entry['x'];
        $y = $entry['y'];
        return <<<EOF
background-image : url('$thumbnail');background-position: {$x}% {$y}%;
EOF;
    }

    private function featureRow($feature, $showAuthor)
    {
        foreach ($feature['entries'] as $entry) {
            $vars['entries'][] = $this->featureColumn($entry,
                    $feature['format'], $feature['columns']);
        }
        switch ($feature['columns']) {
            case '2':
                $bsClass = 'col-sm-6';
                break;
            case '3':
                $bsClass = 'col-sm-4';
                break;
            case '4':
                $bsClass = 'col-sm-6 col-md-3';
                break;
        }
        $vars['bsClass'] = $bsClass;
        $vars['format'] = 'story-feature ' . $feature['format'];
        $vars['featureTitle'] = $feature['title'];
        $vars['showAuthor'] = $showAuthor;
        $template = new \phpws2\Template($vars);
        $template->setModuleTemplate('stories', 'Feature.html');
        return $template->get();
    }

    public function loadEntries(Resource $feature)
    {
        $entries = $feature->entries;
        if (empty($entries)) {
            return;
        }
        $entryFactory = new EntryFactory;
        foreach ($feature->entries as $k => $e) {
            $entryObj = $entryFactory->load($e['entryId']);
            $vars = $entryObj->getStringVars(true,
                    $this->defaultHiddenEntryVars());
            $trimCharacters = $this->trimCharactersCount($feature->format,
                    $feature->columns, $entryObj->title);
            $vars['trim'] = $trimCharacters;
            $vars['strippedSummary'] = $this->trimSummary($vars['strippedSummary'],
                    $trimCharacters);
            $entries[$k]['story'] = $vars;
        }
        $feature->entries = $entries;
    }

    public function show(Request $request)
    {
        $features = $this->listing();
        if (empty($features)) {
            return;
        }

        $showAuthor = Settings::get('stories', 'showAuthor');

        foreach ($features as $f) {
            if (empty($f['entries'])) {
                continue;
            }
            $featureStack[] = $this->featureRow($f, $showAuthor);
        }
        if (empty($featureStack)) {
            return null;
        }
        $this->addStoryCss();
        return '<div id="story-feature-list">' . implode('', $featureStack) . '</div>';
    }


    public function delete($featureId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesfeature');
        $tbl->addFieldConditional('id', $featureId);
        $db->delete();
        $this->deleteEntryList($featureId);
    }

    public function deleteEntryList($featureId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesentrytofeature');
        $tbl->addFieldConditional('featureId', $featureId);
        $db->delete();
    }

    public function update(Resource $feature, Request $request)
    {
        $feature->loadPutByType($request, array('id'));
        $entries = $feature->entries;

        // new code
        $db = Database::getDB();
        $tbl = $db->addTable('storiesentrytofeature');
        $tbl->addFieldConditional('featureId', $feature->id);
        $db->delete();
        $db->clearConditional();

        $count = 1;
        foreach ($entries as $key => $entry) {
            if ($entry['entryId'] == 0) {
                unset($entries[$key]);
            } else {
                $tbl->addValue('entryId', (int)$entry['entryId']);
                $tbl->addValue('featureId', (int)$feature->id);
                $tbl->addValue('x', (int)$entry['x']);
                $tbl->addValue('y', (int)$entry['y']);
                $tbl->addValue('zoom', (int)$entry['zoom']);
                $tbl->addValue('sorting', (int)$count);
                $db->insert();
                $count++;
            }
        }

        $feature->entries = $entries;
        self::saveResource($feature);
        return $feature;
    }

    /**
     * Looks for an entry in all the current features. If found it is removed
     * and the feature is resorted.
     * @param type $entryId
     * @return type
     */
    public function removeEntryFromAll($entryId)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesentrytofeature');
        $tbl->addFieldConditional('entryId', $entryId);
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
        $tbl = $db->addTable('storiesentrytofeature');
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
            $tbl->addFieldConditional('entryId', $row['entryId']);
            $tbl->addFieldConditional('featureId', $row['featureId']);
            $db->update();
            $count++;
        }
    }

}
