<?php

namespace notes\modules\v1\models;

use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\ServerErrorHttpException;

/**
 * Class Tag
 * @package notes\modules\v1\models
 * @property int id
 * @property string name
 * @property int frequency
 * @property string created
 * @property string modified
 */
class Tag extends ActiveRecord
{
    public static function tableName()
    {
        return '{{tags}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
        ];
    }

    /**
     * @return ActiveDataProvider
     */
    public static function findAllAsProvider()
    {
        $provider = new ActiveDataProvider([
            'query' => static::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $provider;
    }

    public static function findAllSelected(string $q = '', array $tags = [])
    {
        $query = static::find()
            ->select(['t.id', 't.name', 'count(a.id) AS count'])
            ->from('tags t')
            ->innerJoin('articles a', 'FIND_IN_SET(t.id, a.tag_ids)>0')
            ->groupBy('t.id')
            ->orderBy('count DESC, t.name ASC');

        if (!empty($q)) {
            $query->andWhere('(a.title LIKE :q OR a.content LIKE :q)', ['q' => '%' . $q . '%']);
        }

        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $query->andWhere('FIND_IN_SET(:tag_id, a.tag_ids)>0', ['tag_id' => $tag]);
            }
        }

        $tags = $query->asArray()
            ->limit(40)
            ->all();

        return $tags;
    }

    public static function saveAll($tags, $userId)
    {
        // remove doublettes
        $tags = static::explodeTags($tags);

        $ids = [];
        foreach ($tags as $name) {
            $tag = static::findOne(['name' => $name]);
            if ($tag) {
                $ids[] = $tag->id;
            } else {
                $tag = new Tag();
                $tag->name = $name;
                $tag->frequency = 1;
                if (!$tag->save()) {
                    throw new ServerErrorHttpException('Failed to save tags for unknown reason.');
                }
                $ids[] = $tag->id;
            }
        }
        sort($ids);
        return $ids;
    }

    private static function explodeTags(string $tagsCsv): array
    {
        $tags = explode(',', $tagsCsv);
        $trimed = array_map('trim', $tags);
        $unique = static::arrayIunique($trimed);
        return $unique;
    }

    private static function arrayIunique($array)
    {
        $lowered = array_map('strtolower', $array);
        return array_intersect_key($array, array_unique($lowered));
    }

    public static function updateFrequencies(array $ids)
    {
        if (empty($ids)) {
            return 0;
        }

        $sql = "
            UPDATE tags
            SET frequency = (
            SELECT COUNT(tag_id)
                FROM article_to_tag
                WHERE tags.id = article_to_tag.tag_id
                GROUP BY tag_id
            )
            WHERE id IN (:ids);
        ";
        $num = \Yii::$app->db->createCommand($sql, ['ids' => $ids])
            ->execute();
        return $num;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created = new Expression('NOW()');
            } else {
                $this->modified = new Expression('NOW()');
            }
            return true;
        }
        return false;
    }
}
