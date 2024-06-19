<?php

namespace frontend\controllers;

use common\models\Cart;
use common\models\CartEditions;
use common\models\Logbook;
use common\models\User;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * CartController implements the CRUD actions for Cart model.
 */
class CartController extends Controller
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
     * Проверяет доступ пользователя
     * 
     * @param string $action тип действия
     * @param int $user_id индекс пользователя
     * @throws ForbiddenHttpException если доступ закрыт
     */
    public function getAccess($action, $user_id = 0)
    {
        if ($user_id === Yii::$app->user->identity->id || Yii::$app->user->can('cart/' . $action)) {
            return true;
        }
        throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
    }

    /**
     * Creates a new Cart model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @param string $name наименование для подборки
     * @return array
     */
    public function actionCreate($name)
    {
        $this->getAccess('create');

        $user_id = Yii::$app->user->identity->id;
        $model = new Cart();
        $model->user_id = $user_id;
        $model->name = $name;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($model->save()) return ['success' => true];
        else return ['success' => true];
    }

    /**
     * Updates an existing Cart model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        // TODO: возможность переименовать подборку?
        $this->getAccess('update', Yii::$app->user->identity->id);

        $model = $this->findModel($id);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Cart model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $user = Yii::$app->user->identity;
        $this->getAccess('delete', $user->id);

        $model = $this->findModel($id);
        $model->delete();

        // Возвращаем HTML-код для обновления offcanvas
        return $this->renderPartial('/cart/_cartList', [
            'user' => $user,
            'carts' => Cart::findAll(['user_id' => $user->id]),
        ]);
    }

    /**
     * Проверяет доступ пользователя к взаимодействию с корзиной
     * 
     * @param int $cart_id индекс подборки
     * @throws ForbiddenHttpException если доступ закрыт
     */
    public function getAccessCart($cart_id)
    {
        $model = Cart::findOne($cart_id);
        if ($model->user_id === Yii::$app->user->identity->id) {
            return true;
        }
        throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
    }

    /**
     * Добавление издания в подборку для корзины пользователя
     * 
     * @param int $cart_id индекс подборки
     * @param string $model_type тип издания
     * @param int $model_id индекс издания
     * 
     * @return yii\web\Response redirect
     */
    public function actionAdd($cart_id, $model_type, $model_id)
    {
        $this->getAccessCart($cart_id);
        $model = new CartEditions();
        $model->cart_id = $cart_id;
        $model->{$model_type} = $model_id;
        $model->save();

        // Обновляем страницу
        Yii::$app->session->setFlash('success', 'Издание успешно добавлено в корзину.');
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Массовое добавление изданий в корзину
     * 
     * @param int $cart_id индекс выбранной подборки
     * 
     * @return array[bool] Обновляет содержимое страницы
     */
    public function actionBulkAdd($cart_id)
    {
        $this->getAccessCart($cart_id);

        $selectedItems = Yii::$app->request->post('selection');
        if ($selectedItems) {
            foreach($selectedItems as $item) {
                $model = new CartEditions();
                $model->cart_id = $cart_id;
                $model->{$item[0]} = $item[1];
                $model->save();
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true];
        } else {
            return ['success' => false];
        }
    }

    /**
     * Удаление выбранных изданий
     * 
     * @param int $cart_id индекс подборки
     * @return bool|bool[] обновляет содержимое страницы при успехе
     */
    public function actionDeleteSelected($cart_id)
    {
        $this->getAccessCart($cart_id);

        $selectedIds = Yii::$app->request->post('selection');
        if ($selectedIds) {
            CartEditions::deleteAll(['id' => $selectedIds]);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true];
        } else {
            return ['success' => false];
        }
    }

    /**
     * Генерация pdf-файла для подборки
     * 
     * @param int $id индекс подборки
     * @param bool $print формирование doc-файла
     */
    public function actionPrintEditions($id, $print = false)
    {
        $model = $this->findModel($id);
        if ($print) {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_RAW;
            $response->headers->add('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            $response->headers->add('Content-Disposition', 'attachment; filename="Библиографический список.doc"');
        }
        return $this->renderPartial('bibliography',['model' => $model, 'print' => $print]);
    }

    /**
     * Finds the Cart model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param int $id ID
     * @return Cart the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Cart::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Страницы не существует');
    }

    /**
     * Находит корзину для указанного пользователя
     * 
     * @param int $user_id индекс пользователя
     * @throws NotFoundHttpException|ForbiddenHttpException не найден пользователь|доступ ограничен
     * @return string renderAjax
     */
    public function actionGetCartList($user_id)
    {
        if (!Yii::$app->user->can('logbook/access')) {
            throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
        }
        $user = User::findOne(['id' => $user_id]);
        if ($user) {
            $carts = Cart::findAll(['user_id' => $user->id]);
            $logbooks = Logbook::find()->where(['user_id' => $user->id, 'return_date' => null]);
            return $this->renderAjax('../cart/_selectedUser', [
                'user' => $user,
                'carts' => $carts,
                'logbooks' => $logbooks
            ]);
        } else {
            throw new NotFoundHttpException('Пользователь не найден.');
        }
    }
}
