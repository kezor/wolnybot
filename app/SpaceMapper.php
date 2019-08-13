<?php

namespace App;


class SpaceMapper
{

    private static $spaces = [
        Space::TYPE_FARMLAND => 'Farmland',
        Space::TYPE_HOVEL => 'Hovel',
    ];

    public static function getSpaceNameByPid($pid)
    {
        if (!isset(self::$spaces[$pid])) {
            return 'Building with type  id "' . $pid . '" not supported yet.';
        }
        return self::$spaces[$pid];
    }

}