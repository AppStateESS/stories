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

    /**
     * @var \phpws2\Variable\TextOnly
     */
    protected $title;

    /**
     * @var \phpws2\Variable\BooleanVar
     */
    protected $active;

    /**
     * @var \phpws2\Variable\Attribute
     */
    protected $format;

    /**
     * @var \phpws2\Variable\SmallInteger
     */
    protected $sorting;

    /**
     * @var string
     */
    protected $table = 'storiesfeature';

    public function __construct()
    {
        parent::__construct();
        $this->title = new \phpws2\Variable\TextOnly(null, 'title');
        $this->title->setLimit(100);
        $this->title->allowNull(true);
        $this->format = new \phpws2\Variable\Attribute('landscape', 'format');
        $this->format->setLimit(20);
        $this->active = new \phpws2\Variable\BooleanVar(true, 'active');
        $this->sorting = new \phpws2\Variable\SmallInteger(0, 'sorting');
    }

}
