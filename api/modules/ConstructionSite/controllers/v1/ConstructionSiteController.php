<?php

declare(strict_types=1);

namespace api\modules\ConstructionSite\controllers\v1;

use api\helpers\RbacValidationHelper;
use api\modules\ConstructionSite\interfaces\ConstructionSiteServiceInterface;
use api\modules\ConstructionSite\models\ConstructionSite;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ConstructionSiteController extends ActiveController
{
    public $modelClass = ConstructionSite::class;

    public function __construct(
        $id,
        $module,
        private readonly ConstructionSiteServiceInterface $constructionSiteService,
        private readonly RbacValidationHelper $validationHelper,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['create', 'update', 'delete', 'site-work-tasks', 'index'],
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
                'site-work-tasks' => ['GET'],
                'index' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionCreate(): Response
    {
        $this->validationHelper->validatePermissionsOrFail(['manageSites']);
        $data = Yii::$app->request->post();

        try {
            $site = $this->constructionSiteService->createSite($data);

            Yii::$app->response->statusCode = 201;
            return $this->asJson([
                'success' => true,
                'data' => $site->toArray(),
                'message' => 'Construction site created successfully.',
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
    public function actionUpdate(int $id): Response
    {
        $this->validationHelper->validatePermissionsOrFail(['manageSites']);
        $data = Yii::$app->request->post();

        try {
            $updatedEmployee = $this->constructionSiteService->updateSite($id, $data);

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
     */
    public function actionDelete(int $id)
    {
        $this->validationHelper->validatePermissionsOrFail(['manageSites']);

        try {
            $this->constructionSiteService->deleteSite($id);

            Yii::$app->response->statusCode = 204;
            return $this->asJson([
                'success' => true,
                'data' => null,
                'message' => 'Construction site deleted successfully.',
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
     * @throws NotFoundHttpException
     */
    public function actionSiteWorkTasks(int $id): Response
    {
        $this->validationHelper->validatePermissionsOrFail(['manageSites', 'manageOwnSites', 'manageOwnTasks']);

        try {
            $workTasks = $this->constructionSiteService->getSiteWithWorkTasks($id);

            Yii::$app->response->statusCode = 200;
            return $this->asJson([
                'success' => true,
                'data' => $workTasks,
                'message' => 'Construction site work tasks retrieved successfully.',
            ]);
        } catch (Throwable $e) {
            Yii::$app->response->statusCode = 400;
            return $this->asJson([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function actionIndex(): Response
    {
        $userId = (int)Yii::$app->user->id;

        try {
            $sites = $this->constructionSiteService->getSitesByIdAndRole($userId);

            Yii::$app->response->statusCode = 200;
            return $this->asJson([
                'success' => true,
                'data' => $sites,
                'message' => ' Construction sites retrieved successfully.',
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
    protected function findModel(int $id): ?ConstructionSite
    {
        $site = ConstructionSite::findOne($id);
        if ($site === null) {
            throw new NotFoundHttpException('Site not found.');
        }
        return $site;
    }
}