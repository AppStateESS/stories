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

namespace stories\Resource;

require_once PHPWS_SOURCE_DIR . 'mod/stories/class/UploadHandler.php';
require_once PHPWS_SOURCE_DIR . 'mod/stories/config/defines.php';

class ThumbnailResource
{

    /**
     * Location of the image used to create the thumbnail
     * @var string
     */
    private $sourceDirectory;

    /**
     * Directory of the thumbnail 
     * @var string
     */
    public $thumbDirectory;

    /**
     * Image filename
     * @var string
     */
    private $filename;

    /**
     * Pixel height of thumbnail
     * @var integer
     */
    public $height;

    /**
     * Pixel width of thumbnail
     * @var integer
     */
    public $width;

    /**
     * Orientation of the thumbnail
     * 0: landscape
     * 1: portrait
     * @var integer
     */
    public $orientation;
    
    public function __construct($sourceDirectory, $filename)
    {
        $this->filename = $filename;
        $this->sourceDirectory = $sourceDirectory;
        $this->thumbDirectory= $this->sourceDirectory . 'thumbnail/';
    }
    
    
    private function createPortraitThumbnail($width, $height)
    {
        $this->setPortraitDimensions($width, $height);
        $this->scaleImage();
    }

    private function createLandscapeThumbnail($width, $height)
    {
        $this->setLandscapeDimensions($width, $height);
        $this->scaleImage();
    }
    
    /**
     * Creates a thumbnail based on object variables.
     * Returns the orientation type (0 landscape, 1 portrait) of the thumbnail.
     */
    public function createThumbnail()
    {
        list($width, $height) = getimagesize($this->getSourcePath());
        $this->createPortraitThumbnail($width, $height);
        $this->createLandscapeThumbnail($width, $height);
        
        $this->loadDimensions();
        $this->scaleImage();
    }
    
    /**
     * Returns the full path of the thumbnail.
     * @return string
     */
    public function getPath()
    {
        return $this->thumbDirectory . $this->filename;
    }

    private function scaleImage()
    {
        $options = array('image_library' => true, 'upload_dir' => $this->sourceDirectory);
        $upload = new \UploadHandler($options, false);
        $scaledOptions = array(
            'max_width' => $this->width,
            'max_height' => $this->height,
            'crop' => true,
            'jpeg_quality' => 100
        );

        $upload->create_scaled_image($this->filename, 'thumbnail', $scaledOptions);
    }

    
    /**
     * Returns the pull path of the source image used to create the thumbnail
     * @return string
     */
    public function getSourcePath()
    {
        return $this->sourceDirectory . $this->filename;
    }

    private function setLandscapeDimensions($width, $height)
    {
        $this->orientation = 0;
        if ($width > STORIES_LANDSCAPE_THUMB_WIDTH) {
            $this->width = STORIES_LANDSCAPE_THUMB_WIDTH;
            $this->height = STORIES_LANDSCAPE_THUMB_HEIGHT;
        } else {
            $this->width = $width;
            if ($height > STORIES_LANDSCAPE_THUMB_HEIGHT) {
                $this->height = STORIES_LANDSCAPE_THUMB_HEIGHT;
            } else {
                $this->height = $height;
            }
        }
    }

    private function setPortraitDimensions($width, $height)
    {
        $this->orientation = 1;
        if ($height > STORIES_PORTRAIT_THUMB_HEIGHT) {
            $this->width = STORIES_PORTRAIT_THUMB_WIDTH;
            $this->height = STORIES_PORTRAIT_THUMB_HEIGHT;
        } else {
            $this->width = $height;
            if ($width > STORIES_PORTRAIT_THUMB_WIDTH) {
                $this->height = STORIES_PORTRAIT_THUMB_HEIGHT;
            } else {
                $this->height = $height;
            }
        }
    }

    private function loadDimensions()
    {
        list($width, $height) = getimagesize($this->getSourcePath());
        $ratio = $width / $height;

        $featureFormat = \phpws2\Settings::get('stories', 'featureFormat');
        if ($featureFormat != 2 && ($featureFormat == 1 || $ratio > STORIES_ORIENTATION_RATIO)) {
            $this->setLandscapeDimensions($width, $height);
        } else {
            $this->setPortraitDimensions($width, $height);
        }
    }

}
