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
use stories\View\FeatureStoryView;
use phpws2\Template;
use phpws2\Settings;

//use stories\Factory\TagFactory;
//use stories\Factory\StoryMenu;
//use stories\Factory\SettingFactory;

class FeatureView extends View
{

    /**
     * @var stories\Factory\FeatureFactory
     */
    protected $factory;

    public function __construct()
    {
        $this->factory = new Factory;
    }

    public function show(Request $request)
    {
        $featureStoryView = new FeatureStoryView;
        $features = $this->factory->listing(true);
        if (empty($features)) {
            return;
        }

        $showAuthor = Settings::get('stories', 'showAuthor');

        foreach ($features as $f) {
            $storyListing = $featureStoryView->listing($f['id'], $f['format']);
            if (empty($storyListing)) {
                continue;
            }
            $f['stories'] = implode("\n", $storyListing);
            $template = new Template($f);
            $template->setModuleTemplate('stories', 'Feature/Feature.html');
            $featureStack[] = $template->get();
        }
        if (empty($featureStack)) {
            return null;
        }
        $this->addStoryCss();
        return '<div id="story-feature-list">' . implode('', $featureStack) . '</div>';
    }
}
