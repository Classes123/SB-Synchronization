<?php

namespace PBY\SBSync\Cron;

class Synchronize
{
    public static function synchronize()
    {
        \XF::app()->repository('PBY\SBSync:Sync')->synchronizeUsers();
    }
}