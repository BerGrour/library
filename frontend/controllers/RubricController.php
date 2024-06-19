<?php

namespace frontend\controllers;

use common\models\BookSearch;
use common\models\InforeleaseSearch;
use common\models\JournalSearch;
use common\models\Rubric;
use common\models\RubricSearch;
use common\models\StatreleaseRubricSearch;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RubricController implements the CRUD actions for Rubric model.
 */
class RubricController extends Controller
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
     * Lists all Rubric models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchMain = new RubricSearch();
        $mainProvider = $searchMain->search(Yii::$app->request->queryParams);

        $searchSecond = new StatreleaseRubricSearch();
        $secondProvider = $searchSecond->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchMain' => $searchMain,
            'mainProvider' => $mainProvider,
            'searchSecond' => $searchSecond,
            'secondProvider' => $secondProvider
        ]);
    }

    /**
     * Displays a single Rubric model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $bookSearchModel = new BookSearch();
        $bookProvider = $bookSearchModel->search(Yii::$app->request->queryParams, rubric_id: $id);

        $journalSearchModel = new JournalSearch();
        $journalProvider = $journalSearchModel->search(Yii::$app->request->queryParams, rubric_id: $id);

        $inforeleaseSearchModel = new InforeleaseSearch();
        $inforeleaseProvider = $inforeleaseSearchModel->search(Yii::$app->request->queryParams, rubric_id: $id);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'book' => $bookProvider,
            'bookSearchModel' => $bookSearchModel,
            'journal' => $journalProvider,
            'journalSearchModel' => $journalSearchModel,
            'inforelease' => $inforeleaseProvider,
            'inforeleaseSearchModel' => $inforeleaseSearchModel,
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
        if (!Yii::$app->user->can('rubric/' . $action)) {
            throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
        }
    }

    /**
     * Creates a new Rubric model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $this->getAccess('create');
        $model = new Rubric();

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
     * Updates an existing Rubric model.
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
     * Deletes an existing Rubric model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $this->getAccess('delete');
        $model = $this->findModel($id);

        try {
            $model->delete();
            Yii::$app->session->setFlash('success', 'Рубрика успешно удалена.');
        } catch (\yii\db\IntegrityException $e) {
            Yii::$app->session->setFlash('error', 'Невозможно удалить рубрику, она используется в изданиях.');
            return $this->redirect(["view", "id" => $id]);
        }

        return $this->redirect(['/rubric' . '/index']);
    }

    /**
     * Finds the Rubric model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Rubric the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rubric::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Страницы не существует');
    }
}
