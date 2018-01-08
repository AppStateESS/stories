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

/**
 * Description of Feature
 *
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 */
class FeatureResource extends BaseResource
{
    protected $title;
    protected $active;
    protected $entries;
    protected $format;
    protected $columns;
    protected $sorting;
    protected $stories;
    
    protected $table = 'storiesfeature';
    
    public function __construct()
    {
        parent::__construct();
        $this->title = new \phpws2\Variable\TextOnly(null, 'title');
        $this->title->setLimit(255);
        $this->title->allowNull(true);
        $this->format = new \phpws2\Variable\Attribute('landscape', 'format');
        $this->format->setLimit(20);
        $this->active = new \phpws2\Variable\BooleanVar(true, 'active');
        $this->columns = new \phpws2\Variable\SmallInteger(2, 'columns');
        $this->sorting = new \phpws2\Variable\SmallInteger(0, 'sorting');
        $this->stories = new \phpws2\Variable\ArrayVar(null, 'stories');
        $this->entries = new \phpws2\Variable\ArrayVar(null, 'entries');
        $this->entries->setIsTableColumn(false);
        $this->stories->setIsTableColumn(false);
    }
}
