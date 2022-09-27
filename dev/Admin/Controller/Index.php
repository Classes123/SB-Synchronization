<?php

namespace PBY\SBSync\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class Index extends \XF\Admin\Controller\AbstractController
{
    protected function preDispatchController($action, ParameterBag $params)
    {
        $this->assertAdminPermission('userGroup');
    }

    public function actionIndex()
    {
        $entriesFinder = $this->finder('PBY\SBSync:Main');

        $page = $this->filterPage();
        $perPage = 20;

        $entriesFinder->limitByPage($page, $perPage);

        $total = $entriesFinder->total();

        $viewParams = [
            'items' => $entriesFinder->fetch(),

            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
        return $this->view('PBY\SBSync:Index\Index', 'pby_sbsync_list', $viewParams);
    }

    public function actionAdd()
    {
        if($this->isPost())
        {
            /** @var \PBY\SBSync\Entity\Main $entry */
            $entry = $this->em()->create('PBY\SBSync:Main');

            $form = $this->entrySaveProcess($entry);
            $form->run();

            return $this->redirect($this->buildLink('sbsync'));
        }

        return $this->view('PBY\SBSync:Index\Edit', 'pby_sbsync_edit', $this->getViewParams());
    }

    public function actionEdit(ParameterBag $params)
    {
        /** @var \PBY\SBSync\Entity\Main $entry */
        $entry = $this->assertEntryExists($params->entry_id);

        if($this->isPost())
        {
            $form = $this->entrySaveProcess($entry);
            $form->run();

            return $this->redirect($this->buildLink('sbsync'));
        }

        return $this->view('PBY\SBSync:Index\Edit', 'pby_sbsync_edit', $this->getViewParams([
            'entry' => $entry
        ]));
    }

    public function actionDelete(ParameterBag $params)
    {
        $entry = $this->assertEntryExists($params->entry_id);

        $plugin = $this->plugin('XF:Delete');
        return $plugin->actionDelete(
            $entry,
            $this->buildLink('sbsync/delete', $entry),
            $this->buildLink('sbsync/edit', $entry),
            $this->buildLink('sbsync'),
            $entry->srv_group
        );
    }

    protected function entrySaveProcess(\PBY\SBSync\Entity\Main $entry)
    {
        $entityInput = $this->filter([
            'srv_group' => 'str',
            'group_ids' => 'array-uint',
            'server_ids' => 'array-uint'
        ]);

        $form = $this->formAction();
        $form->basicEntitySave($entry, $entityInput);

        return $form;
    }

    protected function assertEntryExists($entry_id)
    {
        return $this->assertRecordExists('PBY\SBSync:Main', $entry_id);
    }

    protected function getViewParams($extraFields = [])
    {
        $dataRepo = $this->repository('PBY\SBSync:Data');

        $viewParams = [
            'groups' => $this->repository('XF:UserGroup')->findUserGroupsForList()->fetch(),
            'servers' => $dataRepo->getSbServers(),
            'srv_groups' => $dataRepo->getSbSrvGroups()
        ];
        return array_merge($viewParams, $extraFields);
    }
}