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

namespace stories\Resource;

use \phpws2\Database;
use stories\Exception\MissingInput;

class BaseResource extends \phpws2\Resource
{

    public function __set($name, $value)
    {
        if ((!$this->$name->allowNull() &&
                (method_exists($this->$name, 'allowEmpty') && !$this->$name->allowEmpty())) &&
                ( (is_string($value) && $value === '') || is_null($value))) {
            throw new MissingInput("$name may not be empty");
        }

        $method_name = self::walkingCase($name, 'set');
        if (method_exists($this, $method_name)) {
            return $this->$method_name($value);
        } else {
            return $this->$name->set($value);
        }
    }

    public function __get($name)
    {
        $method_name = self::walkingCase($name, 'get');
        if (method_exists($this, $method_name)) {
            return $this->$method_name();
        } else {
            return $this->$name->get();
        }
    }

    public function __isset($name)
    {
        return !($this->$name->isNull());
    }

    public function isEmpty($name)
    {
        return $this->$name->isEmpty();
    }

    public function relativeTime($date)
    {
        if (empty($date)) {
            throw new \Exception('relativeTime will not function on an empty date');
        }
        $timepassed = time() - mktime(0, 0, 0, strftime('%m', $date),
                        strftime('%d', $date), strftime('%Y', $date));

        $rawday = ($timepassed / 86400);
        $days = floor($rawday);

        switch ($days) {
            case 0:
                return 'today at ' . strftime('%l:%M%P', $date);

            case 1:
                return 'yesterday at ' . strftime('%l:%M%P', $date);

            case -1:
                return 'tomorrow at ' . strftime('%l:%M%P', $date);

            case ($days > 0 && $days < STORIES_DAY_THRESHOLD):
                return "$days days ago";

            case ($days < 0 && abs($days) < STORIES_DAY_THRESHOLD):
                return 'in ' . abs($days) . ' days';

            default:
                if (strftime('%Y', $date) != strftime('%Y')) {
                    return strftime('%b %e, %g', $date);
                } else {
                    return strftime('%b %e', $date);
                }
        }
    }

}
