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

class HostResource extends BaseResource
{
    /**
     * Name of site sending stories
     * @var phpws2\Variable\TextOnly
     */
     protected $siteName;
     
     /**
      * Address of sending site
      * @var phpws2\Variable\Url
      */
     protected $url;
     
     /**
      * Key sent to site to verify their stories
      * @var phpws2\Variable\HashVar
      */
     protected $receiveKey;
     
     /**
      * @var phpws2\Variable\TextOnly
      */
     protected $contactName;
     
     /**
      * @var phpws2\Variable\Email
      */
     protected $contactEmail;
     
}
