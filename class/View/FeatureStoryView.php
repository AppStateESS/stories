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

use stories\Factory\FeatureStoryFactory as Factory;
use stories\View\PublishedView;
use stories\Resource\FeatureStoryResource as Resource;
use phpws2\Database;
use phpws2\Settings;
use Canopy\Request;
use phpws2\Template;

class FeatureStoryView extends View
{

    /**
     * @var stories\Factory\FeatureStoryFactory
     */
    protected $factory;

    public function __construct()
    {
        $this->factory = new Factory;
    }

    public function listing($featureId, $format)
    {
        $listing = $this->factory->listing($featureId);
        $columnCount = count($listing);
        $showAuthor = \phpws2\Settings::get('stories', 'showAuthor');
        foreach ($listing as $story) {
            $columns[] = $this->view($story, $format, $columnCount);
        }
        return $columns;
    }

    public function view(Resource $story, string $format, int $columns = 1)
    {
        $publishedView = new PublishedView;
        $tag = null;
        $vars = $story->getStringVars();
        switch ($columns) {
            case 1:
                $vars['bsClass'] = 'col-12';
                break;
            case 2:
                $vars['bsClass'] = 'col-12 col-sm-6';
                break;
            case 3:
                $vars['bsClass'] = 'col-12 col-sm-4';
                break;
            default:
                $vars['bsClass'] = 'col-12 col-sm-3';
        }
        $vars['publishDateRelative'] = $story->relativeTime($story->publishDate);
        $vars['format'] = "story-feature $format";
        $vars['published'] = 1;
        $vars['thumbnailStyle'] = $this->thumbnailStyle($story);
        $vars['publishInfo'] = $publishedView->publishBlock($vars);
        $template = new Template($vars);
        $template->setModuleTemplate('stories', 'Feature/Story.html');
        return $template->get();
    }

    private function thumbnailStyle(Resource $story)
    {
        $thumbnail = $story->thumbnail;
        $x = $story->x;
        $y = $story->y;
        $zoom = $story->zoom;
        return <<<EOF
background-image : url('$thumbnail');background-position: {$x}% {$y}%;background-size: {$zoom}%;
EOF;
    }

}
