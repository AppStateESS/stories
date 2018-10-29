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

use stories\Factory\EntryFactory;
use stories\Factory\PublishFactory;
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
        $this->scriptView('Caption', false);
        $this->scriptView('Tooltip', false);
        $listOptions = $this->pullListOptions($request);

        $tag = $listOptions['tag'] ?? null;
        //format  - 0 is summary, 1 full
        if ($tag) {
            $data['title'] = "Stories for tag <strong>$tag</strong>";
            // tag searches show stories in summary mode
            $format = 0;
        } else {
            $data['title'] = null;
        }

        $content = [];
        $shareView = new ShareView;
        $entryView = new EntryView;
        \Layout::addJSHeader($entryView->mediumCSSOverride());
        $items = $this->factory->listing();
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
        $vars['stories'] = implode("\n", $content);
        
        $vars['prevpage'] = null;
        $vars['nextpage'] = null;
        if ($listOptions['page'] > 1) {
            $data['prevpage'] = $listOptions['page'] - 1;
        } else {
            $data['prevpage'] = null;
        }
        if ($this->factory->more_rows) {
            $data['nextpage'] = $listOptions['page'] + 1;
        } else {
            $data['nextpage'] = null;
        }
        
        $template = new Template($vars);
        $template->setModuleTemplate('stories', 'FrontPage.html');
        return $template->get();
    }

}
