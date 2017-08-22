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
class Settings
{
    /**
     * Comment code (e.g. Disqus) shown on story entry view.
     * @var string
     */
    protected $commentCode;
    
    /**
     * If true, stories will show entry feature blocks on front page.
     * @var boolean
     */
    protected $showFeatures;
    
    /**
     * Number of features to show.
     * @var integer
     */
    protected $featureNumber;
    
    /**
     *
     * @var boolean
     */
    protected $frontPageListing;
    
    /**
     *
     * @var integer
     */
    protected $listingLimit;

   
}
