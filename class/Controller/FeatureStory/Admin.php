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

namespace stories\Controller\FeatureStory;

use stories\Controller\RoleController;
use stories\Factory\FeatureStoryFactory;
use stories\Factory\PublishFactory;
use Canopy\Request;

class Admin extends RoleController
{

    /**
     * @var \stories\Factory\FeatureStoryFactory
     */
    protected $factory;

    protected function loadFactory()
    {
        $this->factory = new FeatureStoryFactory;
    }

    protected function loadView()
    {
        
    }

    protected function listJsonCommand(Request $request)
    {
        $publishFactory = new PublishFactory;
        $publishedTitles = $publishFactory->featureList();
        $featureStories = $this->factory->listing($request->pullGetInteger('featureId'),
                        false) ?? [];
        $filteredTitles = $this->filterPublishedTitles($featureStories, $publishedTitles);

        return ['featureStories' => $featureStories, 'publishedTitles' => $filteredTitles];
    }
    
    private function filterPublishedTitles($featureStories, $publishedTitles) {
        $publishIds = [];
        foreach ($featureStories as $f) {
            $publishIds[] = $f['publishId'];
        }
        $filteredTitles = [];
        foreach ($publishedTitles as $val) {
            if (!in_array($val['id'], $publishIds)) {
                $filteredTitles[] = $val;
            }
        }
        return $filteredTitles;
    }

    protected function postCommand(Request $request)
    {
        $this->factory->post($request->pullPostInteger('featureId'),
                $request->pullPostInteger('publishId'));
        return ['success' => true];
    }

    protected function putCommand(Request $request)
    {
        $this->factory->put($this->id, $request->pullPutInteger('publishId'));
        return ['success' => true];
    }

    protected function deleteCommand(Request $request)
    {
        $this->factory->delete($this->id);
        return ['success' => true];
    }

    protected function patchCommand(Request $request)
    {
        $this->factory->patch($this->id, $request);
        return ['success' => true];
    }

    protected function updateThumbnailPostCommand(Request $request)
    {
        $this->factory->updateEntryThumbnail($request);
        return ['success' => true];
    }

}
