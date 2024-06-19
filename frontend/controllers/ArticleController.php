<?php

namespace frontend\controllers;

use common\models\Article;
use common\models\ArticleAuthor;
use common\models\Files;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends Controller
{
    const DIR = 'Files/articles/';

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
     * Displays a single Article model.
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
        if (!Yii::$app->user->can('article/' . $action)) {
            throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
        }
    }

    /**
     * Creates a new Article model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @param int $issue_id индекс родительского выпуска
     * @return string|\yii\web\Response
     */
    public function actionCreate($issue_id = null)
    {
        $this->getAccess('create');

        $model = new Article();
        $model->issue_id = $issue_id;

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
                $selectedAuthors = Yii::$app->request->post('Article')['articleAuthorIds'];
                if (!empty($selectedAuthors)) {
                    foreach ($selectedAuthors as $authorId) {
                        $articleAuthor = new ArticleAuthor();
                        $articleAuthor->article_id = $model->id;
                        $articleAuthor->author_id = $authorId;
                        $articleAuthor->type = 0;
                        $articleAuthor->save();
                    }
                }
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
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->getAccess('update');

        $model = $this->findModel($id);

        $attachedFile = $model->getFile()->one();
        if ($attachedFile) {
            $model->uploadedFile = $attachedFile->filename;
        }

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
                $selectedAuthors = Yii::$app->request->post('Article')['articleAuthorIds'];
                $authors = ArticleAuthor::findAll(['article_id' => $model->id, 'type' => 0]);
                foreach ($authors as $author) {
                    $author->delete();
                }
                if (!empty($selectedAuthors)) {
                    foreach ($selectedAuthors as $authorId) {
                        $articleAuthor = new ArticleAuthor();
                        $articleAuthor->article_id = $model->id;
                        $articleAuthor->author_id = $authorId;
                        $articleAuthor->type = 0;
                        $articleAuthor->save();
                    }
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Article model.
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
        Yii::$app->session->setFlash('success', 'Журнальная статья успешно удалена.');

        return $this->redirect(['/issue' . '/view', 'id' => $model->issue_id]);
    }

    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Article::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Страницы не существует');
    }
}
