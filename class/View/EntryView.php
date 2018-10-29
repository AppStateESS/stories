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
use stories\Resource\EntryResource as Resource;
use stories\Factory\EntryFactory as Factory;
use phpws2\Settings;
use stories\Factory\TagFactory;
use stories\Factory\StoryMenu;
use stories\Factory\HostFactory;
use stories\Factory\SettingFactory;
use stories\Exception\ResourceNotFound;

class EntryView extends View
{

    protected $factory;
    protected $isAdmin;

    public function __construct(bool $isAdmin = false)
    {
        $this->factory = new Factory;
        $this->isAdmin = $isAdmin;
    }

    /**
     * Adds publishing and share buttons to assoc. array used for view template
     * 
     * @param array $list
     * @param string $tag
     * @return array
     */
    private function addAccessories($list, $tag = null)
    {
        foreach ($list as $key => $value) {
            $newlist[$key] = $value;
            $newlist[$key]['publishInfo'] = $this->publishBlock($value, $tag);
            $newlist[$key]['shareButtons'] = $this->shareButtons($value);
        }
        return $newlist;
    }

    public function adminListView(Request $request)
    {
        $options = $this->pullListOptions($request);
        $options['limit'] = 10;
        $options['hideExpired'] = false;
        $options['showAuthor'] = true;
        $options['publishedOnly'] = false;
        $result['listing'] = $this->factory->pullList($options);
        $result['more_rows'] = $this->factory->more_rows;
        return $result;
    }

    public function form(Resource $entry, $new = false)
    {
        if (\phpws2\Settings::get('stories', 'hideDefault')) {
            \Layout::hideDefault(true);
        }
        $tagFactory = new TagFactory();

        $sourceHttp = PHPWS_SOURCE_HTTP;
        $status = $new ? 'Draft' : 'Last updated ' . $entry->relativeTime($entry->updateDate);
        $entryVars = $entry->getStringVars();
        $entryVars['content'] = $this->prepareFormContent($entryVars['content']);
        $tags = $tagFactory->listTags(true);
        $hostFactory = new HostFactory;
        $shareList = $hostFactory->getHostsSelect();
        $jsonVars = array('entry' => $entryVars, 'tags' => empty($tags) ? array() : $tags, 'status' => $status, 'shareList' => $shareList);
        $vars['publishBar'] = $this->scriptView('Publish', true, $jsonVars);
        $vars['imageOrientation'] = $this->scriptView('ImageOrientation');
        $vars['tagBar'] = $this->scriptView('TagBar');
        $vars['authorBar'] = $this->scriptView('AuthorBar');
        $vars['navbar'] = $this->scriptView('Navbar');

        $vars['home'] = $sourceHttp;
        $this->loadTwitterScript(true);
        $this->scriptView('MediumEditorPack', false);
        $this->scriptView('EntryForm', false);
        $this->scriptView('Sortable', false);

        $insertSource = "{$sourceHttp}mod/stories/javascript/MediumEditor/insert.js";
        \Layout::addJSHeader("<script src='$insertSource'></script>");

        \Layout::addJSHeader('<script>editor.setContent(entry.content)</script>');

        $template = new \phpws2\Template($vars);
        $this->mediumCSSOverride();
        $template->setModuleTemplate('stories', 'Entry/Form.html');
        return $template->get();
    }

    private function includeFacebookCards(Resource $entry)
    {
        $vars = $entry->getStringVars(true);
        $templateFile = PHPWS_SOURCE_DIR . 'mod/stories/api/Facebook/meta.html';
        $template = new \phpws2\Template($vars, $templateFile);
        return $template->get();
    }

    private function includeTwitterCards(Resource $entry)
    {
        $vars = $entry->getStringVars(true);

        // need to pull author twitter name
        $twitterUsername = \phpws2\Settings::get('stories', 'twitterDefault');
        if (!empty($twitterUsername)) {
            $vars['twitterUsername'] = $twitterUsername;
        }
        $templateFile = PHPWS_SOURCE_DIR . 'mod/stories/api/Twitter/meta.html';
        $template = new \phpws2\Template($vars, $templateFile);
        return $template->get();
    }

    private function includeCards(Resource $entry)
    {
        \Layout::addJSHeader($this->includeFacebookCards($entry));
        \Layout::addJSHeader($this->includeTwitterCards($entry));
    }

    /**
     * 
     * @param Request $request
     * @return string
     */
    public function listing(Request $request)
    {
        $this->includeCss();
        $listOptions = $this->pullListOptions($request);
        $settingsFactory = new \stories\Factory\SettingsFactory;
        $settings = $settingsFactory->listing();

        $list = $this->factory->pullList($listOptions);

        if (empty($list)) {
            return null;
        }

        $tag = $listOptions['tag'] ?? null;

        //format  - 0 is summary, 1 full
        if ($tag) {
            $data['title'] = "Stories for tag <strong>$tag</strong>";
            // tag searches show stories in summary mode
            $format = 0;
        } else {
            $data['title'] = null;
            $format = $settings['listStoryFormat'];
        }

        // Twitter feeds don't show up in summary view
        if ($format) {
            $this->loadTwitterScript();
        }

        $data['list'] = $this->addAccessories($list, $tag);
        $data['style'] = '';
        $data['isAdmin'] = $this->isAdmin;
        $data['showAuthor'] = $settings['showAuthor'];
        $this->scriptView('Caption', false);
        $this->scriptView('Tooltip', false);

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

        if (empty($listOptions['tag'])) {
            $data['url'] = 'Listing';
        } else {
            $data['url'] = 'Tag/' . $listOptions['tag'];
        }

        $template = new \phpws2\Template($data);
        $templateFile = $format ? 'FrontPageFull.html' : 'FrontPageSummary.html';
        $template->setModuleTemplate('stories', $templateFile);

        return $template->get();
    }

    /**
     * Medium editor insert doesn't initialize the Twitter embed. Has to be done
     * manually. The editor includes a script call to widgets BUT that is stripped
     * by our parser. It also showed it at different times. Just in case, 
     * there is the include parameter if we get repeat includes.
     * @param boolean $include - If true, include a link to twitter's widget file.
     * @return string
     */
    private function loadTwitterScript($include = true)
    {
        $includeFile = '<script src="//platform.twitter.com/widgets.js"></script>';
        $homeHttp = PHPWS_SOURCE_HTTP;
        $script = <<<EOF
<script src="{$homeHttp}mod/stories/javascript/MediumEditor/loadTwitter.js"></script>
EOF;
        if ($include) {
            \Layout::addJSHeader($includeFile . $script);
        } else {
            \Layout::addJSHeader($script);
        }
    }

    /**
     * Adds the medium-insert overlay that allows videos to be edited.
     * @param type $content
     * @return type
     */
    public function prepareFormContent($content)
    {
        $content = str_replace("\n", '', $content);
        $suffix = '<p class="medium-insert-active"><br /></p>';
        if (preg_match('@<p class="medium-insert-active"><br /></p>$@', $content)) {
            $suffix = null;
        }
        $contentReady = preg_replace("/<\/figure>/",
                        '</figure><div class="medium-insert-embeds-overlay"></div>',
                        $content) . $suffix;
        $fixCaption = str_replace('<figcaption',
                '<figcaption contenteditable="true"', $contentReady);
        return $fixCaption;
    }

    public function publishBlock($data, $tag = null)
    {
        $showAuthor = \phpws2\Settings::get('stories', 'showAuthor');
        $tagFactory = new TagFactory;
        if (!empty($data['tags'])) {
            $data['tagList'] = $tagFactory->getTagLinks($data['tags'],
                    $data['id'], $tag);
        } else {
            $data['tagList'] = null;
        }
        $data['showAuthor'] = $showAuthor;
        $data['publishDateRelative'] = ucfirst($data['publishDateRelative']);

        $template = new \phpws2\Template($data);
        $template->setModuleTemplate('stories', 'Publish.html');
        return $template->get();
    }

    private function relativeImages($content)
    {
        return preg_replace('@src="https?://[\w:/]+(images/stories/\d+/[^"]+)"@',
                'src="./$1"', $content);
    }

    public function shareButtons($data)
    {
        $template = new \phpws2\Template($data);
        $template->setModuleTemplate('stories', 'ShareButtons.html');
        return $template->get();
    }

    public function inListView($id)
    {
        static $twitterIncluded = false;

        \Layout::addJSHeader($this->mediumCSSOverride());
        $entry = $this->factory->load($id);
        $data = $this->factory->data($entry, !$this->isAdmin);
        if (empty($data)) {
            throw new ResourceNotFound;
        }
        $this->includeCards($entry);

        if ($entry->listView === 1) {
            if (!$twitterIncluded && stristr($entry->content, 'twitter')) {
                $this->loadTwitterScript(true);
                $twitterIncluded = true;
            }

            $data['content'] = preg_replace('/<p class="">::summary(<br\s?/?>)</p>/',
                    '', $data['content']);
            $templateFile = 'Entry/FullListView.html';
        } else {
            $templateFile = 'Entry/SummaryListView.html';
        }
        // Removed the summary break tag
        $address = \Canopy\Server::getSiteUrl();
        $data['currentUrl'] = $address . 'stories/Entry/' . $entry->urlTitle;
        $data['publishInfo'] = $this->publishBlock($data);
        $data['shareButtons'] = $this->shareButtons($data);
        $data['isAdmin'] = $this->isAdmin;

        $template = new \phpws2\Template($data);
        $template->setModuleTemplate('stories', $templateFile);
        $this->addStoryCss();
        return $template->get();
    }

    public function view($id)
    {
        if (\phpws2\Settings::get('stories', 'hideDefault')) {
            \Layout::hideDefault(true);
        }
        try {
            $this->includeCss();
            $entry = $this->factory->load($id);
            $data = $this->factory->data($entry, !$this->isAdmin);
            if (empty($data)) {
                throw new ResourceNotFound;
            }
            $this->includeCards($entry);
            if (stristr($entry->content, 'twitter')) {
                $this->loadTwitterScript(true);
            }
            // Removed the summary break tag
            $data['content'] = str_replace('<p class="">::summary</p>', '',
                    $data['content']);
            $address = \Canopy\Server::getSiteUrl();
            $data['currentUrl'] = $address . 'stories/Entry/' . $entry->urlTitle;
            $data['publishInfo'] = $this->publishBlock($data);
            $data['shareButtons'] = $this->shareButtons($data);
            $data['cssOverride'] = '';
            $data['isAdmin'] = $this->isAdmin;
            if (\phpws2\Settings::get('stories', 'showComments')) {
                $data['commentCode'] = \phpws2\Settings::get('stories',
                                'commentCode');
            } else {
                $data['commentCode'] = null;
            }

            $this->scriptView('Caption', false);
            $this->scriptView('Tooltip', false);
            $template = new \phpws2\Template($data);
            $template->setModuleTemplate('stories', 'Entry/View.html');
            return $template->get();
        } catch (ResourceNotFound $e) {
            return $this->notFound();
        }
    }

    public function notFound()
    {
        $template = new \phpws2\Template();
        $template->setModuleTemplate('stories', 'Entry/NotFound.html');
        return $template->get();
    }

}
