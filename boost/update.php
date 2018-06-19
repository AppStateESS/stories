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

class StoriesUpdate
{

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
            case $this->compare('1.0.8'):
                $this->content[] = 'Update to latest version in phpWebsite';
                return $this->content;
            case $this->compare('1.1.0'):
                $this->update('1.1.0');
            case $this->compare('1.1.1'):
                $this->update('1.1.1');
            case $this->compare('1.1.2'):
                $this->update('1.1.2');
            case $this->compare('1.1.3'):
                $this->update('1.1.3');
        }
        return $this->content;
    }

    private function update($version)
    {
        $method = 'v' . str_replace('.', '_', $version);
        $this->$method();
    }

    private function v1_1_0()
    {
        $changes[] = 'Updated to Bootstrap 4';
        $changes[] = 'Added zoom thumbnail ability for features';
        $changes[] = 'Added reset button recenter';
        $changes[] = 'Display fixes';
        $changes[] = 'Updated npm libraries';
        $this->addContent('1.1.0', $changes);
    }

    private function v1_1_1()
    {
        $db = \phpws2\Database::getDB();
        $entryToFeature = $db->addTable('storiesentrytofeature');
        $dt = $entryToFeature->addDataType('zoom', 'smallint');
        $dt->setDefault(100);
        $dt->add();
        $changes[] = 'Adding missing zoom column';
        $changes[] = 'Several fixes that caused 1.1.0 to be incompatible';
        $changes[] = 'All scripts processed in footer now as is standard.';
        $changes[] = 'Navbar collapses at smaller width than before.';
        $changes[] = 'Fixed jquery conflicts.';
        $changes[] = 'New UI ease-of-use changes.';
        $this->addContent('1.1.1', $changes);
    }
    
    private function v1_1_2()
    {
        $changes[] = 'Fixed publish problems';
        $changes[] = 'Reconfigured webpack and made packages more compact.';
        $this->addContent('1.1.2', $changes);
    }

    private function v1_1_3()
    {
        $changes[] = 'Added option to hide side panel on view.';
        $changes[] = 'Interface fixes.';
        $changes[] = 'Fixed Font Awesome icon on editor insertion.';
        $this->addContent('1.1.3', $changes);
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
