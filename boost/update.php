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

use phpws2\Database;

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
            case $this->compare('1.1.4'):
                $this->update('1.1.4');
            case $this->compare('1.1.5'):
                $this->update('1.1.5');
            case $this->compare('1.2.0'):
                $this->update('1.2.0');
            case $this->compare('1.3.0'):
                $this->update('1.3.0');
            case $this->compare('1.3.1'):
                $this->update('1.3.1');
            case $this->compare('1.3.2'):
                $this->update('1.3.2');
            case $this->compare('1.3.3'):
                $this->update('1.3.3');
            case $this->compare('1.3.4'):
                $this->update('1.3.4');
            case $this->compare('1.4.0'):
                $this->update('1.4.0');
            case $this->compare('1.5.0'):
                $this->update('1.5.0');
            case $this->compare('1.5.1'):
                $this->update('1.5.1');
            case $this->compare('1.5.2'):
                $this->update('1.5.2');
            case $this->compare('1.5.3'):
                $this->update('1.5.3');
            case $this->compare('1.5.4'):
                $this->update('1.5.4');
            case $this->compare('1.5.5'):
                $this->update('1.5.5');
            case $this->compare('1.5.6'):
                $this->update('1.5.6');
            case $this->compare('1.5.7'):
                $this->update('1.5.7');
            case $this->compare('1.5.8'):
                $this->update('1.5.8');
            case $this->compare('1.6.0'):
                $this->update('1.6.0');
            case $this->compare('1.6.1'):
                $this->update('1.6.1');
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

    private function v1_1_4()
    {
        $changes[] = 'Fixed entry listing search not working with offsets.';

        $this->addContent('1.1.4', $changes);
    }

    private function v1_1_5()
    {
        $changes[] = 'Fixed tags javascript warning.';

        $this->addContent('1.1.5', $changes);
    }

    private function v1_2_0()
    {
        $db = Database::getDB();
        $author = $db->addTable('storiesauthor');
        $unique = new Database\Unique($author->getDataType('userId'));
        $unique->add();
        $changes[] = 'Author table user id made unique.';
        $changes[] = 'Added ability to add authors.';

        $this->addContent('1.2.0', $changes);
    }

    private function v1_3_0()
    {
        $db = Database::getDB();
        $entryTable = $db->addTable('storiesentry');
        if (!$entryTable->columnExists('imageOrientation')) {
            $dt = new \phpws2\Database\Datatype\Smallint($entryTable,
                    'imageOrientation');
            $dt->setDefault(0);
            $dt->add();
        }

        $authorTable = $db->addTable('storiesauthor');
        if (!$authorTable->columnExists('deleted')) {
            $dt = new \phpws2\Database\Datatype\Smallint($authorTable, 'deleted');
            $dt->setDefault(0);
            $dt->add();
        }

        if ($db->getDatabaseType() == 'mysql') {
            $dt2 = \phpws2\Database\Datatype::factory($authorTable, 'userId',
                            'int');
            $dt2->setUnsigned(false);
            $authorTable->alter($authorTable->getDataType('userId'), $dt2);
        }

        $changes[] = 'Added summary image positioning.';
        $changes[] = 'Stories more graceful if author is missing.';
        $changes[] = 'Authors may be disabled and deleted with user removal.';
        $changes[] = 'Unpublished warning in story list view.';
        $changes[] = 'Interface changes for more information';

        $this->addContent('1.3.0', $changes);
    }

    private function v1_3_1()
    {
        $changes[] = 'Fixed bad function call for story listing.';
        $changes[] = 'Stripping default caption text.';
        $this->addContent('1.3.1', $changes);
    }

    private function v1_3_2()
    {
        $changes[] = 'Removed foreign key constraint on users table.';
        $changes[] = 'Fixed Feature bugs due to View class inclusion.';
        $changes[] = 'Feature story selection ordered by publish date descending.';
        $changes[] = 'Hard limit on story retrieval added.';
        $changes[] = 'Added note to feature to explain why story may not appear.';
        $this->addContent('1.3.2', $changes);
    }

    private function v1_3_3()
    {
        $changes[] = 'Fixed production mode error.';
        $this->addContent('1.3.3', $changes);
    }

    private function v1_3_4()
    {
        $changes[] = 'Removed foreign key from install.';
        $this->addContent('1.3.4', $changes);
    }

    private function v1_4_0()
    {
        $changes[] = 'Saving occurs on mouse leaving form. Should help more frequent saving.';
        $changes[] = 'Rewrote embed code. No longer reliant on external service.';

        $this->addContent('1.4.0', $changes);
    }

    private function v1_5_0()
    {
        require_once PHPWS_SOURCE_DIR . 'mod/stories/boost/updates/v1_5_0.php';

        $update = new storiesUpdate_1_5_0;
        $changes = $update->run();
        $this->addContent('1.5.0', $changes);
    }

    private function v1_5_1()
    {
        $changes[] = 'Character limiter added to urlTitle variable.';
        $changes[] = 'Added id to non-friendly error message.';
        $changes[] = 'Fixed create date relative not working.';
        $changes[] = 'Fixed Feature stories.';
        $changes[] = 'Fixed share image css.';
        $changes[] = 'Removed PHP 7.2 type hinting.';

        $this->addContent('1.5.1', $changes);
    }

    private function v1_5_2()
    {
        $changes[] = 'Fixed embed bug.';
        $changes[] = 'Fixed Twitter embed.';

        $this->addContent('1.5.2', $changes);
    }

    private function v1_5_3()
    {
        $changes[] = 'Share pulls and displays leadImage instead of thumbnail.';
        $this->addContent('1.5.3', $changes);
    }

    private function v1_5_4()
    {
        require_once PHPWS_SOURCE_DIR . 'mod/stories/boost/updates/v1_5_4.php';
        $update = new storiesUpdate_1_5_4;
        $changes = $update->run();
        $this->addContent('1.5.4', $changes);
    }

    private function v1_5_5()
    {
        $changes[] = 'Fixed production build javascript.';
        $changes[] = 'Added tag deletion.';
        $this->addContent('1.5.5', $changes);
    }

    private function v1_5_6()
    {
        $changes[] = 'Updated Feature UI.';
        $changes[] = 'Repeat features prevented.';
        $changes[] = 'New date ui for publish date.';
        $changes[] = 'Permanent link uses story id.';
        $changes[] = 'Option to show full story starting at anchor.';
        $this->addContent('1.5.6', $changes);
    }

    private function v1_5_7()
    {
        $changes[] = 'Fixed install bug.';
        $changes[] = 'Fixed Feature Popover problem.';
        $this->addContent('1.5.7', $changes);
    }

    private function v1_5_8()
    {
        $changes[] = 'Added shortened url option for YouTube insertion.';
        $changes[] = 'Added checks to prevent empty title and content publication.';
        $changes[] = 'Removed babel-core';
        $changes[] = 'Fixed publish warning message.';
        $changes[] = 'Fixed bug with Settings page concerning tags.';
        $changes[] = 'Removed vendor package inclusion on view.';
        $changes[] = 'Darkened insert button.';
        $this->addContent('1.5.8', $changes);
    }

    private function v1_6_0()
    {
        $db = Database::getDB();
        $publishTable = $db->addTable('storiespublish');
        if (!$publishTable->columnExists('showInList')) {
            $dt = new \phpws2\Database\Datatype\Smallint($publishTable,
                    'showInList');
            $dt->setDefault(1);
            $dt->add();
        }
        $changes[] = 'Added way to remove stories from listing (i.e. feature only)';
        $changes[] = 'Fixed permanent link.';
        $this->addContent('1.6.0', $changes);
    }
    
    private function v1_6_1()
    {
        $changes[] = 'Fixed bugs with publish date.';
        $this->addContent('1.6.1', $changes);
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
