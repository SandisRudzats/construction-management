<?php

declare(strict_types=1);

namespace console\commands;

use Exception;
use Yii;
use yii\console\Controller;
use yii\rbac\PhpManager;

class RbacController extends Controller
{
    /**
     * @throws \yii\base\Exception
     * @throws Exception
     */
    public function actionInit(): void
    {
        /** @var PhpManager $auth */
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        // Permissions
        $manageEmployees = $auth->createPermission('manageEmployees');
        $manageEmployees->description = 'Create, update, deactivate employees';
        $auth->add($manageEmployees);

        $viewTeam = $auth->createPermission('viewTeam');
        $viewTeam->description = 'View subordinate employees';
        $auth->add($viewTeam);

        $viewOwnProfile = $auth->createPermission('viewOwnProfile');
        $viewOwnProfile->description = 'View own employee profile';
        $auth->add($viewOwnProfile);

        $manageSites = $auth->createPermission('manageSites');
        $manageSites->description = 'Create, update, delete construction sites';
        $auth->add($manageSites);

        $viewAssignedSites = $auth->createPermission('viewAssignedSites');
        $viewAssignedSites->description = 'View sites where employee has tasks';
        $auth->add($viewAssignedSites);

        $manageAllTasks = $auth->createPermission('manageAllTasks');
        $manageAllTasks->description = 'Admin can manage all tasks';
        $auth->add($manageAllTasks);

        $manageOwnTasks = $auth->createPermission('manageOwnTasks');
        $manageOwnTasks->description = 'Manager can manage tasks in his sites';
        $auth->add($manageOwnTasks);

        $viewOwnTasks = $auth->createPermission('viewOwnTasks');
        $viewOwnTasks->description = 'Employee can view their own tasks';
        $auth->add($viewOwnTasks);

        // Roles
        $employee = $auth->createRole('employee');
        $auth->add($employee);
        $auth->addChild($employee, $viewOwnProfile);
        $auth->addChild($employee, $viewOwnTasks);
        $auth->addChild($employee, $viewAssignedSites);

        $manager = $auth->createRole('manager');
        $auth->add($manager);
        $auth->addChild($manager, $employee);
        $auth->addChild($manager, $viewTeam);
        $auth->addChild($manager, $manageOwnTasks);

        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $manager);
        $auth->addChild($admin, $manageEmployees);
        $auth->addChild($admin, $manageSites);
        $auth->addChild($admin, $manageAllTasks);

        echo "RBAC roles and permissions initialized.\n";
    }
}