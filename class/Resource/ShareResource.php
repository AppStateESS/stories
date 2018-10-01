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

namespace stories\Resource;

class ShareResource extends BaseResource
{
    /**
     * GuestResource id of site sharing the story
     * @var phpws2\Variable\IntegerVar
     */
    protected $guestId;
    
    /**
     * Id of story on sending site
     * @var phpws2\Variable\IntegerVar
     */
    protected $storyId;
    
    /**
     * Address of source story
     * @var phpws2\Variable\Url
     */
    protected $url;
    
}
