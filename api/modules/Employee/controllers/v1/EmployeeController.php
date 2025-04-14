<?php

declare(strict_types=1);

namespace api\modules\Employee\controllers\v1;

use api\modules\Employee\models\Employee;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class EmployeeController extends ActiveController
{
    public $modelClass = Employee::class;

    // todo:: add eager loading kur vajag
    // todo:: nočekot vai es nevaru pāriet uz Yii::$app->getRequest()->getBodyParams()
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
                'active-employees' => ['GET'], //added
            ],
        ];

        unset($behaviors['actions']['create']);
        unset($behaviors['actions']['update']);
        unset($behaviors['actions']['delete']);
        unset($behaviors['actions']['view']); //remove view

        return $behaviors;
    }

    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']); //remove view
        return $actions;
    }

    /**
     * Creates a new employee and assigns a role.
     * @return Response
     * @throws ForbiddenHttpException if the user doesn't have permission
     * @throws BadRequestHttpException if the data is invalid
     * @throws Exception
     */
    public function actionCreate(): Response
    {
        // Ensure the user is an admin.  Use Yii::$app->user->can() for authorization checks.
        if (!Yii::$app->user->can('manageEmployees')) {
            throw new ForbiddenHttpException("You don't have permission to create employees.");
        }

        // Get the data from the request.  Use Yii::$app->request->post() for POST data.
        $data = Yii::$app->request->post();

        // Validate required fields.  Throw a BadRequestHttpException for missing data.
        if (empty($data['username']) || empty($data['password']) || empty($data['first_name']) || empty($data['last_name']) || empty($data['role'])) {
            throw new BadRequestHttpException('Missing required fields: username, password, first_name, last_name, role.');
        }

        // Create a new Employee instance.
        $employee = new Employee();
        $employee->username = $data['username'];
        $employee->first_name = $data['first_name'];
        $employee->last_name = $data['last_name'];
        $employee->setPassword($data['password']); // Use setPassword for hashing.
        $employee->role = $data['role'];
        $employee->access_level = $data['access_level'] ?? 1; //default access level
        $employee->manager_id = $data['manager_id'];

        // Validate the employee data.  Return a JSON response with errors if validation fails.
        if (!$employee->validate()) {
            Yii::$app->response->statusCode = 422; // Unprocessable Entity
            return $this->asJson(['errors' => $employee->errors]);
        }

        // Save the employee.  Handle database errors and return a JSON response on failure.
        if (!$employee->save()) {
            Yii::$app->response->statusCode = 500; // Internal Server Error
            return $this->asJson(['errors' => 'Failed to save employee.']);
        }

        // Assign the role using RBAC.  Handle the case where the role doesn't exist.
        $auth = Yii::$app->authManager;
        $roleName = strtolower($data['role']); // Convert role to lowercase
        $role = $auth->getRole($roleName);
        if ($role === null) {
            Yii::$app->response->statusCode = 400; // Bad Request
            return $this->asJson(['errors' => 'Invalid role specified: ' . $data['role']]); // Include the invalid role in the error message
        }
        $auth->assign($role, $employee->id);

        // Return a JSON response indicating success.
        Yii::$app->response->statusCode = 201; // Created
        return $this->asJson([
            'message' => 'Employee created successfully.',
            'employee' => $employee->toArray(), // Use toArray() to control the output.
        ]);
    }


    /**
     * Updates an existing Employee model.
     * @param int $id
     * @return Response|array
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function actionUpdate(int $id): Response|array
    {
        $employee = $this->findModel($id);

        if (!Yii::$app->user->can('manageEmployees', ['employee' => $employee])) {
            throw new ForbiddenHttpException("You don't have permission to update this employee.");
        }

        $data = Yii::$app->request->post();
        $employee->load($data, ''); // Load all post data

        if (!$employee->validate()) {
            Yii::$app->response->statusCode = 422;  //  Unprocessable Entity
            return ['errors' => $employee->errors];
        }

        if (!$employee->save()) {
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Failed to update employee.'];
        }

        return $employee->toArray();
    }

    /**
     * Deletes an existing Employee model.
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionDelete(int $id): Response
    {
        $employee = $this->findModel($id);

        if (!Yii::$app->user->can('deleteEmployee', ['employee' => $employee])) {
            throw new ForbiddenHttpException("You don't have permission to delete this employee.");
        }

        if (!$employee->delete()) {
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Failed to delete employee.'];
        }

        Yii::$app->response->statusCode = 204;  //  No Content
        return Yii::$app->response;
    }

    /**
     * Finds the Employee model based on its primary key value.
     * If the model is not found, a 404 exception will be thrown.
     * @param int $id
     * @return Employee The loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Employee
    {
        $model = Employee::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Employee not found.');
        }
        return $model;
    }

    /**
     * View the profile of the currently logged-in employee.
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionViewSelf(): array
    {
        // Get the ID of the currently logged-in user.
        $userId = Yii::$app->user->id;

        // Find the employee model.
        $employee = Employee::findOne($userId);

        if (!$employee) {
            throw new NotFoundHttpException('Employee not found.');
        }

        // Return the employee data.
        return $employee->toArray();
    }

    /**
     * Returns only employees with active status (status = 1 or status = true).
     * @return ActiveDataProvider
     */
    public function actionActiveEmployees()
    {
        $query = Employee::find()->where(['active' => true]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }
}
