<?php

/*
 * See docs/AUTHORS and docs/COPYRIGHT for relevant info.
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @author Matthew McNaney <mcnaney at gmail dot com>
 *
 * @license http://opensource.org/licenses/lgpl-3.0.html
 */

namespace stories\Controller;

define('STORIES_FRIENDLY_MESSAGE', 'Server error. Could not complete action');

class FriendlyError extends \phpws2\Http\Controller
{
    public function execute(\Canopy\Request $request)
    {
        if ($request->isAjax()) {
            throw new \Exception(STORIES_FRIENDLY_MESSAGE);
        }
        return parent::execute($request);
    }
    
    public function get(\Canopy\Request $request)
    {
        $template = new \phpws2\Template();
        $template->setModuleTemplate('stories', 'error.html');
        $template->add('message', STORIES_FRIENDLY_MESSAGE);
        $view = new \phpws2\View\HtmlView($template->get());
        $response = new \Canopy\Response($view);
        return $response;
    }

}
