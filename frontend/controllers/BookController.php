<?php

namespace frontend\controllers;

use common\models\Book;
use common\models\BookAuthor;
use common\models\BookSearch;
use common\models\Files;
use common\models\InventoryBookSearch;
use common\models\Logbook;
use common\models\LogbookSearch;
use TCPDF;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{
    const DIR = 'Files/books/';

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
     * Lists all Book models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchLogbook = new LogbookSearch();
        $logbookProvider = $searchLogbook->search(Yii::$app->request->queryParams, book_id: $id);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'logbookProvider' => $logbookProvider,
            'searchLogbook' => $searchLogbook
        ]);
    }

    /**
     * Инвентарная книга.
     * 
     * @throws ForbiddenHttpException если доступ закрыт
     * @return string render
     */
    public function actionInventory()
    {
        if (!Yii::$app->user->can('inventorybook/access')) {
            throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
        }
        $searchModel = new InventoryBookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (!$searchModel->filterType == 'filter2'&& !$searchModel->selectDate && !$searchModel->selectDisposition) {
            $searchModel->selectDate = Yii::$app->formatter->asDate('now', 'yyyy-MM');
            $searchModel->selectDisposition = 1;
            $searchModel->filterType = 'filter1';
            $searchModel->dateDisplay = '';
            $searchModel->codeDisplay = 'style="display: none;"';
        }

        return $this->render('inventory', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        if (!Yii::$app->user->can('book/' . $action)) {
            throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
        }
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $this->getAccess('create');

        $model = new Book();
        $model->withraw = 0;
        $model->disposition = 1;
        $model->publishyear = Yii::$app->formatter->asDate('now', 'yyyy');
        $model->recieptdate = Yii::$app->formatter->asDate('now', 'yyyy-MM-dd');

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
                $selectedAuthors = Yii::$app->request->post('Book')['bookAuthorIds'];
                if (!empty($selectedAuthors)) {
                    foreach ($selectedAuthors as $authorId) {
                        $bookAuthor = new BookAuthor();
                        $bookAuthor->book_id = $model->id;
                        $bookAuthor->author_id = $authorId;
                        $bookAuthor->type = 0;
                        $bookAuthor->save();
                    }
                }
                $selectedRedactors = Yii::$app->request->post('Book')['bookRedactorIds'];
                if (!empty($selectedRedactors)) {
                    foreach ($selectedRedactors as $authorId) {
                        $bookRedactor = new BookAuthor();
                        $bookRedactor->book_id = $model->id;
                        $bookRedactor->author_id = $authorId;
                        $bookRedactor->type = 1;
                        $bookRedactor->save();
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
     * Updates an existing Book model.
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
                $selectedAuthors = Yii::$app->request->post('Book')['bookAuthorIds'];
                $authors = BookAuthor::findAll(['book_id' => $model->id, 'type' => 0]);
                foreach ($authors as $author) {
                    $author->delete();
                }
                if (!empty($selectedAuthors)) {
                    foreach ($selectedAuthors as $authorId) {
                        $bookAuthor = new BookAuthor();
                        $bookAuthor->book_id = $model->id;
                        $bookAuthor->author_id = $authorId;
                        $bookAuthor->type = 0;
                        $bookAuthor->save();
                    }
                }

                $selectedRedactors = Yii::$app->request->post('Book')['bookRedactorIds'];
                $redactors = BookAuthor::findAll(['book_id' => $model->id, 'type' => 1]);
                foreach ($redactors as $redactor) {
                    $redactor->delete();
                }
                if (!empty($selectedRedactors)) {
                    foreach ($selectedRedactors as $redactorId) {
                        $bookRedactor = new BookAuthor();
                        $bookRedactor->book_id = $model->id;
                        $bookRedactor->author_id = $redactorId;
                        $bookRedactor->type = 1;
                        $bookRedactor->save();
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
     * Deletes an existing Book model.
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
        } elseif (Logbook::findOne(['book_id' => $id])) {
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
        $fileModel = Files::findOne($model->file_id);
        if ($fileModel) {
            unlink($fileModel->filepath);
            $fileModel->delete();
        }
        $model->delete();
        Yii::$app->session->setFlash('success', 'Книга успешно удалена.');

        return $this->redirect(['/book' . '/index']);
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Страницы не существует');
    }

    /**
     * Генерация pdf-файла для инвентарной книги
     */
    public function actionGeneratepdf()
    {
        $searchModel = new InventoryBookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $content = $this->renderPartial('_pdfView', [
            'dataProvider' => $dataProvider,
        ]);

        $mpdf = new TCPDF();

        $mpdf->SetFont('dejavusans', '', 7, '', true);
        $mpdf->AddPage();

        $mpdf->WriteHTML($content);

        $mpdf->Output("Инвентарная книга.pdf", 'I');
    }

    /**
     * Метод для печати каталожной карточки издания
     * 
     * @param int $id индекс книги
     * @return string render
     */
    public function actionEditionCard($id)
    {
        return $this->renderPartial('editionCard', [
            'edition' => $this->findModel($id)
        ]);
    }
}
