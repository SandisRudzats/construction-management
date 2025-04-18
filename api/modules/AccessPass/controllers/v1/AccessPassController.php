<?php

declare(strict_types=1);

namespace api\modules\AccessPass\controllers\v1;

use api\helpers\UserRequestValidationHelper;
use api\modules\AccessPass\models\AccessPass;
use api\modules\AccessPass\services\AccessPassService;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AccessPassController extends ActiveController
{
    public $modelClass = AccessPass::class;

    public function __construct(
        $id,
        $module,
        private readonly AccessPassService $accessPassService,
        private readonly UserRequestValidationHelper $validationHelper,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        $this->validationHelper->ensureJsonRequest();
        $this->validationHelper->sanitizeRequestData();

        return parent::beforeAction($action);
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['index', 'view', 'create', 'update', 'delete'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
                [
                    'actions' => ['validate-access'],
                    'allow' => true,
                    'verbs' => ['POST']
                ],
                [
                    'actions' => ['update-from-task'],
                    'allow' => true,
                    'verbs' => ['PUT']
                ]
            ],
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'view' => ['GET'],
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH'],
                'delete' => ['DELETE'],
                'validate-access' => ['POST'],
                'update-from-task' => ['PUT'],
            ],
        ];

        return $behaviors;
    }

    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionCreate(): Response
    {
        $this->validationHelper->validatePermissionsOrFail(['manageEmployees', 'manageOwnTasks']);
        $data = Yii::$app->request->bodyParams;

        try {
            $accessPass = $this->accessPassService->createAccessPass($data);

            Yii::$app->response->statusCode = 201;
            return $this->asJson([
                'success' => true,
                'data' => $accessPass,
                'message' => 'Access pass created successfully.',
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
     * @throws \Exception
     */
    public function actionUpdateFromTask(): Response
    {
        $this->validationHelper->validatePermissionsOrFail(['manageEmployees', 'manageOwnTasks']);
        $data = Yii::$app->request->bodyParams;

        try {
            $accessPass = $this->accessPassService->updateAccessPassFromTask($data);

            Yii::$app->response->statusCode = 200;
            return $this->asJson([
                'success' => true,
                'data' => $accessPass,
                'message' => 'Access pass created successfully.',
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
     * @throws ForbiddenHttpException
     */
    public function actionValidateAccess(): Response
    {
        $this->validationHelper->validatePermissionsOrFail(['viewAssignedSites']);
        $data = Yii::$app->request->bodyParams;

        try {
            $accessPass = $this->accessPassService->validateAccess($data);

            Yii::$app->response->statusCode = 200;
            return $this->asJson([
                'success' => true,
                'data' => $accessPass,
                'message' => 'Access granted.',
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
     */
    protected function findModel(int $id): ?AccessPass
    {
        $accessPass = AccessPass::findOne($id);
        if ($accessPass === null) {
            throw new NotFoundHttpException('Employee not found.');
        }
        return $accessPass;
    }
}