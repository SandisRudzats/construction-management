<?php

declare(strict_types=1);

namespace api\modules\WorkTask\controllers\v1;

use api\helpers\RbacValidationHelper;
use api\modules\WorkTask\interfaces\WorkTaskServiceInterface;
use api\modules\WorkTask\models\WorkTask;
use Exception;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class WorkTaskController extends ActiveController
{
    public function __construct(
        $id,
        $module,
        private readonly WorkTaskServiceInterface $workTaskService,
        private readonly RbacValidationHelper $validationHelper,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public $modelClass = WorkTask::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['create', 'update', 'delete', 'employee-tasks'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH'],
                'delete' => ['DELETE'],
                'employee-tasks' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionCreate(): Response
    {
        $this->validationHelper->validatePermissionsOrFail(['manageOwnTasks', 'manageAllTasks']);
        $data = Yii::$app->request->post();

        try {
            $workTask = $this->workTaskService->createTask($data);

            Yii::$app->response->statusCode = 201;
            return $this->asJson([
                'success' => true,
                'data' => $workTask->toArray(),
                'message' => 'Work Task created successfully.',
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
     * @param $id
     * @throws ForbiddenHttpException
     */
    public function actionUpdate($id): Response
    {
        $this->validationHelper->validatePermissionsOrFail(['manageOwnTasks', 'manageAllTasks']);
        $data = Yii::$app->request->post();

        try {
            $updatedEmployee = $this->workTaskService->updateTask($id, $data);

            Yii::$app->response->statusCode = 200;
            return $this->asJson([
                'success' => true,
                'data' => $updatedEmployee->toArray(),
                'message' => 'Work task updated successfully.',
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
     */
    public function actionDelete(int $id): Response
    {
        $this->validationHelper->validatePermissionsOrFail(['manageOwnTasks', 'manageAllTasks']);

        try {
            $this->workTaskService->deleteTask($id);

            Yii::$app->response->statusCode = 204;
            return $this->asJson([
                'success' => true,
                'data' => null,
                'message' => 'Work task deleted successfully.',
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
    public function actionEmployeeTasks(): Response
    {
        $this->validationHelper->validatePermissionsOrFail(['viewOwnTasks']);

        $userId = Yii::$app->user->id;

        try {
            $workTasks = $this->workTaskService->getTasksByEmployeeId($userId);

            Yii::$app->response->statusCode = 200;
            return $this->asJson([
                'success' => true,
                'data' => $workTasks,
                'message' => 'Employee work tasks retrieved successfully.',
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
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ?WorkTask
    {
        $task = WorkTask::findOne($id);
        if ($task === null) {
            throw new NotFoundHttpException('Work Task not found.');
        }
        return $task;
    }
}
