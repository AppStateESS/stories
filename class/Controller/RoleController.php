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

namespace stories\Controller;

use stories\Exception\BadCommand;
use stories\Exception\PrivilegeMissing;
use stories\Factory\StoryMenu;
use phpws2\Database;
use Canopy\Request;

abstract class RoleController
{

    protected $factory;
    protected $view;
    protected $role;
    protected $id;

    abstract protected function loadFactory();
    abstract protected function loadView();

    public function __construct($role)
    {
        $this->role = $role;
        $this->loadFactory();
        $this->loadView();
    }

    /**
     * Returns the current get command
     * Defaults to a "view" command if an id is set and
     * "list" if otherwise.
     * @param Request $request
     * @return string
     */
    protected function pullGetCommand(Request $request)
    {
        $command = $request->shiftCommand();
        if (is_numeric($command)) {
            $this->id = $command;

            $subcommand = $request->shiftCommand();
            if (empty($subcommand)) {
                $command = 'view';
            } else {
                return $subcommand;
            }
        } else if (empty($command)) {
            $command = 'list';
        }
        return $command;
    }

    /**
     * Loads the EXPECTED id from the url into the object.
     * If the id is not there, the command fails
     */
    protected function loadRequestId(Request $request)
    {
        $id = $request->shiftCommand();
        if (!is_numeric($id)) {
            throw new \stories\Exception\ResourceNotFound($id);
        }
        $this->id = $id;
    }

    public function getHtml(Request $request)
    {
        $command = $this->pullGetCommand($request);

        $method_name = $command . 'HtmlCommand';
        if (!method_exists($this, $method_name)) {
            $entryFactory = new \stories\Factory\EntryFactory;
            $entry = $entryFactory->getByUrlTitle($command);
            if (!empty($entry)) {
                $this->id = $entry->id;
                $content = $this->viewHtmlCommand($request);
                return $this->htmlResponse($content);
            }
            /**
             * Although view will be returned by pullGetCommand if the command
             * is empty, we force a view for any other unrecognized command.
             */
            if ($this->id && method_exists($this, 'viewHtmlCommand')) {
                $method_name = 'viewHtmlCommand';
            } else {
                throw new BadCommand($method_name);
            }
        }

        $content = $this->$method_name($request);
        return $this->htmlResponse($content);
    }

    public function getJson(Request $request)
    {
        $command = $this->pullGetCommand($request);

        if (empty($command)) {
            throw new BadCommand;
        }

        $method_name = $command . 'JsonCommand';

        /**
         * Unlike getHtml, a bad command is not excused
         */
        if (!method_exists($this, $method_name)) {
            throw new BadCommand($method_name);
        }

        $json = $this->$method_name($request);
        return $this->jsonResponse($json);
    }

    public function htmlResponse($content)
    {
        $view = new \phpws2\View\HtmlView($content);
        $response = new \Canopy\Response($view);
        return $response;
    }

    public function jsonResponse($json)
    {
        $view = new \phpws2\View\JsonView($json);
        $response = new \Canopy\Response($view);
        return $response;
    }

    /**
     * For delete, post, patch, and put commands
     * @param Request $request
     */
    public function changeResponse(Request $request)
    {
        $method = strtolower($request->getMethod());
        if ($method !== 'post') {
            $this->loadRequestId($request);
        }

        $getCommand = $request->shiftCommand();

        if (empty($getCommand)) {
            $restCommand = $method . 'Command';
        } else {
            $restCommand = $getCommand . ucfirst($method) . 'Command';
        }

        if (!method_exists($this, $restCommand)) {
            $errorMessage = get_class($this) . ':' . $restCommand;
            throw new BadCommand($errorMessage);
        }

        $content = $this->$restCommand($request);

        if ($request->isAjax()) {
            return $this->jsonResponse($content);
        } else {
            return $this->htmlResponse($content);
        }
    }

    public function getResponse($content, Request $request)
    {
        return $request->isAjax() ? $this->jsonResponse($content) : $this->htmlResponse($content);
    }

    protected function createHtmlCommand(Request $request)
    {
        throw new PrivilegeMissing;
    }

    protected function editHtmlCommand(Request $request)
    {
        throw new PrivilegeMissing;
    }

    protected function createPostCommand(Request $request)
    {
        throw new PrivilegeMissing(__FUNCTION__);
    }

    protected function putCommand(Request $request)
    {
        throw new PrivilegeMissing(__FUNCTION__);
    }

    protected function patchCommand(Request $request)
    {
        throw new PrivilegeMissing(__FUNCTION__);
    }

    protected function deleteCommand(Request $request)
    {
        throw new PrivilegeMissing(__FUNCTION__);
    }

}
