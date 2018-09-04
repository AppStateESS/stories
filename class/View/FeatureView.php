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

namespace stories\View;

use Canopy\Request;
use stories\Resource\FeatureResource as Resource;
use stories\Factory\FeatureFactory as Factory;
use phpws2\Settings;

//use stories\Factory\TagFactory;
//use stories\Factory\StoryMenu;
//use stories\Factory\SettingFactory;

class FeatureView extends View
{

    /**
     *
     * @var stories\Factory\FeatureFactory
     */
    protected $factory;

    public function __construct()
    {
        $this->factory = new Factory;
    }

    public function show(Request $request)
    {
        $features = $this->factory->listing();
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

    private function featureColumn($entry, $format, $columns)
    {
        $vars = $entry['story'];
        $entryView = new EntryView;
        $vars['publishInfo'] = $entryView->publishBlock($vars);
        $vars['thumbnailStyle'] = $this->thumbnailStyle($entry);
        return $vars;
    }

    private function thumbnailStyle($entry)
    {
        $thumbnail = $entry['story']['thumbnail'];
        $x = $entry['x'];
        $y = $entry['y'];
        $zoom = $entry['zoom'];
        return <<<EOF
background-image : url('$thumbnail');background-position: {$x}% {$y}%;background-size: {$zoom}%;
EOF;
    }

}
