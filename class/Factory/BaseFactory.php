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

use stories\Exception\ResourceNotFound;
use phpws2\Settings;
use phpws2\Database;
use Canopy\Request;

/**
 *
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 */
abstract class BaseFactory extends \phpws2\ResourceFactory
{

    abstract public function build();

    public function load($id)
    {
        if (empty($id)) {
            throw new ResourceNotFound;
        }
        $resource = $this->build();
        $resource->setId($id);
        if (!parent::loadByID($resource)) {
            throw new ResourceNotFound($id);
        }
        return $resource;
    }

    public function scriptView($view_name, $add_anchor = true)
    {
        static $vendor_included = false;
        if (!$vendor_included) {
            $script[] = $this->getScript('vendor');
            $vendor_included = true;
        }
        $script[] = $this->getScript($view_name);
        $react = implode("\n", $script);
        if ($add_anchor) {
            $content = <<<EOF
<div id="$view_name"></div>
$react
EOF;
            return $content;
        } else {
            return $react;
        }
    }

    protected function walkingCase($name)
    {
        if (stripos($name, '_')) {
            return preg_replace_callback('/^(\w)(\w*)_(\w)(\w*)/',
                    function($letter) {
                $str = strtoupper($letter[1]) . $letter[2] . strtoupper($letter[3]) . $letter[4];
                return $str;
            }, $name);
        } else {
            return ucfirst($name);
        }
    }

    protected function getStoriesRootDirectory()
    {
        return PHPWS_SOURCE_HTTP . 'mod/stories/';
    }

    private function getScript($scriptName)
    {
        $root_directory = $this->getStoriesRootDirectory() . 'javascript/';
        if (STORIES_REACT_DEV) {
            $path = "dev/$scriptName.js";
        } else {
            $path = $this->getAssetPath($scriptName);
        }
        $script = "<script type='text/javascript' src='{$root_directory}$path'></script>";
        return $script;
    }

    private function getAssetPath($scriptName)
    {
        $jsonRaw = file_get_contents($this->getStoriesRootDirectory() . 'assets.json');
        $json = json_decode($jsonRaw, true);
        if (!isset($json[$scriptName]['js'])) {
            throw new \Exception('Script file not found among assets.');
        }
        return $json[$scriptName]['js'];
    }

    public function relativeTime($date)
    {
        $timepassed = time() - mktime(0, 0, 0, strftime('%m', $date),
                        strftime('%d', $date), strftime('%Y', $date));

        $rawday = ($timepassed / 86400);
        $days = floor($rawday);

        switch ($days) {
            case 0:
                return 'Today at ' . strftime('%l:%M%P', $date);

            case 1:
                return 'Yesterday at ' . strftime('%l:%M%P', $date);

            case -1:
                return 'Tomorrow at ' . strftime('%l:%M%P', $date);

            case ($days > 0 && $days < STORIES_DAY_THRESHOLD):
                return "$days days ago";

            case ($days < 0 && abs($days) < STORIES_DAY_THRESHOLD):
                return 'in ' . abs($days) . ' days';

            default:
                if (strftime('%Y', $date) != strftime('%Y')) {
                    return strftime('%b %e, %g', $date);
                } else {
                    return strftime('%b %e', $date);
                }
        }
    }

}
