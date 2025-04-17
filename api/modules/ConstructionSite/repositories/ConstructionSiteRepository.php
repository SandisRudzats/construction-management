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
    public function findAll(): array
    {
        return ConstructionSite::find()->all();
    }

    public function find(int $id): ?ConstructionSite
    {
        return ConstructionSite::findOne($id);
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

    /**
     * @return ConstructionSite[]
     */
    public function getSitesByIds(array $siteIds): array
    {
        return ConstructionSite::find()->where(['id' => $siteIds])->all() ?? [];
    }

    /**
     * @return ConstructionSite[]
     */
    public function getSitesByManagerId(int $id): array
    {
        return ConstructionSite::find()->where(['manager_id' => $id])->all() ?? [];
    }
}