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
define('STORIES_FRIENDLY_ERROR', true);

define('STORIES_REACT_DEV', false);

define('STORIES_CONTENT_TAGS',
        'b,strong,em,i,p,img,iframe,h2,h3,h4,h5,a,style,blockquote,ul,ol,li,figure,figcaption,div,twitterwidget,twitter-widget,script,br');

define('STORIES_SUMMARY_TAGS', 'b,strong,em,i,a,style,ul,ol,li,p,br');

define('STORIES_DAY_THRESHOLD', 3);

define('STORIES_THUMB_TARGET_WIDTH', 600);
define('STORIES_THUMB_TARGET_HEIGHT', 600);

// Puts a hard limit on the EntryFactory::pullList method
define('STORIES_HARD_LIMIT', 100);

define('STORIES_SUMMARY_CHARACTER_LIMIT', 300);

define('STORIES_SENDMAIL', '/usr/sbin/sendmail -bs');

// If true, curl calls will disregard SSL warnings
define('STORIES_DISABLE_CURL_SSL', false);