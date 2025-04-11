<?php

return [
    'admin' => [
        'type' => 1,
        'description' => 'Administrator',
        'children' => [
            'manageEmployees',
            'manageConstructionSites',
            'manageWorkTasks',
        ],
    ],
    'manager' => [
        'type' => 1,
        'description' => 'Manager',
        'children' => [
            'viewSubordinateEmployees',
            'manageConstructionSitesInScope',
            'manageWorkTasksInScope',
            'viewAssignedConstructionSites',
        ],
    ],
    'employee' => [
        'type' => 1,
        'description' => 'Employee',
        'children' => [
            'viewOwnInformation',
            'viewAssignedTasks',
            'viewAssignedConstructionSites',
        ],
    ],
    'manageEmployees' => [
        'type' => 2,
        'description' => 'Manage Employees (add, edit, deactivate)',
    ],
    'viewSubordinateEmployees' => [
        'type' => 2,
        'description' => 'View Subordinate Employees',
    ],
    'manageConstructionSites' => [
        'type' => 2,
        'description' => 'Manage Construction Sites (add, edit, delete)',
    ],
    'manageConstructionSitesInScope' => [
        'type' => 2,
        'description' => 'Manage Construction Sites in Manager\'s Scope',
    ],
    'manageWorkTasks' => [
        'type' => 2,
        'description' => 'Manage Work Tasks (add, edit, delete)',
    ],
    'manageWorkTasksInScope' => [
        'type' => 2,
        'description' => 'Manage Work Tasks in Manager\'s Scope',
    ],
    'viewAssignedTasks' => [
        'type' => 2,
        'description' => 'View Assigned Work Tasks',
    ],
    'viewAssignedConstructionSites' => [
        'type' => 2,
        'description' => 'View Assigned Construction Sites',
    ],
    'viewOwnInformation' => [
        'type' => 2,
        'description' => 'View Own Information',
    ],
];