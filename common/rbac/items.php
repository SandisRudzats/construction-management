<?php

return [
    'manageEmployees' => [
        'type' => 2,
        'description' => 'Create, update, deactivate employees',
    ],
    'viewTeam' => [
        'type' => 2,
        'description' => 'View subordinate employees',
    ],
    'viewOwnProfile' => [
        'type' => 2,
        'description' => 'View own employee profile',
    ],
    'manageSites' => [
        'type' => 2,
        'description' => 'Create, update, delete construction sites',
    ],
    'viewAssignedSites' => [
        'type' => 2,
        'description' => 'View sites where employee has tasks',
    ],
    'manageAllTasks' => [
        'type' => 2,
        'description' => 'Admin can manage all tasks',
    ],
    'manageOwnTasks' => [
        'type' => 2,
        'description' => 'Manager can manage tasks in his sites',
    ],
    'viewOwnTasks' => [
        'type' => 2,
        'description' => 'Employee can view their own tasks',
    ],
    'employee' => [
        'type' => 1,
        'children' => [
            'viewOwnProfile',
            'viewOwnTasks',
            'viewAssignedSites',
        ],
    ],
    'manager' => [
        'type' => 1,
        'children' => [
            'employee',
            'viewTeam',
            'manageOwnTasks',
        ],
    ],
    'admin' => [
        'type' => 1,
        'children' => [
            'manager',
            'manageEmployees',
            'manageSites',
            'manageAllTasks',
        ],
    ],
];
