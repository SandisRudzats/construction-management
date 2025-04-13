<?php

namespace api\modules\Employee\controllers\v1;

use api\modules\Employee\interfaces\EmployeeServiceInterface;
use api\modules\Employee\models\Employee;
use Yii;
use yii\base\Exception;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class EmployeeController extends ActiveController
{
    public $modelClass = Employee::class;

    public function __construct(
        $id,
        $module,
        private EmployeeServiceInterface $employeeService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actions(): array
    {
        $actions = parent::actions();

        // Disable the default 'create' action
        unset($actions['create']);

        return $actions;
    }

    /**
     * Create a new employee and assign a role.
     * @return Response
     * @throws ForbiddenHttpException if the user doesn't have permission
     * @throws BadRequestHttpException if the data is invalid
     * @throws Exception
     * @throws \Exception
     */
    public function actionCreate()
    {
        // todo:: pārnest šito uz employee servisu
        // Ensure the user is an admin

        if (!Yii::$app->user->can('manageEmployees')) {
            throw new ForbiddenHttpException("You don't have permission to create employees.");
        }
        $data = Yii::$app->request->post();

        if (empty($data['username']) || empty($data['password']) || empty($data['first_name']) || empty($data['last_name']) || empty($data['role'])) {
            throw new BadRequestHttpException("Missing required fields.");
        }

        // Create a new Employee instance
        $employee = new Employee();
        $employee->username = $data['username'];
        $employee->first_name = $data['first_name'];
        $employee->last_name = $data['last_name'];
        $employee->password_hash = $employee->setPassword($data['password']); // Use the setPassword method
        $employee->role = $data['role'];

        // Validate and save the employee
        if (!$employee->validate()) {
            return $this->asJson(['errors' => $employee->errors]);
        }

        if (!$employee->save()) {
            return $this->asJson(['errors' => 'Failed to save employee']);
        }

        // Assign the correct role from RBAC
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($data['role']);

        if ($role === null) {
            var_dump($data['role']);
            return $this->asJson(['errors' => 'Invalid role specified']);
        }

        $auth->assign($role, $employee->id);

        return $this->asJson([
            'message' => 'Employee created successfully',
            'employee' => $employee
        ]);
    }
}
