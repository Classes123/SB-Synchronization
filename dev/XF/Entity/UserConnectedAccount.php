<?php

namespace PBY\SBSync\XF\Entity;

use XF\Mvc\Entity\Structure;

class UserConnectedAccount extends XFCP_UserConnectedAccount
{
    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        $structure->columns += [
            'pby_sb_sync' => ['type' => self::UINT, 'default' => 0]
        ];

        return $structure;
    }

    protected function _postSave()
    {
        if ($this->isInsert() && $this->provider == 'steam')
        {
            \XF::runLater( function () {
                $syncData = $this->finder('PBY\SBSync:Main')->fetch();

                $syncRepo = $this->repository('PBY\SBSync:Sync');
                $syncRepo->synchronizeUser($syncData, $this->User, $this);
            });
        }

        return parent::_postSave();
    }

    protected function _preDelete()
    {
        $parent = parent::_preDelete();

        if ($this->provider == 'steam')
        {
            $user = $this->User;
            if($user !== null)
            {
                $service = $this->app()->service('XF:User\UserGroupChange');
                $service->removeUserGroupChange($user->user_id, \PBY\SBSync\Repository\Sync::CHANGE_KEY);
            }
        }

        return $parent;
    }
}