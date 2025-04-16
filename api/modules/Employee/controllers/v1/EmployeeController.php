<?php

declare(strict_types=1);

namespace api\modules\Employee\controllers\v1;

use api\helpers\RbacValidationHelper;
use api\modules\Employee\interfaces\EmployeeServiceInterface;
use api\modules\Employee\models\Employee;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class EmployeeController extends ActiveController
{
    public $modelClass = Employee::class;

    public function __construct(
        $id,
        $module,
        private readonly EmployeeServiceInterface $employeeService,
        private readonly RbacValidationHelper $validationHelper,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'view' => ['GET'],
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH'],
                'delete' => ['DELETE'],
                'view-self' => ['GET'],
                'active-employees' => ['GET'],
            ],
        ];

        unset($behaviors['actions']['create']);
        unset($behaviors['actions']['update']);
        unset($behaviors['actions']['delete']);

        return $behaviors;
    }

    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionCreate(): Response
    {
        $this->validationHelper->validatePermissionsOrFail(['manageEmployees']);
        $data = Yii::$app->request->post();

        try {
            $employee = $this->employeeService->createEmployee($data);

            Yii::$app->response->statusCode = 201;
            return $this->asJson([
                'success' => true,
                'data' => $employee->toArray(),
                'message' => 'Employee created successfully.',
            ]);
        } catch (Exception $e) {
            Yii::$app->response->statusCode = 400;
            return $this->asJson([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     * @throws Exception
     */
    public function actionUpdate(int $id): Response
    {
        $this->validationHelper->validatePermissionsOrFail(['manageEmployees']);
        $data = Yii::$app->request->post();

        try {
            $updatedEmployee = $this->employeeService->updateEmployee($id, $data);

            Yii::$app->response->statusCode = 200;
            return $this->asJson([
                'success' => true,
                'data' => $updatedEmployee->toArray(),
                'message' => 'Employee updated successfully.',
            ]);
        } catch (Exception|\Exception $e) {
            Yii::$app->response->statusCode = 400;
            return $this->asJson([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @throws ForbiddenHttpException
     * @throws Exception
     */
    public function actionDelete(int $id): Response
    {
        $this->validationHelper->validatePermissionsOrFail(['manageEmployees']);

        try {
            $this->employeeService->deleteEmployee($id);

            Yii::$app->response->statusCode = 204;
            return $this->asJson([
                'success' => true,
                'data' => null,
                'message' => 'Employee deleted successfully.',
            ]);
        } catch (Exception|Throwable $e) {
            Yii::$app->response->statusCode = 400;
            return $this->asJson([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
            ]);
        }
    }


    /**
     * @throws ForbiddenHttpException
     */
    public function actionViewSelf(): Response
    {
        $userId = Yii::$app->user->id;

        $this->validationHelper->validatePermissionsOrFail(['viewOwnProfile']);

        try {
            $employee = $this->employeeService->getEmployeeById($userId);

            Yii::$app->response->statusCode = 200;
            return $this->asJson([
                'success' => true,
                'data' => $employee->toArray(),
                'message' => 'Employee profile retrieved successfully.',
            ]);
        } catch (Exception $e) {
            Yii::$app->response->statusCode = 500;
            return $this->asJson([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve employee profile.',
                'errors' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionActiveEmployees(): Response
    {
        $this->validationHelper->validatePermissionsOrFail(['manageEmployees', 'viewTeam']);

        try {
            $employees = $this->employeeService->getActiveEmployees();

            Yii::$app->response->statusCode = 200;
            return $this->asJson([
                'success' => true,
                'data' => $employees,
                'message' => 'Active employees retrieved successfully.',
            ]);
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return $this->asJson([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve active employees.',
                'errors' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): ?Employee
    {
        $employee = Employee::findOne($id);
        if ($employee === null) {
            throw new NotFoundHttpException('Employee not found.');
        }
        return $employee;
    }
}
