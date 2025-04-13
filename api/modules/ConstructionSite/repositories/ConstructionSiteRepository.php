<?php

declare(strict_types=1);

namespace api\modules\ConstructionSite\repositories;

use api\modules\ConstructionSite\interfaces\ConstructionSiteRepositoryInterface;
use api\modules\ConstructionSite\models\ConstructionSite;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

class ConstructionSiteRepository implements ConstructionSiteRepositoryInterface
{
    public function find(int $id): ?ConstructionSite
    {
        return ConstructionSite::findOne($id);
    }

    public function findAll(): array
    {
        return ConstructionSite::find()->all();
    }

    /**
     * @throws Exception
     */
    public function save(ConstructionSite $constructionSite): bool
    {
        return $constructionSite->save();
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function delete(ConstructionSite $constructionSite): bool
    {
        return $constructionSite->delete();
    }
}