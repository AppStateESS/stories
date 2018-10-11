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
        return $db->select();
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
        $guest->url = $request->pullPostString('url');
        $guest->email = $request->pullPostString('email');
        $guest->submitDate = time();
        return self::saveResource($guest);
    }

    public function acceptRequest($id)
    {
        $guest = $this->load($id);
        $guest->status = 1;
        $guest->acceptDate = time();
        $this->createAuthkey($guest);
        self::saveResource($guest);
        $this->emailAcceptance($guest);
    }

    private function emailAcceptance($guest)
    {
        $transport = \Swift_SendmailTransport::newInstance(STORIES_SENDMAIL);

        $subject = 'Sharing accepted for site: ' . $guest->siteName;
        $from = 'noreply@' . \Canopy\Server::getSiteUrl(false, false, false);
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

    private function createAuthKey(Resource $guest)
    {
        $guest->authkey = sha1(microtime());
    }

    public function denyGuest($id)
    {
        $guest = $this->load($id);
        $guest->status = 2;
        self::saveResource($guest);
    }

}
