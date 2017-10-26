<?php

/*
 * The MIT License
 *
 * Copyright 2017 Matthew McNaney <mcnaneym@appstate.edu>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace stories\Factory;

use phpws2\Database;
use Canopy\Request;
use phpws2\Settings;
use stories\Resource\EntryResource;
use stories\Resource\ThumbnailResource;

require_once PHPWS_SOURCE_DIR . 'mod/stories/class/UploadHandler.php';

/**
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 */
class EntryPhotoFactory
{

    function build()
    {
        
    }

    public function save(Request $request)
    {
        $entryId = $request->pullPostInteger('entryId');
        $imageDirectory = "images/stories/$entryId/";
        $imagePath = PHPWS_HOME_DIR . $imageDirectory;

        $options = array(
            'max_width' => Settings::get('stories', 'image_max_width'),
            'max_height' => Settings::get('stories', 'image_max_height'),
            'corrent_image_extensions' => true,
            'upload_dir' => $imagePath,
            'upload_url' => \Canopy\Server::getSiteUrl(true) . $imageDirectory,
            'image_versions' => array()
        );
        $upload_handler = new \UploadHandler($options, false);
        $result = $upload_handler->post(false);
        return $result;
    }

    public function delete($entryId, Request $request)
    {
        $file = $request->pullDeleteString('file', true);
        if ($file === false) {
            return;
        }
        $path = \Canopy\Server::getSiteUrl(true) . 'images/stories/' . $entryId . '/';
        $filenameOnly = urldecode(preg_replace("@$path@", '', $file));
        $cleanName = "images/stories/$entryId/$filenameOnly";
        if (is_file($cleanName)) {
            unlink($cleanName);
        }
        $this->deleteThumbnail($entryId, $filenameOnly);
        return true;
    }

    public function getImagePath($entryId)
    {
        return 'images/stories/' . $entryId . '/';
    }

    public function getThumbnailPath($entryId)
    {
        return 'images/stories/' . $entryId . '/thumbnail/';
    }

    private function deleteThumbnail($entryId, $filename)
    {
        $thumbnailPath = $this->getThumbnailPath($entryId) . $filename;
        $thumbnailUrl = \Canopy\Server::getSiteUrl(true) . $thumbnailPath;
        if (is_file($thumbnailPath)) {
            unlink($thumbnailPath);
        }

        $db = Database::getDB();
        $tbl = $db->addTable('storiesEntry');
        $tbl->addFieldConditional('id', $entryId);
        $tbl->addFieldConditional('thumbnail', $thumbnailUrl);
        $tbl->addValue('thumbnail', null);
        $db->update();
    }

    private function getYouTubeId($src)
    {
        // https://stackoverflow.com/questions/2936467/parse-youtube-video-id-using-preg-match
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
                        $src, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

    private function getYouTubeThumbUrl($youTubeId)
    {
        return <<<EOF
https://img.youtube.com/vi/$youTubeId/0.jpg
EOF;
    }

    public function saveYouTubeImage($entryId, $src)
    {
        $youtubeId = $this->getYouTubeId($src);
        if (empty($youtubeId)) {
            return false;
        }
        $url = $this->getYouTubeThumbUrl($youtubeId);
        $content = file_get_contents($url);
        if (empty($content)) {
            return false;
        }
        $imageName = $youtubeId . '.jpg';
        $imagePath = $this->getImagePath($entryId);
        $fullPath = $imagePath . $imageName;
        if (!is_dir($imagePath)) {
            mkdir($imagePath, 0755);
        }

        if (file_put_contents($fullPath, $content) !== false) {
            $thumbnail = new ThumbnailResource($imagePath, $imageName);
            $thumbnail->createThumbnail();
            return array('thumbnail' => $thumbnail, 'image' => $fullPath);
        } else {
            return false;
        }
    }

    public function getImageFilename($url)
    {
        $urlArray = explode('/', $url);
        return array_pop($urlArray);
    }

    public function purgeEntry($entryId)
    {
        $thumbnailPath = $this->getThumbnailPath($entryId);
        $imagePath = $this->getImagePath($entryId);
        \phpws\PHPWS_File::rmdir($imagePath);
    }
}
