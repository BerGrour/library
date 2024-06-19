<?php

namespace frontend\controllers;

use common\models\Inforelease;
use common\models\Seria;
use common\models\SeriaSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * SeriaController implements the CRUD actions for Seria model.
 */
class SeriaController extends Controller
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
     * Lists all Seria models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SeriaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Seria model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'groupedData' => $this->findChild($id, null),
        ]);
    }

    /**
     * Возвращает массив с инфо выпусками сгруппированными по годам и с примененными фильтрами данных
     * @param int $id индекс серии
     * @param int $rubric_id индекс искомой рубрики
     */
    public function actionInforeleasesList($id, $rubric_id)
    {
        return $this->renderAjax('_inforeleases_list', [
            'groupedData' => $this->findChild($id, $rubric_id),
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
        if (!Yii::$app->user->can('seria/' . $action)) {
            throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
        }
    }

    /**
     * Creates a new Seria model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $this->getAccess('create');
        $model = new Seria();

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
     * Updates an existing Seria model.
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
     * Deletes an existing Seria model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->getAccess('delete');

        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Инфо серия успешно удалена.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Seria model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Seria the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Seria::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'Страницы не существует'));
    }

    /**
     * Определяет список выпусков для соответствующего журнала
     * Если не найдено выдает 404 ошибку.
     * @param int $id ID
     * @return array the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findChild($id, $rubric_id)
    {
        $series = Inforelease::getReleasesOrdered($id, $rubric_id)->all();
        $groupedData = [];
        foreach ($series as $seria) {
            $groupedData[$seria->publishyear][] = $seria;
        }

        if ($groupedData !== null) {
            return $groupedData;
        }

        throw new NotFoundHttpException('Страницы не существует');
    }
}
