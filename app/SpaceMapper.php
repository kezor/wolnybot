<?php

namespace App;


class SpaceMapper
{

    private static $spaces = [
        1 => 'Farmland',
        2 => 'Hovel',
    ];

    public static function getSpaceNameByPid($pid)
    {
        if (!isset(self::$spaces[$pid])) {
            return 'Building with type  id "' . $pid . '" not supported yet.';
        }
        return self::$spaces[$pid];
    }

}