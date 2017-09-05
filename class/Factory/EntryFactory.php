<?php

/**
 * MIT License
 * Copyright (c) 2017 Electronic Student Services @ Appalachian State University
 * 
 * See LICENSE file in root directory for copyright and distribution permissions.
 * 
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @license https://opensource.org/licenses/MIT
 */

namespace stories\Factory;
use stories\Resource\EntryResource as Resource;
use phpws2\Database;
use Canopy\Request;

class EntryFactory extends BaseFactory
{
    protected function build()
    {
        return new Resource;
    }
    
    public function listing(Request $request)
    {
        return 'listing of entries';
    }
    
    public function form()
    {
        $source = PHPWS_SOURCE_HTTP . 'mod/stories/javascript/MediumEditor/insert.js';
        $vars['home'] = PHPWS_SOURCE_HTTP;
        $vars['required'] = $this->reactView('EntryForm');
        $vars['title'] = '';
        $vars['content'] = "";
        $vars['editor'] = <<<EOF
<script src="$source"></script>
EOF;
        $template = new \phpws2\Template($vars);
        $template->setModuleTemplate('stories', 'Entry/Form.html');
        return $template->get();
    }
}
