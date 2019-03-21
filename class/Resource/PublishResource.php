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

class PublishResource extends BaseResource
{

    /**
     * @var \phpws2\Variable\IntegerVar
     */
    protected $entryId;

    /**
     * @var \phpws2\Variable\IntegerVar
     */
    protected $shareId;

    /**
     * @var \phpws2\Variable\DateTime
     */
    protected $publishDate;
    
    /**
     * @var \phpws2\Variable\BooleanVar
     */
    protected $showInList;
    
    /**
     * @var string
     */
    protected $table = 'storiespublish';

    public function __construct()
    {
        parent::__construct();
        $this->entryId = new \phpws2\Variable\IntegerVar(0, 'entryId');
        $this->shareId = new \phpws2\Variable\IntegerVar(0, 'shareId');
        $this->publishDate = new \phpws2\Variable\DateTime(0, 'publishDate');
        $this->showInList = new \phpws2\Variable\BooleanVar(1, 'showInList');
    }

    public function stamp()
    {
        $this->publishDate->stamp();
    }

}
