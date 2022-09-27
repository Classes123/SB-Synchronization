<?php

namespace PBY\SBSync\Entity;

use XF\Mvc\Entity\Structure;

class Main extends \XF\Mvc\Entity\Entity
{
    public static function getStructure(Structure $structure)
    {
        $structure->shortName = 'PBY\SBSync:Main';
        $structure->table = "xf_pby_sbsync";
        $structure->primaryKey = 'entry_id';
        $structure->columns = [
            'entry_id' => ['type' => self::UINT, 'autoIncrement' => true],
            'group_ids' => ['type' => self::JSON_ARRAY, 'required' => 'pby_sbsync_select_groups'],
            'server_ids' => ['type' => self::JSON_ARRAY, 'default' => []],
            'srv_group' => ['type' => self::STR, 'maxLength' => 128, 'required' => true]
        ];

        return $structure;
    }
}