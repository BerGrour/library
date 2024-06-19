<?php

namespace frontend\controllers;

use common\models\ArticleSearch;
use common\models\Author;
use common\models\AuthorSearch;
use common\models\BookSearch;
use common\models\InfoarticleSearch;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * AuthorController implements the CRUD actions for Author model.
 */
class AuthorController extends Controller
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
     * Lists all Author models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AuthorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Author model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $bookSearchModel = new BookSearch();
        $bookProvider = $bookSearchModel->search(Yii::$app->request->queryParams, author_id: $id);

        $articleSearchModel = new ArticleSearch();
        $articleProvider = $articleSearchModel->search(Yii::$app->request->queryParams, author_id: $id);

        $infoarticleSearchModel = new InfoarticleSearch();
        $infoarticleProvider = $infoarticleSearchModel->search(Yii::$app->request->queryParams, author_id: $id);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'books' => $bookProvider,
            'bookSearchModel' => $bookSearchModel,
            'articles' => $articleProvider,
            'articleSearchModel' => $articleSearchModel,
            'infoarticles' => $infoarticleProvider,
            'infoarticleSearchModel' => $infoarticleSearchModel,
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
        if (!Yii::$app->user->can('author/' . $action)) {
            throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
        }
    }

    /**
     * Creates a new Author model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $this->getAccess('create');

        $model = new Author();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $author_exist = Author::findOne([
                    'surname' => $model->surname,
                    'name' => $model->name,
                    'middlename' => $model->middlename
                ]);
                if ($author_exist) {
                    $model->addError('middlename', 'Такой автор уже существует');
                } elseif ($model->save()) {
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
     * Updates an existing Author model.
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
     * Deletes an existing Author model.
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
        Yii::$app->session->setFlash('success', 'Автор успешно удален.');

        return $this->redirect(['/author' . '/index']);
    }

    /**
     * Finds the Author model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id ID
     * @return Author the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Author::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Страницы не существует');
    }

    /**
     * Находит записи которые содержат указанную строку и ограничивает
     * количество одновременно отображаемых элементов
     * 
     * @param string $term искомая строка
     * @param string $page номер страницы
     * @param string $limit количество элементов на странице
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionList($term = null, $page = 1, $limit = 20)
    {
        if (Yii::$app->request->isAjax) {
            $out = ['more' => false, 'results' => []];
            $query = Author::find();
            $data = $query
                ->select([
                    'id' => '[[id]]',
                    'text' => 'CONCAT([[surname]], " ", [[name]], ". ", [[middlename]], ".")',
                ])
                ->andFilterWhere(['like', 'CONCAT([[surname]], " ", [[name]], " ", [[middlename]], "")', $term])
                ->orFilterWhere(['like', 'CONCAT([[surname]], " ", [[name]], ". ", [[middlename]], ".")', $term])
                ->orderBy(['surname' => SORT_ASC, 'name' => SORT_ASC, 'middlename' => SORT_ASC])
                ->groupBy('id')
                ->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->asArray()
                ->all();
            if (count($data) === $limit + 1) {
                $out['more'] = true;
                array_pop($data);
            }
            $out['results'] = $data;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $out;
        }
        throw new ForbiddenHttpException;
    }
}
