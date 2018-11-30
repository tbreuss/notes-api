<?php

namespace notes\modules\v1\models;

use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\Query;
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
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{tags}}';
    }

    /**
     * @return array
     */
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
        $query = new Query;
        $query->select('t.*');
        $query->from('tags t');

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 1000, // no pagination
            ],
            'sort' => [
                'attributes' => [
                    'name',
                    'frequency' => [
                        'asc' => ['t.frequency' => SORT_DESC, 't.name' => SORT_ASC],
                        'desc' => ['t.frequency' => SORT_ASC, 't.name' => SORT_ASC],
                        'default' => SORT_DESC
                    ],
                    'created' => [
                        'asc' => ['t.created' => SORT_DESC, 't.name' => SORT_ASC],
                        'desc' => ['t.created' => SORT_ASC, 't.name' => SORT_ASC],
                        'default' => SORT_DESC
                    ],
                    'changed' => [
                        'asc' => ['t.modified' => SORT_DESC, 't.name' => SORT_ASC],
                        'desc' => ['t.modified' => SORT_ASC, 't.name' => SORT_ASC],
                        'default' => SORT_DESC
                    ]
                ],
                'defaultOrder' => [
                    'name' => SORT_ASC
                ]
            ]
        ]);
        return $provider;
    }

    /**
     * @param string $q
     * @param array $tags
     * @return array
     */
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
            foreach ($tags as $i => $tagId) {
                $key = 'tag_id_' . $i;
                $query->andWhere("FIND_IN_SET(:${key}, a.tag_ids)>0", [$key => $tagId]);
            }
        }

        $tags = $query
            ->limit(40)
            ->asArray()
            ->all();

        return $tags;
    }

    /**
     * @param string $tags
     * @param int $userId
     * @return array
     * @throws ServerErrorHttpException
     */
    public static function saveAll(string $tags, int $userId)
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

    /**
     * @param string $tagsCsv
     * @return array
     */
    private static function explodeTags(string $tagsCsv): array
    {
        $tags = explode(',', $tagsCsv);
        $trimed = array_map('trim', $tags);
        $unique = static::arrayIunique($trimed);
        return $unique;
    }

    /**
     * @param array $array
     * @return array
     */
    private static function arrayIunique(array $array)
    {
        $lowered = array_map('strtolower', $array);
        return array_intersect_key($array, array_unique($lowered));
    }

    /**
     * @param array $ids
     * @return int
     * @throws Exception
     */
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
        $sql = \Yii::$app->db->createCommand($sql, ['ids' => $ids])->rawSql;
        // does'n work in one shot (~ strange)
        $num = \Yii::$app->db->createCommand($sql)->execute();
        return $num;
    }

    /**
     * @param bool $insert
     * @return bool
     */
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
