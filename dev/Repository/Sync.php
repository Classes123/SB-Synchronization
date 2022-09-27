<?php

namespace PBY\SBSync\Repository;

use XF\Mvc\Entity\Repository;

class Sync extends Repository
{
    const CHANGE_KEY = 'PBY_SBSync';

    public function synchronizeUsers()
    {
        $syncData = $this->finder('PBY\SBSync:Main')->fetch();

        $accountsFinder = $this->finder('XF:UserConnectedAccount');
        $accounts = $accountsFinder
            ->where('provider', 'steam')
            ->order('pby_sb_sync')
            ->with('User', true)
            ->fetch($this->options()->pbySBSync_usersPerSync);

        foreach ($accounts as $account)
        {
            $this->synchronizeUser($syncData, $account->User, $account);
        }
    }

    public function synchronizeUser($syncData, \XF\Entity\User $user, \XF\Entity\UserConnectedAccount $account)
    {
        $service = $this->app()->service('XF:User\UserGroupChange');
        $dataRepo = $this->repository('PBY\SBSync:Data');

        $admin = $dataRepo->getSbAdmin($account->provider_key);
        if ($admin && ($admin['expired'] == 0 || $admin['expired'] > \XF::$time))
        {
            $service->addUserGroupChange($account->user_id, self::CHANGE_KEY, $this->buildUserGroups($syncData, $admin));
        }
        else $service->removeUserGroupChange($user->user_id, self::CHANGE_KEY);

        $account->pby_sb_sync = \XF::$time;
        $account->save(false);
    }

    public function buildUserGroups($syncData, $adminData)
    {
        $result = [];
        foreach ($syncData as $entry)
        {
            if ($adminData['srv_group'] == $entry->srv_group &&
                (empty($entry->server_ids) || $adminData['server_ids'] == $entry->server_ids))
            {
                $result = array_merge($result, $entry->group_ids);
            }
        }
        return array_unique($result);
    }
}