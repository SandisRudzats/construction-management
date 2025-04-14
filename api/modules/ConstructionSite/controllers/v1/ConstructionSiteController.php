<?php

declare(strict_types=1);

namespace api\modules\ConstructionSite\controllers\v1;

use api\modules\ConstructionSite\models\ConstructionSite;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use Yii;

class ConstructionSiteController extends ActiveController
{
    public $modelClass = ConstructionSite::class;

    public function behaviors()
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

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionCreate()
    {
        $model = new ConstructionSite();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            return $model;
        } else {
            return $model->getErrors();
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            return $model;
        } else {
            return $model->getErrors();
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->getResponse()->setStatusCode(204);
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

    public function actionEmployeeSites(int $id)
    {

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

    protected function findModel($id)
    {
        if (($model = ConstructionSite::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}