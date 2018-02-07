<?php

/*
 * The MIT License
 *
 * Copyright 2018 Matthew McNaney <mcnaneym@appstate.edu>.
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

function stories_update(&$content, $currentVersion)
{
    $update = new StoriesUpdate($content, $currentVersion);
    $content = $update->run();
    return true;
}

class StoriesUpdate {
    private $content;
    private $cversion;
    
    public function __construct($content, $cversion)
    {
        $this->content = $content;
        $this->cversion = $cversion;
    }
    
    private function compare($version)
    {
        return version_compare($this->cversion, $version, '<');
    }
    
    public function run()
    {
        switch (1) {
            case $this->compare('1.0.1'):
                $this->update('1.0.1');
            case $this->compare('1.0.2'):
                $this->update('1.0.2');
            case $this->compare('1.0.3'):
                $this->update('1.0.3');
            case $this->compare('1.0.4'):
                $this->update('1.0.4');
        }
        return $this->content;
    }
    
    private function update($version) {
        $method = 'v' . str_replace('.', '_', $version);
        $this->$method();
    }
    
    private function v1_0_1()
    {
        $changes[] = 'Table names lowercased';
        $this->addContent('1.0.1', $changes);
    }

    private function v1_0_2()
    {
        $changes[] = 'Image styles fixed.';
        $this->addContent('1.0.2', $changes);
    }

    private function v1_0_3()
    {
        $changes[] = 'Fixed admin listing limit.';
        $changes[] = 'Unpublish removes features properly.';
        $changes[] = 'Unpublished status in list move obvious.';
        $changes[] = 'Changed initial edit action to mouse click.';
        $changes[] = 'Added indent and outdent buttons';
        $changes[] = 'Changed icons to FontAwesome';
        $changes[] = 'Fixed author not showing in admin list';
        $changes[] = 'Updated npm packages';
        $this->addContent('1.0.3', $changes);
    }
    
    private function v1_0_4()
    {
        $changes[] = 'Fixed bug with spaced tags.';
        $changes[] = 'Fixed bug in admin list when all stories deleted.';
        $this->addContent('1.0.4', $changes);
    }
    
    private function addContent($version, array $changes)
    {
        $changes_string = implode("\n+ ", $changes);
        $this->content[] = <<<EOF
<pre>
Version $version
------------------------------------------------------
+ $changes_string
</pre>
EOF;
    }
}
