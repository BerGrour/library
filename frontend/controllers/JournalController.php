<?php

namespace frontend\controllers;

use common\models\Issue;
use common\models\Journal;
use common\models\JournalSearch;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * JournalController implements the CRUD actions for Journal model.
 */
class JournalController extends Controller
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
     * Lists all Journal models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new JournalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Journal model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'groupedData' => $this->findChild($id),
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
        if (!Yii::$app->user->can('journal/' . $action)) {
            throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
        }
    }

    /**
     * Creates a new Journal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $this->getAccess('create');

        $model = new Journal();
        $model->disposition = 1;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Journal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->getAccess('update');
        
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Journal model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->getAccess('delete');

        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Журнал успешно удален.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Journal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Journal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Journal::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Страницы не существует');
    }

    /**
     * Определяет список выпусков для соответствующего журнала
     * Если не найдено выдает 404 ошибку.
     * @param int $id ID
     * @return array the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findChild($id)
    {
        $issues = Issue::getIssuesOrdered($id)->all();
        $groupedData = [];
        foreach ($issues as $issue) {
            $groupedData[$issue->issueyear][] = $issue;
        }

        if ($groupedData !== null) {
            return $groupedData;
        }

        throw new NotFoundHttpException('Страницы не существует');
    }

}
