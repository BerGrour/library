<?php

namespace frontend\controllers;

use common\models\Files;
use common\models\InfoarticleSearch;
use common\models\Inforelease;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * InforeleaseController implements the CRUD actions for Inforelease model.
 */
class InforeleaseController extends Controller
{
    const DIR = 'Files/Inform/';
    
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
     * Displays a single Inforelease model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchChildModel = $this->findChild();
        $childProvider = $searchChildModel->search(Yii::$app->request->queryParams, $id);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'child_provider_model' => $childProvider,
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
        if (!Yii::$app->user->can('inforelease/' . $action)) {
            throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
        }
    }

    /**
     * Creates a new Inforelease model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @param int|null $seria_id индекс серии
     * @return string|\yii\web\Response
     */
    public function actionCreate($seria_id = null)
    {
        $this->getAccess('create');

        $model = new Inforelease();
        $model->publishyear = Yii::$app->formatter->asDate('now', 'yyyy');
        $model->seria_id = $seria_id;

        if ($this->request->isPost) {
            $file = UploadedFile::getInstance($model, 'uploadedFile');
            if ($file) {
                $filename = time() . '.' . $file->extension;
                $file->saveAs(self::DIR . $filename);
    
                $fileModel = new Files();
                $fileModel->filepath = self::DIR . $filename;
                $fileModel->filename = $file->name;
                $fileModel->save();
    
                $model->file_id = $fileModel->id;
            }

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
     * Updates an existing Inforelease model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->getAccess('update');

        $model = $this->findModel($id);

        if ($this->request->isPost) {
            $file = UploadedFile::getInstance($model, 'uploadedFile');
            if ($file) {
                if ($model->file) {
                    unlink($model->file->filepath);
                    $model->file->delete();
                }

                $filename = time() . '.' . $file->extension;
                $file->saveAs(self::DIR . $filename);

                $fileModel = new Files();
                $fileModel->filepath = self::DIR . $filename;
                $fileModel->filename = $file->name;
                $fileModel->save();

                $model->file_id = $fileModel->id;
            }

            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } 

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Inforelease model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->getAccess('delete');
        $model = $this->findModel($id);

        $fileModel = Files::findOne($model->file_id);
        if ($fileModel) {
            unlink($fileModel->filepath);
            $fileModel->delete();
        }
        $model->delete();
        Yii::$app->session->setFlash('success', 'Инфо выпуск успешно удален.');

        return $this->redirect(['/seria' . '/view', 'id' => $model->seria_id]);
    }

    /**
     * Finds the Inforelease model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Inforelease the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Inforelease::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Страницы не существует');
    }

    /**
     * Поиск записей в инфо выпуске для соответствующего инфо выпуска
     * 
     * @throws NotFoundHttpException
     * @return \common\models\InfoarticleSearch 
     */
    protected function findChild()
    {
        $searchModel = new InfoarticleSearch();

        if ($searchModel !== null) {
            return $searchModel;
        }

        throw new NotFoundHttpException('Страницы не существует');
    }
}
