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
        $tbl = $db->addTable('storiesFeature');
        if (!$options['activeOnly']) {
            $tbl->addFieldConditional('active', 1);
        }
        $tbl->addOrderBy('sorting');
        $result = $db->select('\stories\Resource\FeatureResource');
        if (empty($result)) {
            return null;
        }

        array_walk($result, array($this, 'encodeEntry'), $options['hiddenVars']);
        return $result;
    }

    private function encodeEntry(&$feature, $key, $hiddenVars)
    {
        $entryFactory = new EntryFactory;
        $entries = json_decode($feature['entries']);
        if (!empty($entries)) {
            foreach ($entries as $k => $e) {
                $entry = $entryFactory->load($e->id);
                $vars = $entry->getStringVars(true, $hiddenVars);
                $entries[$k]->story = $vars;
            }
        }
        $feature['entries'] = $entries;
    }

    private function featureColumn($entry)
    {
        $vars = $entry->story;
        $vars['thumbnailStyle'] = $this->thumbnailStyle($entry);
        return $vars;
    }

    private function thumbnailStyle($entry)
    {
        $thumbnail = $entry->story['thumbnail'];
        $x = $entry->x;
        $y = $entry->y;
        return <<<EOF
background-image : url('$thumbnail');background-position: {$x}% {$y}%;
EOF;
    }

    private function featureRow($feature)
    {
        foreach ($feature['entries'] as $entry) {
            $vars['entries'][] = $this->featureColumn($entry);
        }
        switch ($feature['columns']) {
            case '2':
                $bsClass = 'col-sm-6';
                break;
            case '3':
                $bsClass = 'col-sm-4';
                break;
            case '4':
                $bsClass = 'col-sm-3';
                break;
        }
        $vars['bsClass'] = $bsClass;
        $vars['format'] = 'story-feature ' . $feature['format'];
        $vars['featureTitle'] = $feature['title'];
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
        foreach ($feature->entries as $k=>$e) {
            $entryObj = $entryFactory->load($e['id']);
            $vars = $entryObj->getStringVars(true,
                    $this->defaultHiddenEntryVars());
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

        foreach ($features as $f) {
            if (empty($f['entries'])) {
                continue;
            }
            $featureStack[] = $this->featureRow($f);
        }
        if (empty($featureStack)) {
            return null;
        }
        $this->addStoryCss();
        return '<div id="story-feature-list">' . implode('', $featureStack) . '</div>';
    }

    public function update(Resource $feature, Request $request)
    {
        $feature->loadPutByType($request, array('id'));
        $entries = $feature->entries;
        foreach ($entries as $key => $entry) {
            if ($entry['id'] == 0) {
                unset($entries[$key]);
            }
        }
        $feature->entries = $entries;
        self::saveResource($feature);
        return $feature;
    }

}
