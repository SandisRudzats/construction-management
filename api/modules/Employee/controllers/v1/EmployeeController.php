<?php

namespace api\modules\Employee\controllers\v1;

use api\interfaces\EmployeeServiceInterface;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

class EmployeeController extends Controller
{
    public function __construct(
        $id,
        $module,
        private EmployeeServiceInterface $employeeService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        return $this->employeeService->getAllEmployees();
    }

    public function actionView($id)
    {
        $employee = $this->employeeService->getEmployeeById($id);
        if ($employee === null) {
            throw new NotFoundHttpException('Employee not found.');
        }
        return $employee;
    }

    public function actionCreate()
    {
        $employeeData = \Yii::$app->request->post();
        return $this->employeeService->createEmployee($employeeData);
    }

    public function actionUpdate($id)
    {
        $employeeData = \Yii::$app->request->post();
        $employee = $this->employeeService->updateEmployee($id, $employeeData);
        if ($employee === null) {
            throw new NotFoundHttpException('Employee not found.');
        }
        return $employee;
    }

    public function actionDelete($id)
    {
        if (!$this->employeeService->deleteEmployee($id)) {
            throw new NotFoundHttpException('Employee not found.');
        }
        \Yii::$app->response->statusCode = 204; // No Content
    }
}