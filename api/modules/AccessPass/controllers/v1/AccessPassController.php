<?php

declare(strict_types=1);

namespace api\modules\AccessPass\controllers\v1;

use api\modules\AccessPass\models\AccessPass;
use Yii;
use yii\db\Exception;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;

class AccessPassController extends ActiveController
{
    public $modelClass = AccessPass::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['index', 'view', 'create', 'update', 'delete'],
                    'allow' => true,
                    'roles' => ['@'], // Requires any authenticated user.  Adjust as needed.
                ],
                [
                    'actions' => ['validate-access'], //addded validate access
                    'allow' => true,
                    'verbs' => ['POST']
                ],
                [
                    'actions' => ['update-from-task'], //addded validate access
                    'allow' => true,
                    'verbs' => ['POST']
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
                'validate-access' => ['POST'], // Added validate-access action
                'update-from-task' => ['POST'], // Added validate-access action
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => AccessPass::find(),
        ]);

        return $dataProvider;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    public function actionCreate()
    {
        $model = new AccessPass();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            Yii::$app->getResponse()->setStatusCode(201);
            return $model;
        } else {
            Yii::$app->getResponse()->setStatusCode(422);
            return ['errors' => $model->getErrors()];
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            return $model;
        } else {
            Yii::$app->getResponse()->setStatusCode(422);
            return ['errors' => $model->getErrors()];
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->getResponse()->setStatusCode(204);
    }

    /**
     * @throws Exception
     */
    public function actionUpdateFromTask()
    {
        $body = Yii::$app->request->post();

        $accessPass = AccessPass::find()
            ->where([
                'construction_site_id' => $body['construction_site_id'],
                'employee_id' => $body['employee_id'],
                'work_task_id' => $body['work_task_id'],
            ])
            ->one();

        if (!$accessPass) {
            $accessPass = new AccessPass();
            $accessPass->construction_site_id = $body['construction_site_id'];
            $accessPass->employee_id = $body['employee_id'];
            $accessPass->work_task_id = $body['work_task_id'];
        }

        $accessPass->valid_from = $body['valid_from'];
        $accessPass->valid_to = $body['valid_to'];

        if ($accessPass->save()) {
            return $this->asJson([
                'success' => true,
                'message' => $accessPass->isNewRecord ? 'Access pass created.' : 'Access pass updated.',
                'data' => $accessPass,
            ]);
        }

        return $this->asJson([
            'success' => false,
            'message' => 'Failed to save access pass.',
            'errors' => $accessPass->getErrors(),
        ]);
    }

    /**
     * Validates if an employee has access to a construction site at a given time.
     * This is the "modern" access pass validation endpoint.
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionValidateAccess()
    {
        $request = Yii::$app->request;
        $employeeId = $request->post('employeeId');
        $constructionSiteId = $request->post('constructionSiteId');
        $workTaskId = $request->post('workTaskId');
        $checkDate = $request->post('checkDate');

        // Validate input data.  Use Yii's validation features.
        if (!$employeeId || !$constructionSiteId || !$workTaskId || !$checkDate) {
            throw new BadRequestHttpException(
                'Missing required parameters: employeeId, constructionSiteId, workTaskId, and checkDate are required.'
            );
        }

        // Convert checkDate to a DateTime object for safe comparison.
        try {
            $checkDateTime = new \DateTime($checkDate);
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Invalid date format for checkDate.  Use YYYY-MM-DD HH:MM:SS.');
        }

        // Query the database to check for a valid access pass.
        $accessPass = AccessPass::find()
            ->where([
                'employee_id' => $employeeId,
                'construction_site_id' => $constructionSiteId,
                'work_task_id' => $workTaskId,
            ])
            ->andWhere(['<=', 'valid_from', $checkDateTime->format('Y-m-d H:i:s')])
            ->andWhere(['>=', 'valid_to', $checkDateTime->format('Y-m-d H:i:s')])
            ->one();

        // Return the appropriate response.
        if ($accessPass) {
            return [
                'success' => true,
                'message' => 'Access granted',
                'data' => ['accessPassId' => $accessPass->id],
            ];
        } else {
            Yii::$app->getResponse()->setStatusCode(403); // 403 Forbidden
            return [
                'success' => false,
                'message' => 'Access denied',
            ];
        }
    }

    protected function findModel($id)
    {
        if (($model = AccessPass::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
