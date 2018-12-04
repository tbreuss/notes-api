<?php

namespace notes\commands;

use yii\console\Controller;
use yii\console\ExitCode;

class TagController extends Controller
{
    /**
     * Clean tags
     * @return int
     * @throws \yii\db\Exception
     */
    public function actionClean()
    {
        try {
            $sql = '
                DELETE FROM tags
                WHERE id NOT IN (
                    SELECT tag_id
                    FROM article_to_tag
                );        
            ';
            $command = \Yii::$app->db->createCommand($sql);
            $num = $command->execute();
            echo sprintf("%s tags removed\n", $num);

            $sql = '
                UPDATE articles 
                SET tag_ids = (
                SELECT GROUP_CONCAT(tag_id ORDER BY tag_id)
                    FROM article_to_tag
                    WHERE article_to_tag.article_id = articles.id 
                );                           
            ';
            $command = \Yii::$app->db->createCommand($sql);
            $num = $command->execute();
            echo sprintf("%s articles updated\n", $num);

            return ExitCode::OK;
        } catch (Exception $e) {
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Update frequencies
     * @return int
     * @throws \yii\db\Exception
     */
    public function actionUpdateFrequency()
    {
        try {
            $sql = '
                UPDATE tags 
                SET frequency = (
                    SELECT COUNT(article_id) 
                    FROM article_to_tag
                    WHERE article_to_tag.tag_id = tags.id 
                );            
            ';
            $command = \Yii::$app->db->createCommand($sql);
            $num = $command->execute();
            echo sprintf("%s frequencies updated\n", $num);
            return ExitCode::OK;
        } catch (Exception $e) {
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
