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

require_once PHPWS_SOURCE_DIR . 'mod/stories/lib/UploadHandler.php';

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
        /*
        if (!is_dir($imagePath)) {
            mkdir($imagePath);
        }
        */
        $options = array(
            'max_width' => 1920,
            'max_height' => 1080,
            'corrent_image_extensions' => true,
            'upload_dir' => $imagePath,
            'upload_url' => \Canopy\Server::getSiteUrl(true) . $imageDirectory
        );
        $upload_handler = new \UploadHandler($options, false);
        return $upload_handler->post(false);
    }

}
