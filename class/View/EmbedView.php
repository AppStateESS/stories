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
use stories\Factory\EmbedFactory as Factory;

class EmbedView extends View
{

    protected $factory;

    public function __construct()
    {
        $this->factory = new Factory;
    }

    public function embed(Request $request)
    {
        $id = $request->shiftCommand();
        return $this->getEmbed($request->pullGetString('url'), $id);
    }

    public function getEmbed($url, $id)
    {
        switch (1) {
            case preg_match('/^https?:\/\/(www\.)?youtube\.com/', $url):
                $json = $this->youtube($url);
                break;

            case preg_match('/https?:\/\/vimeo.com/', $url):
                $json = $this->vimeo($url);
                break;

            case preg_match('/https?:\/\/twitter.com/', $url):
                $json = $this->twitter($url);
                break;

            case preg_match('/https?:\/\/(www\.)?instagram.com/', $url):
                $json = $this->instagram($url);
                break;

            case preg_match('/https?:\/\/(www\.)?facebook.com/', $url):
                $json = $this->facebook($url);
                break;

            case preg_match('/https?:\/\/(www\.)?soundcloud.com/', $url):
                $json = $this->soundcloud($url);
                break;

            case preg_match('/https?:\/\/(www\.)?flickr.com/', $url):
                $json = $this->flickr($url);
                break;

            default:
                return null;
        }
        if (!is_array($json) && !is_object($json)) {
            throw new \Exception('Expected an array or object result from embed');
        }
        $this->addThumbnail($json, $id);
        return $json;
    }

    public function flickr($url)
    {
        $result = file_get_contents("https://www.flickr.com/services/oembed/?format=json&url=$url");
        return json_decode($result);
    }

    public function soundcloud($url)
    {
        $result = file_get_contents("https://soundcloud.com/oembed?format=json&url=$url");
        return json_decode($result);
    }

    public function youtube($url)
    {
        $result = file_get_contents("https://www.youtube.com/oembed?url=$url");
        $json = json_decode($result);
        return $json;
    }

    public function instagram($url)
    {
        $result = file_get_contents("https://api.instagram.com/oembed?url=$url");
        return json_decode($result);
    }

    public function twitter($url)
    {
        $options = array(CURLOPT_URL => "https://publish.twitter.com/oembed?url=$url", CURLOPT_HEADER => 0, CURLOPT_RETURNTRANSFER => true);
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        if (!$result = curl_exec($ch)) {
            throw new \Exception('Cannot to connect to Twitter');
        }
        curl_close($ch);
        return json_decode($result);
    }

    public function facebook($url)
    {
        $browser = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.16 (KHTML, like Gecko) \Chrome/24.0.1304.0 Safari/537.16';
        $options = array(CURLOPT_URL => "https://www.facebook.com/plugins/post/oembed.json/?url=$url", CURLOPT_HEADER => 0, CURLOPT_RETURNTRANSFER => true, CURLOPT_USERAGENT => $browser);
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        if (!$result = curl_exec($ch)) {
            throw new \Exception('Cannot to connect to media');
        }
        curl_close($ch);
        return json_decode($result);
    }

    public function vimeo($url)
    {
        $result = file_get_contents("https://vimeo.com/api/oembed.json?url=$url");
        $json = json_decode($result);
        return $json;
    }
    
    private function addThumbnail($json, $id)
    {
        if (empty($json->thumbnail_url)) {
            return;
        }
        $entryFactory = new \stories\Factory\EntryFactory;
        $entry = $entryFactory->load($id);
        if (empty($entry->thumbnail)) {
            $entry->thumbnail = $json->thumbnail_url;
        }
        if (empty($entry->leadImage)) {
            $entry->leadImage = $json->thumbnail_url;
        }
        $entryFactory->save($entry);
    }


}
