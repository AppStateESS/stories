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
        return $this->getEmbed($request->pullGetString('url'));
    }

    public function getEmbed($url)
    {
        switch (1) {
            case preg_match('/^https?:\/\/(www\.)?youtube\.com/', $url):
                $result =  $this->youtube($url);
                break;

            case preg_match('/https?:\/\/vimeo.com/', $url):
                $result =  $this->vimeo($url);
                break;

            case preg_match('/https?:\/\/twitter.com/', $url):
                $result =  $this->twitter($url);
                break;

            case preg_match('/https?:\/\/(www\.)?instagram.com/', $url):
                $result =  $this->instagram($url);
                break;

            case preg_match('/https?:\/\/(www\.)?facebook.com/', $url):
                $result =  $this->facebook($url);
                break;

            case preg_match('/https?:\/\/(www\.)?soundcloud.com/', $url):
                $result =  $this->soundcloud($url);
                break;

            case preg_match('/https?:\/\/(www\.)?flickr.com/', $url):
                $result =  $this->flickr($url);
                break;

            default:
                return null;
        }
        if (!is_array($result) && !is_object($result)) {
            throw new \Exception('Expected an array or object result from embed');
        }
        return $result;
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
        $id = preg_replace('/.*v=(\w+)$/', '\\1', $url);
        return array(
            'html' => "<div style=\"left: 0; width: 100%; height: 0; position: relative; padding-bottom: 56.2493%;\"><iframe src=\"https://www.youtube.com/embed/$id?rel=0&amp;showinfo=0\" style=\"border: 0; top: 0; left: 0; width: 100%; height: 100%; position: absolute;\" allowfullscreen scrolling=\"no\"></iframe></div>"
        );
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
        $id = preg_replace('/.*\/(\d+)$/', '\\1', $url);
        return array(
            "html" => "<div style=\"left: 0; width: 100%; height: 0; position: relative; padding-bottom: 56.2493%;\"><iframe src=\"https://player.vimeo.com/video/$id?byline=0&amp;badge=0&amp;portrait=0&amp;title=0\" style=\"border: 0; top: 0; left: 0; width: 100%; height: 100%; position: absolute;\" allowfullscreen scrolling=\"no\"></iframe></div>"
        );
    }

}
