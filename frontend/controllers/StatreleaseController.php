<?php

namespace frontend\controllers;

use common\models\Logbook;
use common\models\LogbookSearch;
use common\models\Statrelease;
use common\models\StatreleaseSearch;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StatreleaseController implements the CRUD actions for Statrelease model.
 */
class StatreleaseController extends Controller
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
     * Lists all Statrelease models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new StatreleaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Statrelease model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchLogbook = new LogbookSearch();
        $logbookProvider = $searchLogbook->search(Yii::$app->request->queryParams, statrelease_id: $id);

        return $this->render('view', [
            'model' => $this->findModel($id),
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
        if (!Yii::$app->user->can('statrelease/' . $action)) {
            throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
        }
    }

    /**
     * Creates a new Statrelease model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $this->getAccess('create');

        $model = new Statrelease();
        $model->disposition = 1;
        $model->publishyear = Yii::$app->formatter->asDate('now', 'yyyy');
        $model->recieptdate = Yii::$app->formatter->asDate('now', 'yyyy-MM-dd');

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
     * Updates an existing Statrelease model.
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
     * Deletes an existing Statrelease model.
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
        } elseif (Logbook::findOne(['statrelease_id' => $id])) {
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
        Yii::$app->session->setFlash('success', 'Стат сборник успешно удален.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Statrelease model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Statrelease the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Statrelease::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Страницы не существует');
    }

    /**
     * Метод для печати каталожной карточки издания
     * 
     * @param int $id индекс стат. сборника
     * @return string render
     */
    public function actionEditionCard($id)
    {
        return $this->renderPartial('editionCard', [
            'edition' => $this->findModel($id)
        ]);
    }
}
