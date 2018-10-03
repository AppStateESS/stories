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

    public function addStoryCss()
    {
        \Layout::addToStyleList('mod/stories/css/story.css');
    }

    public function mediumCSSOverride()
    {
        $css = "mod/stories/css/MediumOverrides.css";
        \Layout::addToStyleList($css);
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

}
