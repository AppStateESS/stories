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
use stories\Factory\EntryPhotoFactory;
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

    private function getImageOptions(Request $request)
    {
        $entryId = $request->pullPostInteger('entryId');
        $imageDirectory = "images/stories/$entryId/";
        $imagePath = PHPWS_HOME_DIR . $imageDirectory;
        $options = array(
            'max_width' => Settings::get('stories', 'image_max_width'),
            'max_height' => Settings::get('stories', 'image_max_height'),
            'current_image_extensions' => true,
            'upload_dir' => $imagePath,
            'upload_url' => \Canopy\Server::getSiteUrl(true) . $imageDirectory,
            'image_versions' => array()
        );
        return $options;
    }
    
    private function getThumbOptions(Request $request)
    {
        $entryId = $request->pullPostInteger('entryId');
        $imageDirectory = "images/stories/$entryId/thumbnail/";
        $imagePath = PHPWS_HOME_DIR . $imageDirectory;
        $options = array(
            'max_width' => STORIES_THUMB_TARGET_WIDTH,
            'max_height' => STORIES_THUMB_TARGET_HEIGHT,
            'current_image_extensions' => true,
            'upload_dir' => $imagePath,
            'upload_url' => \Canopy\Server::getSiteUrl(true) . $imageDirectory,
            'image_versions' => array()
        );
        return $options;
    }

    public function save(Request $request)
    {
        $options = $this->getImageOptions($request);
        $upload_handler = new \UploadHandler($options, false);
        $result = $upload_handler->post(false);
        return $result;
    }

    public function postThumbnail(Request $request)
    {
        $entryFactory = new EntryFactory;
        $entry = $entryFactory->load($request->pullPostInteger('entryId'));
        
        $options = $this->getThumbOptions($request);
        $options['param_name'] = 'image';
        $filename = $request->getUploadedFileArray('image');
        $upload_handler = new \UploadHandler($options, false);
        $result = $upload_handler->post(false);
        $imageFile = $result['image'][0]->name;
        $imageDirectory = $this->getThumbnailPath($entry->id);

        $this->deleteThumbnail($entry->id, $entry->thumbnail);
        
        $entry->thumbnail = $imageDirectory . $imageFile;
        $entryFactory->save($entry);
        return $entry->thumbnail;
    }
    
    /**
     * Creates a thumbnail based on object variables.
     */
    public function createThumbnail($sourceDirectory, $filename)
    {
        $source = $sourceDirectory . $filename;
        if (!is_file($source)) {
            return false;
        }
        list($width, $height) = getimagesize($source);
        $maxWidth = $width <= STORIES_THUMB_TARGET_WIDTH ? $width : STORIES_THUMB_TARGET_WIDTH;
        $maxHeight = $height <= STORIES_THUMB_TARGET_HEIGHT ? $height : STORIES_THUMB_TARGET_HEIGHT;

        $options = array('image_library' => true, 'upload_dir' => $sourceDirectory);
        $upload = new \UploadHandler($options, false);

        $scaledOptions = array(
            'max_width' => $maxWidth,
            'max_height' => $maxHeight,
            'crop' => true,
            'jpeg_quality' => 100
        );

        $upload->create_scaled_image($filename, 'thumbnail',
                $scaledOptions);
        return $sourceDirectory . 'thumbnail/' . $filename;
    }

    public function delete($entryId, $file, $thumb = true)
    {
        $entryFactory = new EntryFactory;
        $entry = $entryFactory->load($entryId);
        $entryUpdated = false;
        
        $filenameOnly = $this->getImageFilename($file);
        $cleanName = "images/stories/$entryId/$filenameOnly";
        if ($entry->leadImage == $cleanName) {
            $entryUpdated = true;
            $entry->leadImage = null;
        }
        if (is_file($cleanName)) {
            unlink($cleanName);
        }
        if ($thumb) {
            $thumbpath = $this->getThumbnailPath($entryId) . $filenameOnly;
            if ($entry->thumbnail == $thumbpath) {
                $entryUpdated = true;
                $entry->thumbnail = null;
            }
            $this->deleteThumbnail($entryId, $filenameOnly);
        }
        if ($entryUpdated) {
            $entryFactory->save($entry);
        }
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
            $thumbnail = $this->createThumbnail($imagePath, $imageName);
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
