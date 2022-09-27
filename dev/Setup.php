<?php

namespace PBY\SBSync;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;

use XF\Db\Schema\Alter;

class Setup extends AbstractSetup
{
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;

    public function install(array $stepParams = [])
    {
        $schemaManager = $this->schemaManager();

        $schemaManager->createTable('xf_pby_sbsync', function(\XF\Db\Schema\Create $table)
        {
            $table->addColumn('entry_id', 'int')->autoIncrement();

            $table->addColumn('group_ids', 'blob');
            $table->addColumn('server_ids', 'blob');
            $table->addColumn('srv_group', 'varchar', 128);

            $table->addPrimaryKey('entry_id');
        });
        $schemaManager->alterTable('xf_user_connected_account', function(Alter $table)
        {
            $table->addColumn('pby_sb_sync', 'int', 10)->setDefault(0);
        });
    }

    public function upgrade1000170Step1()
    {
        $db = $this->db();

        $db->query('
            CREATE TABLE `xf_pby_sbsync` LIKE `pby_sbsync`
        ');
        $db->query('
            INSERT `xf_pby_sbsync` SELECT * FROM `pby_sbsync`
        ');

        $this->schemaManager()->dropTable('pby_sbsync');
    }

    public function uninstallStep1()
    {
        $key = \PBY\SBSync\Repository\Sync::CHANGE_KEY;
        $userIds = $this->db()->fetchAllColumn('
            SELECT `user_id`
            FROM `xf_user_group_change`
            WHERE `change_key` = ?
        ', [$key]);

        if($userIds)
        {
            $service = $this->app()->service('XF:User\UserGroupChange');
            foreach($userIds as $userId)
            {
                $service->removeUserGroupChange($userId, $key);
            }
        }
    }

    public function uninstallStep2()
    {
        $schemaManager = $this->schemaManager();

        $schemaManager->dropTable('xf_pby_sbsync');
        $schemaManager->alterTable('xf_user_connected_account', function(Alter $table)
        {
            $table->dropColumns(['pby_sb_sync']);
        });
    }
}