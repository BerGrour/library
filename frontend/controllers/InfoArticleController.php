<?php

namespace frontend\controllers;

use common\models\Infoarticle;
use common\models\InfoarticleAuthor;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InfoarticleController implements the CRUD actions for Infoarticle model.
 */
class InfoarticleController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Infoarticle models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Infoarticle::find(),
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize']
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Infoarticle model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Проверяет доступ пользователя
     * 
     * @param string $action тип действия
     * @throws ForbiddenHttpException если доступ закрыт
     */
    public function getAccess($action)
    {
        if (!Yii::$app->user->can('infoarticle/' . $action)) {
            throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
        }
    }

    /**
     * Creates a new Infoarticle model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @param int $inforelease_id индекс родительского инфо выпуска
     * @return string|\yii\web\Response
     */
    public function actionCreate($inforelease_id = null)
    {
        $this->getAccess('create');

        $model = new Infoarticle();
        $model->type = 1;
        $model->reciept_year = date("Y");
        $model->recieptdate = Yii::$app->formatter->asDate('now', 'yyyy-MM-dd');
        $model->inforelease_id = $inforelease_id;

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if ($model->type == 1) {
                    $model->source = Yii::$app->request->post('Infoarticle')['source_name'] . " №" . Yii::$app->request->post('Infoarticle')['source_number'];
                } else if ($model->type == 2) {
                    $model->source = Yii::$app->request->post('Infoarticle')['source_name'] . " №" . Yii::$app->request->post('Infoarticle')['source_number'];
                    $model->recieptdate = Yii::$app->request->post('Infoarticle')['reciept_year'] . "-00-00";
                }
                if ($model->save()) {
                    $selectedAuthors = Yii::$app->request->post('Infoarticle')['infoarticleAuthorIds'];
                    if (!empty($selectedAuthors)) {
                        foreach ($selectedAuthors as $authorId) {
                            $articleAuthor = new InfoarticleAuthor();
                            $articleAuthor->infoarticle_id = $model->id;
                            $articleAuthor->author_id = $authorId;
                            $articleAuthor->type = 0;
                            $articleAuthor->save();
                        }
                    }
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Infoarticle model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->getAccess('update');

        $model = $this->findModel($id);
        if ($model->type != 3) {
            list($model->source_name, $model->source_number) = $model->getDevideSource();
            if ($model->type == 2) {
                $model->reciept_year = $model->getYearFromDate();
            }
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if ($model->type != 3) {
                    $model->source = Yii::$app->request->post('Infoarticle')['source_name'] . " №" . Yii::$app->request->post('Infoarticle')['source_number'];
                }
                if ($model->type == 2) {
                    $model->recieptdate = Yii::$app->request->post('Infoarticle')['reciept_year'] . "-00-00";
                }
                if ($model->save()) {
                    $selectedAuthors = Yii::$app->request->post('Infoarticle')['infoarticleAuthorIds'];
                    $authors = InfoarticleAuthor::findAll(['infoarticle_id' => $model->id, 'type' => 0]);
                    foreach ($authors as $author) {
                        $author->delete();
                    }
                    if (!empty($selectedAuthors)) {
                        foreach ($selectedAuthors as $authorId) {
                            $articleAuthor = new InfoarticleAuthor();
                            $articleAuthor->infoarticle_id = $model->id;
                            $articleAuthor->author_id = $authorId;
                            $articleAuthor->type = 0;
                            $articleAuthor->save();
                        }
                    }
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Infoarticle model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->getAccess('delete');
        $model = $this->findModel($id);

        $model->delete();
        Yii::$app->session->setFlash('success', 'Инфо статья успешно удалена.');

        return $this->redirect([
            '/inforelease' . '/view',
            'id' => $model->inforelease_id
        ]);
    }

    /**
     * Finds the Infoarticle model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Infoarticle the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Infoarticle::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Страницы не существует');
    }
}
