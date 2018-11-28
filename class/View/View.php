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
use phpws2\Template;

abstract class View
{

    protected $factory;

    private function addScriptVars($vars)
    {
        if (empty($vars)) {
            return null;
        }
        foreach ($vars as $key => $value) {
            if (is_array($value)) {
                $varList[] = "const $key = " . json_encode($value) . ';';
            } else {
                $varList[] = "const $key = '$value';";
            }
        }
        return '<script type="text/javascript">' . implode('', $varList) . '</script>';
    }

    public function includeCss()
    {
        $this->addStoryCss();
        $this->mediumCssOverride();
        $this->mediumInsertCss();
    }

    public function addStoryCss()
    {
        \Layout::addToStyleList('mod/stories/css/story.css');
    }

    public function mediumCssOverride()
    {
        \Layout::addToStyleList('mod/stories/css/MediumOverrides.css');
    }

    public function mediumInsertCss()
    {
        \Layout::addToStyleList('mod/stories/css/medium-editor-insert-plugin.min.css');
    }

    protected function getScript($scriptName)
    {
        $jsDirectory = $this->getStoriesRootUrl() . 'javascript/';
        if (STORIES_REACT_DEV) {
            $path = "{$jsDirectory}dev/$scriptName.js";
        } else {
            $path = $jsDirectory . 'build/' . $this->getAssetPath($scriptName);
        }
        $script = "<script type='text/javascript' src='$path'></script>";
        return $script;
    }

    protected function getAssetPath($scriptName)
    {
        $rootDirectory = $this->getStoriesRootDirectory();
        if (!is_file($rootDirectory . 'assets.json')) {
            exit('Missing assets.json file. Run "npm run build" in the stories directory.');
        }
        $jsonRaw = file_get_contents($rootDirectory . 'assets.json');
        $json = json_decode($jsonRaw, true);
        if (!isset($json[$scriptName]['js'])) {
            throw new \Exception('Script file not found among assets.');
        }
        return $json[$scriptName]['js'];
    }

    /**
     * 
     * @staticvar boolean $vendor_included
     * @param string $view_name
     * @param boolean $add_anchor
     * @param array $vars
     * @return string
     */
    public function scriptView($view_name, $add_anchor = true, $vars = null)
    {
        static $vendor_included = false;
        if (!$vendor_included) {
            $script[] = $this->getScript('vendor');
            $vendor_included = true;
        }
        if (!empty($vars)) {
            $script[] = $this->addScriptVars($vars);
        }
        $script[] = $this->getScript($view_name);
        $react = implode("\n", $script);
        \Layout::addJSHeader($react);
        if ($add_anchor) {
            $content = <<<EOF
<div id="$view_name"></div>
EOF;
            return $content;
        }
    }

    public function getStoriesRootDirectory()
    {
        return PHPWS_SOURCE_DIR . 'mod/stories/';
    }

    public function getStoriesRootUrl()
    {
        return PHPWS_SOURCE_HTTP . 'mod/stories/';
    }

    public function pullListOptions(Request $request)
    {
        $settingFactory = new \stories\Factory\SettingsFactory;
        $settings = $settingFactory->listing();
        // if offset not set, default 0
        $page = (int) $request->pullGetInteger('page', true);
        if ($page > 1) {
            $offsetSize = $settings['listStoryAmount'] * ($page - 1);
        } else {
            $offset = $request->pullGetInteger('offset', true);
            if ($offset > 0) {
                $offsetSize = $settings['listStoryAmount'] * $offset;
            } else {
                $offsetSize = 0;
            }
            $page = 1;
        }

        $sortBy = $request->pullGetString('sortBy', true);
        if (!in_array($sortBy, array('publishDate', 'title', 'updateDate'))) {
            $sortBy = 'publishDate';
        }

        $search = $request->pullGetString('search', true);

        $tag = str_replace('%20', ' ', $request->pullGetString('tag', true));

        $options = array(
            'search' => $search,
            'sortBy' => $sortBy,
            'offset' => $offsetSize,
            'page' => $page,
            'tag' => $tag
        );
        $options['showAuthor'] = $settings['showAuthor'];
        $options['limit'] = $settings['listStoryAmount'];
        return $options;
    }

    protected function imageZoom()
    {
        $this->scriptView('ImageZoom', false);
        $template = new Template();
        $template->setModuleTemplate('stories', 'ImageZoom.html');
        return $template->get();
    }

}
