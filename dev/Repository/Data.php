<?php

namespace PBY\SBSync\Repository;

use XF\Mvc\Entity\Repository;

class Data extends Repository
{
    protected ?array $sbServers = null;
    protected ?array $sbSrvGroups = null;

    public function getDatabase()
    {
        return $this->app()->container('pbySBSync.db');
    }

    public function getSbAdmin($comId)
    {
        $prefix = $this->options()->pbySBSync_prefix;
        $steamId = bcmod($comId, '2') .':'. bcdiv(bcsub($comId, '76561197960265728'), '2');

        /** @var \XF\Db\AbstractAdapter $db */
        $db = $this->getDatabase();
        $admin = $db->fetchRow('
            SELECT `t1`.`srv_group`, `t1`.`expired`,
            GROUP_CONCAT(
                `t2`.`server_id` ORDER BY `t2`.`server_id`
                ) AS `server_ids`
            FROM `'. $prefix .'admins` AS `t1`
            LEFT JOIN `'. $prefix .'admins_servers_groups` AS `t2`
                ON `t2`.`admin_id` = `t1`.`aid`
            WHERE `t1`.`authid` LIKE \'STEAM_%:'. $steamId .'\'
            GROUP BY `t1`.`aid`
        ');
        if ($admin) $admin['server_ids'] = explode(',', $admin['server_ids']);

        return $admin;
    }

    public function getSbServers()
    {
        if($this->sbServers === null)
        {
            $prefix = $this->options()->pbySBSync_prefix;

            /** @var \XF\Db\AbstractAdapter $db */
            $db = $this->getDatabase();
            $this->sbServers = $db->fetchAll('
                SELECT `sid`, `ip`, `port`
                FROM `'. $prefix .'servers`
            ');
        }

        return $this->sbServers;
    }

    public function getSbSrvGroups()
    {
        if($this->sbSrvGroups === null)
        {
            $prefix = $this->options()->pbySBSync_prefix;

            /** @var \XF\Db\AbstractAdapter $db */
            $db = $this->getDatabase();
            $this->sbSrvGroups = $db->fetchAll('
                SELECT `name`
                FROM `'. $prefix .'srvgroups`
            ');
        }

        return $this->sbSrvGroups;
    }
}