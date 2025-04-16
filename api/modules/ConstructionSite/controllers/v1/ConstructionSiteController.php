<?php

declare(strict_types=1);

namespace api\modules\ConstructionSite\controllers\v1;

use api\helpers\RbacValidationHelper;
use api\modules\ConstructionSite\interfaces\ConstructionSiteServiceInterface;
use api\modules\ConstructionSite\models\ConstructionSite;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
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
                    'actions' => ['create', 'update', 'delete', 'work-tasks', 'my-sites', 'index', 'manager-sites'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
                [
                    'actions' => ['manager-sites'],
                    'allow' => true,
                    'roles' => ['manager', 'admin'],
                ],
            ],
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH'],
                'delete' => ['DELETE'],
                'work-tasks' => ['GET'],
                'my-sites' => ['GET'],
                'index' => ['GET'],
                'manager-sites' => ['GET'],
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
    public function actionDelete($id)
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

    public function actionWorkTasks($id)
    {
        $model = $this->findModel($id);
        return $model->workTasks;
    }

    public function actionMySites()
    {
        $userId = Yii::$app->user->id;
        $dataProvider = new ActiveDataProvider([
            'query' => ConstructionSite::find()->where(['manager_id' => $userId]),
        ]);

        return $dataProvider;
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        if ($user) {
            if ($user->role === 'admin') {
                $dataProvider = new ActiveDataProvider([
                    'query' => ConstructionSite::find(),
                ]);

                return $dataProvider;
            } elseif ($user->role === 'employee') {
                // Get the employee's work tasks and extract the construction site IDs.
                $workTaskQuery = \api\modules\WorkTask\models\WorkTask::find()->where(['employee_id' => $user->id]);
                $siteIds = $workTaskQuery->select('construction_site_id')->distinct()->column();

                // If the employee has work tasks, retrieve the associated sites.
                if (!empty($siteIds)) {
                    $dataProvider = new ActiveDataProvider([
                        'query' => ConstructionSite::find()->where(['id' => $siteIds]),
                    ]);
                    return $dataProvider;
                } else {
                    // If the employee has no work tasks, return an empty dataset.
                    return new ActiveDataProvider([
                        'query' => ConstructionSite::find()->where(['id' => null]),
                    ]);
                }
            } else { //For managers
                $dataProvider = new ActiveDataProvider([
                    'query' => ConstructionSite::find()->where(['manager_id' => $user->id]),
                ]);
                return $dataProvider;
            }
        }
        Yii::$app->getResponse()->setStatusCode(403);
        return ['message' => 'Forbidden'];
    }

    public function actionManagerSites()
    {
        // 1. Get the manager's ID.
        $managerId = Yii::$app->request->get('managerId');

        if ($managerId === null) {
            Yii::$app->getResponse()->setStatusCode(400);
            return ['message' => 'Missing managerId parameter'];
        }
        // 2.  Find all construction sites managed by this manager.
        $dataProvider = new ActiveDataProvider([
            'query' => ConstructionSite::find()->where(['manager_id' => $managerId]),
        ]);

        // 3.  Return the construction sites.
        return $dataProvider;
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