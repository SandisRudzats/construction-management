<?php

namespace api\modules\Employee\controllers\v1;

use api\interfaces\EmployeeServiceInterface;
use api\modules\Employee\models\Employee;
use Yii;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

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

    public function actionSandis()
    {
        dd('sandis here');
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionFindByUsername($username)
    {
        $employee = $this->employeeService->findEmployeeByUsername($username);
        if ($employee === null) {
            throw new NotFoundHttpException('Employee not found.');
        }
        return $employee;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function findModel($id): Employee
    {
        Yii::info("findModel called with id: $id", 'debug'); // Add this line
        $model = Employee::findOne($id);
        if ($model === null) {
            Yii::info("Employee not found with id: $id", 'debug'); // Add this line
            throw new NotFoundHttpException('Employee not found.');
        }
        Yii::info("Employee found with id: $id", 'debug'); // Add this line
        return $model;
    }
}