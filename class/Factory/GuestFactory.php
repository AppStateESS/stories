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

namespace stories\Factory;

use stories\Resource\GuestResource as Resource;
use phpws2\Database;
use Canopy\Request;
use phpws2\Variable;
use phpws2\Template;
use stories\Factory\ShareFactory;
use Canopy\Server;

if (!defined('STORIES_SENDMAIL')) {
    define('STORIES_SENDMAIL', '/usr/sbin/sendmail -bs');
}

class GuestFactory extends BaseFactory
{

    /**
     * @param array $data
     * @return stories\Resource\GuestResource
     */
    public function build($data = null)
    {
        $resource = new Resource;
        if ($data) {
            $resource->setVars($data);
        }
        return $resource;
    }

    public function getCurrentGuests()
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesguest');
        $tbl->addFieldConditional('status', 1);
        $tbl->addOrderBy('siteName');
        $rows = $db->select();
        if (empty($rows)) {
            return [];
        }
        $shareFactory = new ShareFactory;
        foreach ($rows as $row) {
            $row['storyCount'] = $shareFactory->guestShareCount($row['id']);
            $newRows[] = $row;
        }
        return $newRows;
    }

    public function getGuestRequests()
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesguest');
        $tbl->addFieldConditional('status', 0);
        $tbl->addOrderBy('siteName');
        return $db->select();
    }

    public function newGuestRequest(Request $request)
    {
        $guest = $this->build();
        $guest->siteName = $request->pullPostString('siteName');
        $url = $request->pullPostString('url');
        if (!preg_match('@^(https?:)?//@', $url)) {
            $url = 'http://' . $url;
        } elseif (!preg_match('@^https?:@', $url)) {
            $url = 'http:' . $url;
        }
        $guest->url = $url;
        $guest->email = $request->pullPostString('email');
        $guest->submitDate = time();
        $this->createAuthkey($guest);
        return self::saveResource($guest);
    }

    public function acceptRequest($id)
    {
        $guest = $this->load($id);
        $guest->status = 1;
        $guest->acceptDate = time();
        //$this->createAuthkey($guest);
        self::saveResource($guest);
        $this->emailAcceptance($guest);
    }

    private function emailAcceptance($guest)
    {
        $transport = \Swift_SendmailTransport::newInstance(STORIES_SENDMAIL);
        $subject = 'Sharing accepted for site: ' . $guest->siteName;
        $from = 'noreply@' . \Canopy\Server::getSiteUrl(false, false, false);
        $vars['hostName'] = \Layout::getPageTitle(true);
        $vars['hostUrl'] = Server::getSiteUrl();
        $vars['authkey'] = $guest->authkey;
        $vars['siteName'] = $guest->siteName;
        $vars['url'] = $guest->url;
        $template = new Template($vars);
        $template->setModuleTemplate('stories', 'Email/Acceptance.html');
        $content = $template->get();

        $message = \Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setFrom($from);
        $message->setTo($guest->email);
        $message->setBody($content, 'text/html');
        $mailer = new \Swift_Mailer($transport);
        $mailer->send($message);
    }

    public function emailRemoval($guest)
    {
        $transport = \Swift_SendmailTransport::newInstance(STORIES_SENDMAIL);

        $subject = 'Removed ' . $guest->siteName;
        $from = 'noreply@' . \Canopy\Server::getSiteUrl(false, false, false);
        $vars['siteName'] = $guest->siteName;
        $vars['url'] = $guest->url;
        $vars['hostName'] = \Layout::getPageTitle(true);
        $vars['hostUrl'] = Server::getSiteUrl();
        $template = new Template($vars);
        $template->setModuleTemplate('stories', 'Email/Removal.html');
        $content = $template->get();

        $message = \Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setFrom($from);
        $message->setTo($guest->email);
        $message->setBody($content, 'text/html');
        $mailer = new \Swift_Mailer($transport);
        $mailer->send($message);
    }

    private function emailDenial($guest)
    {
        $transport = \Swift_SendmailTransport::newInstance(STORIES_SENDMAIL);

        $subject = 'Sharing denied for site: ' . $guest->siteName;
        $from = 'noreply@' . \Canopy\Server::getSiteUrl(false, false, false);
        $vars['siteName'] = $guest->siteName;
        $vars['url'] = $guest->url;
        $vars['hostName'] = \Layout::getPageTitle(true);
        $vars['hostUrl'] = Server::getSiteUrl();
        $template = new Template($vars);
        $template->setModuleTemplate('stories', 'Email/Denied.html');
        $content = $template->get();

        $message = \Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setFrom($from);
        $message->setTo($guest->email);
        $message->setBody($content, 'text/html');
        $mailer = new \Swift_Mailer($transport);
        $mailer->send($message);
    }

    private function createAuthKey(Resource $guest)
    {
        $guest->authkey = sha1(microtime());
    }

    public function denyRequest($id)
    {
        $guest = $this->load($id);
        $this->emailDenial($guest);
        self::deleteResource($guest);
    }

    /**
     * Deletes a guest and all their shares.
     * @param int $id
     */
    public function delete(int $id)
    {
        $shareFactory = new ShareFactory;
        $shareFactory->deleteByGuestId($id);

        $db = Database::getDB();
        $tbl = $db->addTable('storiesguest');
        $tbl->addFieldConditional('id', $id);
        $db->delete();
    }

    public function getByAuthkey($authkey)
    {
        $db = Database::getDB();
        $tbl = $db->addTable('storiesguest');
        $tbl->addFieldConditional('authkey', $authkey);
        $row = $db->selectOneRow();
        if (empty($row)) {
            return null;
        } else {
            return $this->build($row);
        }
    }

    public function requestShare(Request $request)
    {
        try {
            $this->newGuestRequest($request);
            Server::forward('./stories/Guest/requestAccepted');
        } catch (\Exception $ex) {
            if (\Current_User::isDeity()) {
               exit($ex->getMessage());
            } else {
                Server::forward('./stories/Guest/requestError');
            }
        }
    }

    public function unsubscribeByAuthkey($authkey)
    {
        $guest = $this->getByAuthkey($authkey);
        if (empty($guest)) {
            throw new \Exception('Could not find guest by authkey');
        }
        $this->delete($guest->id);
    }

}
