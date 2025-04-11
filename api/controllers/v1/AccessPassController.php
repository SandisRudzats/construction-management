<?php

declare(strict_types=1);

namespace api\controllers\v1;

use api\interfaces\AuthServiceInterface;
use Yii;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\auth\HttpBearerAuth;

class AccessPassController extends Controller
{
    public function __construct($id, $module, private AuthServiceInterface $authorizationService, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionValidate(int $constructionSiteId): array
    {
        $employeeId = Yii::$app->user->id;

        if (!$employeeId) {
            throw new ForbiddenHttpException('User not authenticated.');
        }

        if ($this->authorizationService->canAccessSite($employeeId, $constructionSiteId)) {
            return ['access' => true];
        } else {
            throw new ForbiddenHttpException('Access to this construction site is denied.');
        }
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        return $behaviors;
    }
}