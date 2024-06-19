<?php

namespace common\models;

use Yii;
use yii\data\ArrayDataProvider;
use yii\db\ActiveRecord;

class CatalogSearch extends ActiveRecord
{
    public $search;
    public $bool_book;
    public $bool_journal;
    public $bool_inforelease;
    public $bool_statrelease;

    /**
     * @inheritdoc
     */
    public function rules() 
    {
        return [[
            ['search', 'bool_book', 'bool_journal', 'bool_inforelease', 'bool_statrelease'],
            'safe'
        ]];
    }

    public function search($params)
    {
        $this->load($params);
        $search_clear = trim(preg_replace('/[^a-zA-Z0-9а-яА-Я \/]/u', ' ', $this->search));

        if ($search_clear != "" && (
            $this->bool_book || 
            $this->bool_journal || 
            $this->bool_inforelease || 
            $this->bool_statrelease)
        ){
            $words = preg_split('/\s+/', $search_clear);

            $wordsWithPlus = array_map(function($word) {
                if (mb_strlen($word) > 2) {
                    return "+*" . $word . "*";
                }
            }, $words);
            $required = implode(" ", $wordsWithPlus);

            $attributes = [
                ':default' => $search_clear,
                ':required' => $required,
                ':for_like' => '%' . $search_clear . '%'
            ];

            $bookData = $issueData = $articleData = $statreleaseData = $inforeleaseData = [];
            $countTypes["в инфо выпусках"] = $countTypes["в стат сборниках"] = $countTypes["в журналах"] = $countTypes['в книгах'] = null;

            if ($this->bool_book) {
                $queryBook = "(
                SELECT book.id, book.code, book.ISBN, book.name,
                    book.authorsign, rubric.title as rubr
                FROM {{book}}
                LEFT JOIN {{rubric}} ON rubric.id = book.rubric_id
                WHERE MATCH (book.code, book.name, book.ISBN, book.authorsign,
                    book.additionalname, book.response, book.bookinfo,
                    book.publishplace, book.publishhouse, book.annotation,
                    book.key_words)
                AGAINST (:required IN BOOLEAN MODE)
            UNION
                SELECT book.id, book.code, book.ISBN, book.name,
                book.authorsign, rubric.title as rubr
                FROM {{book}}
                LEFT JOIN {{rubric}} ON rubric.id = book.rubric_id
                WHERE rubric.title LIKE :for_like
            ) as catalog
                ORDER BY CASE
                    WHEN code = :default THEN 0
                    WHEN name LIKE :for_like OR ISBN = :default OR authorsign = :default THEN 1
                    WHEN rubr LIKE :for_like THEN 2
                    ELSE 3
                END, name ASC, code DESC";
                
                $bookData = Yii::$app->db->createCommand(
                    "SELECT DISTINCT id, 1 AS order_edition, code, name,
                        'Book' AS classModel, 'book' as tableName
                        FROM {$queryBook}",
                    $attributes
                )->cache(3600)->queryAll();

                $countTypes['в книгах'] = Yii::$app->db->createCommand(
                    "SELECT COUNT(DISTINCT id) FROM {$queryBook}",
                    $attributes
                )->cache(3600)->queryScalar();
            }

            if ($this->bool_journal) {
                $queryIssue = "(
                SELECT DISTINCT issue.id, journal.name, issue.issueyear as year,
                    issue.issuenumber as issue_num, journal.ISSN as ISBN,
                    rubric.title as rubr
                FROM {{issue}}
                INNER JOIN {{journal}} ON issue.journal_id = journal.id
                LEFT JOIN {{rubric}} ON rubric.id = journal.rubric_id
            ) as catalog
                WHERE name LIKE :for_like OR
                    ISBN LIKE :for_like OR
                    issue_num = :default OR
                    rubr LIKE :for_like OR
                    year LIKE :default
                ORDER BY CASE
                    WHEN issue_num = :default THEN 0
                    ELSE 1
                END, name ASC, issue_num DESC";

                $issueData = Yii::$app->db->createCommand(
                    "SELECT id, 2 as order_edition, issue_num, name,
                        'Issue' as classModel, 'issue' as tableName
                        FROM {$queryIssue}",
                    $attributes
                )->cache(3600)->queryAll();

                $issue_count = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM {$queryIssue}",
                    $attributes
                )->cache(3600)->queryScalar();

                $queryArticle = "(
                SELECT DISTINCT id, name, annotation, key_words
                FROM {{article}}
                WHERE MATCH (name, annotation, key_words)
                AGAINST (:required IN BOOLEAN MODE)
            ) as catalog
                ORDER BY CASE
                    WHEN name LIKE :for_like THEN 0
                    WHEN key_words LIKE :for_like THEN 1
                    ELSE 2
                END,  name ASC";

                $articleData = Yii::$app->db->createCommand(
                    "SELECT id, 3 as order_edition, name,
                        'Article' as classModel, 'article' as tableName
                        FROM {$queryArticle}",
                    $attributes
                )->cache(3600)->queryAll();

                $articles_count = Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM {$queryArticle}",
                    $attributes
                )->cache(3600)->queryScalar();

                $countTypes["в журналах"] = $issue_count + $articles_count;
            }

            if ($this->bool_statrelease) {
                $queryStatrelease = "(
                SELECT stat.id, stat.name, stat.code, stat.additionalname,
                    stat.response, stat.publishplace, stat.authorsign,
                    stat.key_words, rubric.title as rubr
                FROM {{statrelease}} as stat
                LEFT JOIN {{statreleaserubric}} as rubric
                    ON rubric.id = stat.rubric_id
                WHERE MATCH (name, additionalname, response, publishplace,
                    authorsign, key_words)
                AGAINST (:required IN BOOLEAN MODE)
            UNION 
                SELECT stat.id, stat.name, stat.code, stat.additionalname,
                    stat.response, stat.publishplace, stat.authorsign,
                    stat.key_words, rubric.title as rubr
                FROM {{statrelease}} as stat
                LEFT JOIN {{statreleaserubric}} as rubric
                    ON rubric.id = stat.rubric_id
                WHERE stat.code LIKE :default OR
                    rubric.title LIKE :for_like
            ) as catalog
                ORDER BY CASE
                    WHEN code = :default THEN 0
                    WHEN name LIKE :for_like OR authorsign = :default THEN 1
                    WHEN rubr LIKE :for_like THEN 2
                    ELSE 3
                END, name ASC, code DESC";

                $statreleaseData = Yii::$app->db->createCommand(
                    "SELECT DISTINCT id, 4 as order_edition, name,
                        'Statrelease' as classModel, 'statrelease' as tableName
                        FROM {$queryStatrelease}",
                    $attributes
                )->cache(3600)->queryAll();

                $countTypes["в стат сборниках"] = Yii::$app->db->createCommand(
                    "SELECT COUNT(DISTINCT id) FROM {$queryStatrelease}",
                    $attributes
                )->cache(3600)->queryScalar();
            }

            if ($this->bool_inforelease) {
                $queryInforelease = "(
                SELECT infoart.id, infoart.name, infoart.source as response,
                    infoart.additionalinfo as adname, inforel.numbersk,
                    inforel.publishyear as year, inforel.number as issue_num,
                    rubric.title as rubr
                FROM {{infoarticle}} as infoart
                INNER JOIN {{inforelease}} as inforel ON infoart.inforelease_id = inforel.id
                LEFT JOIN {{rubric}} ON rubric.id = inforel.rubric_id
                WHERE MATCH (name, additionalinfo, source)
                AGAINST (:required IN BOOLEAN MODE)
            UNION
                SELECT infoart.id, infoart.name, infoart.source as response,
                infoart.additionalinfo as adname, inforel.numbersk,
                inforel.publishyear as year, inforel.number as issue_num,
                rubric.title as rubr
                FROM {{infoarticle}} as infoart
                INNER JOIN {{inforelease}} as inforel ON infoart.inforelease_id = inforel.id
                LEFT JOIN {{rubric}} ON rubric.id = inforel.rubric_id
                WHERE inforel.numbersk LIKE :default OR
                    inforel.number LIKE :default OR
                    inforel.publishyear LIKE :default OR
                    rubric.title LIKE :for_like
            ) as catalog
                ORDER BY CASE
                    WHEN numbersk = :default THEN 0
                    WHEN name LIKE :for_like OR issue_num = :default THEN 1
                    WHEN rubr LIKE :for_like THEN 2
                    ELSE 3
                END, name ASC, numbersk DESC";

                $inforeleaseData = Yii::$app->db->createCommand(
                    "SELECT DISTINCT id, 5 as order_edition, name,
                        'Infoarticle' as classModel, 'infoarticle' as tableName
                        FROM {$queryInforelease}",
                    $attributes
                )->cache(3600)->queryAll();

                $countTypes["в инфо выпусках"] = Yii::$app->db->createCommand(
                    "SELECT COUNT(DISTINCT id) FROM {$queryInforelease}",
                    $attributes
                )->cache(3600)->queryScalar();                
            }

            if (isset($bookData) || 
                isset($issueData) ||
                isset($articleData) ||
                isset($statreleaseData) ||
                isset($inforeleaseData)) {
                
                $dataProvider = new ArrayDataProvider([
                    'allModels' => array_merge(
                        $bookData,
                        $issueData,
                        $articleData,
                        $statreleaseData,
                        $inforeleaseData
                    ),
                    'key' => function ($model) {
                        return $model['tableName'] . '_id;' . $model['id'];
                    },
                    'totalCount' => $countTypes['в книгах'] + $countTypes["в журналах"] + $countTypes["в стат сборниках"] + $countTypes["в инфо выпусках"],
                    'sort' => [
                        'attributes' => [
                            'order_edition'
                        ],
                        'defaultOrder' => [
                            'order_edition' => SORT_ASC
                        ],
                    ],
                    'pagination' => [
                        'pageSize' => Yii::$app->params['pageSize'],
                    ],
                ]);
            }
        } else {
            $dataProvider = null;
            $countTypes = null;
        }

        return [$dataProvider, $countTypes];
    }
}