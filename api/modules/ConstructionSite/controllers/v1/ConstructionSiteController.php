<?php

declare(strict_types=1);

namespace api\modules\ConstructionSite\controllers\v1;

use api\interfaces\ConstructionSiteServiceInterface;
use yii\rest\Controller;

class ConstructionSiteController extends Controller
{
    public function __construct(
        $id,
        $module,
        private ConstructionSiteServiceInterface $constructionSiteService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }
}