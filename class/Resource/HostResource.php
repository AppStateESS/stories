<?php

/**
 * MIT License
 * Copyright (c) 2019 Electronic Student Services @ Appalachian State University
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
     * Name of site receiving stories
     * @var phpws2\Variable\TextOnly
     */
     protected $siteName;
     
     /**
      * Address of site
      * @var phpws2\Variable\Url
      */
     protected $url;
     
     /**
      * Host authorization key sent to site to verify stories
      * @var phpws2\Variable\HashVar
      */
     protected $authkey;
     
     protected $table = 'storieshost';
     
     public function __construct()
     {
         parent::__construct();
         $this->siteName = new \phpws2\Variable\TextOnly(null, 'siteName');
         $this->siteName->setLimit(100);
         //$this->url = new \phpws2\Variable\Url(null, 'url');
         $this->url = new \phpws2\Variable\StringVar(null, 'url');
         $this->url->setLimit(200);
         $this->authkey = new \phpws2\Variable\HashVar(null, 'authkey');
         $this->authkey->setLimit(40);
     }
     
     public function setUrl($url) {
         if (!preg_match('@/$@', $url)) {
             $url = $url . '/';
         }
         if (!preg_match('@^https?:@', $url)) {
             if (!preg_match('@^//@', $url)) {
                 $url = 'http://' . $url;
             } else {
                 $url = 'http:' . $url;
             }
         }
         $this->url->set($url);
     }
             
}
