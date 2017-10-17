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
define('STORIES_FRIENDLY_ERROR', false);

define('STORIES_REACT_DEV', true);

define('STORIES_CONTENT_TAGS',
        'b,strong,em,i,p,img,iframe,h2,h3,h4,h5,a,style,blockquote,ul,ol,li,figure,figcaption,div,twitterwidget');

define('STORIES_SUMMARY_TAGS', 'b,strong,em,i,a,style,ul,ol,li,p');

define('STORIES_DAY_THRESHOLD', 3);

// pics with the width:height ratio below will cropped to landscape
define('STORIES_ORIENTATION_RATIO', 1.3);

// width/height on thumb ratios
define('STORIES_LANDSCAPE_THUMB_WIDTH', 340);
define('STORIES_LANDSCAPE_THUMB_HEIGHT', 100);

define('STORIES_PORTRAIT_THUMB_WIDTH', 180);
define('STORIES_PORTRAIT_THUMB_HEIGHT', 260);