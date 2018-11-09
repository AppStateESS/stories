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

    public function listing(int $featureId, string $format)
    {
        $listing = $this->factory->listing($featureId);
        $totalColumns = count($listing);
        $showAuthor = \phpws2\Settings::get('stories', 'showAuthor');
        foreach ($listing as $key => $story) {
            $columns[] = $this->view($story, $format, $totalColumns, $key + 1);
        }
        return $columns;
    }

    private function getColumnClass(int $totalColumns, int $currentCount)
    {
        switch ($totalColumns) {
            case 1:
                return 'col-12';
            case 2:
                return 'col-sm-6';
            case 3:
                return 'col-sm-4';
            case 4:
            case 8:
                return 'col-md-3 col-sm-6';
            case 5:
                if ($currentCount > 3) {
                    return 'col-sm-6';
                } else {
                    return 'col-sm-4';
                }
            case 6:
                return 'col-sm-4';
            case 7:
                if ($currentCount == 4) {
                    return 'col-md-3 col-sm-6';
                } elseif ($currentCount >= 4) {
                    return 'col-md-4 col-sm-6';
                } else {
                    return 'col-sm-4 col-md-3';
                }
            default:
                return 'col-sm-3';
        }
    }

    public function view(Resource $story, string $format, int $totalColumns,
            int $currentCount)
    {
        $publishedView = new PublishedView;
        $tag = null;
        $vars = $story->getStringVars();
        $vars['bsClass'] = $this->getColumnClass($totalColumns, $currentCount);
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
