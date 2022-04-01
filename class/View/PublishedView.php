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

namespace stories\View;

use stories\Factory\EntryFactory;
use stories\Factory\PublishFactory;
use stories\Factory\TagFactory;
use stories\View\ShareView;
use stories\View\EntryView;
use phpws2\Template;
use Canopy\Request;

class PublishedView extends View
{

    public function __construct()
    {
        $this->factory = new PublishFactory;
    }

    public function listing(Request $request)
    {
        $this->includeCss();
        //$this->scriptView('Caption', false);
        $this->scriptView('Tooltip', false, null, false);

        $listOptions = $this->pullListOptions($request);

        $tag = $listOptions['tag'] ?? null;
        if ($tag) {
            $tplVars['title'] = "Stories for tag <strong>$tag</strong>";
        } else {
            $tplVars['title'] = null;
        }

        $content = [];
        $shareView = new ShareView;
        $entryView = new EntryView;
        $items = $this->factory->listing($listOptions);
        if (empty($items)) {
            return null;
        }
        foreach ($items as $publish) {
            if ($publish['entryId'] > 0) {
                $content[] = $entryView->inListView($publish['entryId']);
            } elseif ($publish['shareId'] > 0) {
                $content[] = $shareView->view($publish['shareId']);
            } else {
                throw \Exception('Problem with published items');
            }
        }
        $tplVars['stories'] = implode("\n", $content);

        $tplVars['prevpage'] = null;
        $tplVars['nextpage'] = null;
        if ($listOptions['page'] > 1) {
            $tplVars['prevpage'] = $listOptions['page'] - 1;
        } else {
            $tplVars['prevpage'] = null;
        }
        if ($this->factory->more_rows) {
            $tplVars['nextpage'] = $listOptions['page'] + 1;
        } else {
            $tplVars['nextpage'] = null;
        }

        if (empty($listOptions['tag'])) {
            $tplVars['url'] = 'Entry';
        } else {
            $tplVars['url'] = 'Tag/' . $listOptions['tag'];
        }
        $tplVars['imageZoom'] = $this->imageZoom();

        $template = new Template($tplVars);
        $template->setModuleTemplate('stories', 'FrontPage.html');
        return $template->get();
    }

    public function publishBlock($data, $currentTag = null)
    {
        $data['showAuthor'] = \phpws2\Settings::get('stories', 'showAuthor');
        if (!empty($data['tags'])) {
            $tagFactory = new TagFactory;
            if (isset($data['shareId']) && $data['shareId'] > 0 && isset($data['siteUrl'])) {
                $data['tagList'] = $tagFactory->getTagLinks($data['tags'],
                    $data['id'], $currentTag, $data['siteUrl']);
            } else {
                $data['tagList'] = $tagFactory->getTagLinks($data['tags'],
                    $data['id'], $currentTag);
            }
        } else {
            $data['tagList'] = null;
        }

        $data['publishDateRelative'] = ucfirst($data['publishDateRelative']);
        $template = new \phpws2\Template($data);
        $template->setModuleTemplate('stories', 'Publish.html');
        return $template->get();
    }

}
