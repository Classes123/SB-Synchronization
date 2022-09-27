<?php

namespace PBY\SBSync;

use XF\Container;
use XF\Db\Mysqli\Adapter;
use XF\Mvc\Entity\Manager;

/*
 * Thx to https://hlmod.ru/members/inzanty.113086/
 */
class Listener
{
    public static function appSetup(\XF\App $app)
    {
        $container = $app->container();

        $container['pbySBSync.config'] = function (Container $c)
        {
            return $c['config']['sbintegration'] ?? [
                    'host' => '127.0.0.1',
                    'username' => '',
                    'password' => '',
                    'port' => 3306,
                    'dbname' => ''
                ];
        };
        $container['pbySBSync.db'] = function (Container $c)
        {
            $config = $c['config'];
            $connection = $c['pbySBSync.config'];
            return new Adapter($connection, $config['fullUnicode']);
        };
    }
}