<?php

namespace frontend\controllers;

use common\models\ArticleSearch;
use common\models\Issue;
use common\models\Logbook;
use common\models\LogbookSearch;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * IssueController implements the CRUD actions for Issue model.
 */
class IssueController extends Controller
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
     * Displays a single Issue model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchChildModel = $this->findChild();
        $childProvider = $searchChildModel->search(Yii::$app->request->queryParams, $id);

        $searchLogbook = new LogbookSearch();
        $logbookProvider = $searchLogbook->search(Yii::$app->request->queryParams, issue_id: $id);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'search_child_model' => $searchChildModel,
            'child_provider_model' => $childProvider,
            'logbookProvider' => $logbookProvider,
            'searchLogbook' => $searchLogbook
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
        if (!Yii::$app->user->can('issue/' . $action)) {
            throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
        }
    }

    /**
     * Creates a new Issue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @param int|null $journal_id индекс журнала
     * @return string|\yii\web\Response
     */
    public function actionCreate($journal_id = null)
    {
        $this->getAccess('create');

        $model = new Issue();
        $model->issueyear = Yii::$app->formatter->asDate('now', 'yyyy');
        $model->issuedate = Yii::$app->formatter->asDate('now', 'yyyy-MM-dd');
        $model->journal_id = $journal_id;

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
     * Updates an existing Issue model.
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
     * Deletes an existing Issue model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->getAccess('delete');
        $model = $this->findModel($id);

        if ($model->isBorrowed()) {
            // Отмена удаления
            Yii::$app->session->setFlash('error', 'Издание прямо сейчас на руках.');
            return $this->redirect(Yii::$app->request->referrer);
        } elseif (Logbook::findOne(['issue_id' => $id])) {
            // Списание издания вместо удаления
            $model->withraw = 1;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Издание списано.');
            } else {
                Yii::$app->session->setFlash('error', 'Произошла неизвестная ошибка.');
            }
            return $this->redirect(Yii::$app->request->referrer);
        }
        // Удаление издания
        $model->delete();
        Yii::$app->session->setFlash('success', 'Журнальный выпуск успешно удален.');

        return $this->redirect(['/journal' . '/view', 'id' => $model->journal_id]);
    }

    /**
     * Finds the Issue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Issue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Issue::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Страницы не существует');
    }

    /**
     * Поиск записей в инфо выпуске для соответствующего инфо выпуска
     * 
     * @throws NotFoundHttpException
     * @return \common\models\ArticleSearch
     */
    protected function findChild()
    {
        $searchModel = new ArticleSearch();

        if ($searchModel !== null) {
            return $searchModel;
        }

        throw new NotFoundHttpException('Страницы не существует');
    }

}
