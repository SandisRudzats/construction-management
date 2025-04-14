<?php

declare(strict_types=1);

namespace api\modules\WorkTask\controllers\v1;

use api\modules\WorkTask\models\WorkTask;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use Yii;

class WorkTaskController extends ActiveController
{
    public $modelClass = WorkTask::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['create', 'update', 'delete', 'index', 'employee'], // Added 'employee' action
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
                'index' => ['GET'],
                'employee' => ['GET'], // Added 'employee' action to verbs
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
        $model = new WorkTask();
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

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => WorkTask::find(),
        ]);

        return $dataProvider;
    }

    public function actionEmployee()
    {
        $userId = Yii::$app->user->id; // Get user ID from session.

        $query = WorkTask::find()->where(['employee_id' => $userId]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

    protected function findModel($id)
    {
        if (($model = WorkTask::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
