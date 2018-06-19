<?php

/**
 * MIT License
 * Copyright (c) 2017 Electronic Student Services @ Appalachian State University
 * 
 * See LICENSE file in root directory for copyright and distribution permissions.
 * 
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 *
 */

namespace stories;

use Canopy\Request;
use Canopy\Response;
use Canopy\Server;

require_once PHPWS_SOURCE_DIR . 'src/Module.php';

class Module extends \Canopy\Module implements \Canopy\SettingDefaults
{

    public function __construct()
    {
        parent::__construct();
        $this->loadDefines();
        $this->setTitle('stories');
        $this->setProperName('Stories');
        spl_autoload_register('\stories\Module::autoloader', true, true);
    }

    public function getSettingDefaults()
    {
        // segmentSize : how many stories to show on the admin list page
        // listStoryFormat : 0 - summary, 1 - full
        // featureFormat : 0 - dynamic, 1 - horizontal, 2 - portrait
        $settings = array(
            'image_max_width' => 1920,
            'image_max_height' => 1080,
            'segmentSize' => 10,
            'showComments' => 0,
            'showAuthor' => 0,
            'commentCode' => '',
            'hideDefault' => 0,
            'listStories' => 1,
            'listStoryAmount' => 6,
            'listStoryFormat' => 0,
            'twitterDefault' => '');
        return $settings;
    }

    public function getController(Request $request)
    {
        try {
            $controller = new Controller\BaseController($this, $request);
            return $controller;
        } catch (\Exception $e) {
            if (STORIES_FRIENDLY_ERROR) {
                \phpws2\Error::log($e);
                $controller = new Controller\FriendlyError($this);
                return $controller;
            } else {
                throw $e;
            }
        }
    }

    private function friendlyController()
    {
        $error_controller = new Controller\FriendlyError($this);
        return $error_controller;
    }

    private function loadDefines()
    {
        $dist = PHPWS_SOURCE_DIR . 'mod/stories/config/defines.dist.php';
        $custom = PHPWS_SOURCE_DIR . 'mod/stories/config/defines.php';
        if (is_file($custom)) {
            require_once $custom;
        } else {
            require_once $dist;
        }
    }

    public function runTime(Request $request)
    {
        if (\Current_User::allow('stories')) {
            \stories\Factory\StoryMenu::addStoryLink();
            \stories\Factory\StoryMenu::authorLink();
            \stories\Factory\StoryMenu::featureLink();
            \stories\Factory\StoryMenu::listStoryLink();
            \stories\Factory\StoryMenu::adminDisplayLink();
        }
        $this->frontPage($request);
    }

    private function frontPage(Request $request)
    {
        if (!$request->isGet() || $request->getUrl() != '/') {
            return;
        }
        $entryFactory = new \stories\Factory\EntryFactory;
        $featureFactory = new \stories\Factory\FeatureFactory();
        $settings = new \phpws2\Settings;
        \Layout::add($featureFactory->show($request), 'stories', 'features',
                true);

        if ($settings->get('stories', 'listStories')) {
            \Layout::add($entryFactory->showStories($request), 'stories',
                    'stories', true);
        }
    }

    public static function autoloader($class_name)
    {
        static $not_found = array();

        if (strpos($class_name, 'stories') !== 0) {
            return;
        }

        if (isset($not_found[$class_name])) {
            return;
        }
        $class_array = explode('\\', $class_name);
        array_shift($class_array);
        $class_dir = implode('/', $class_array);

        $class_path = PHPWS_SOURCE_DIR . 'mod/stories/class/' . $class_dir . '.php';
        if (is_file($class_path)) {
            require_once $class_path;
            return true;
        } else {
            $not_found[] = $class_name;
            return false;
        }
    }

}
