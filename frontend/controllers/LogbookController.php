<?php

namespace frontend\controllers;

use common\models\Article;
use common\models\Book;
use common\models\CartEditions;
use common\models\Issue;
use common\models\Logbook;
use common\models\Statrelease;
use common\models\User;
use TCPDF;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class LogbookController extends \yii\web\Controller
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
     * @throws ForbiddenHttpException если доступ закрыт
     */
    public function getAccess($action)
    {
        if (!Yii::$app->user->can('logbook/' . $action)) {
            throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
        }
    }

    /**
     * Выдача издания на руки
     * 
     * @param int $user_id индекс пользователя
     * @param string $model_type имя поля к которому принадлежит выбранное издание
     * @param int $model_id индекс выбранного издания
     * 
     * @throws NotFoundHttpException если пользователь не найден
     * 
     * @return yii\web\Response redirect
     */
    public function actionAdd($user_id, $model_type, $model_id)
    {
        $this->getAccess('give');

        if ($user_id) {
            $validate = $this->ValidateReservatedEdition($model_type, $model_id);
            if ($validate) {
                $model = new Logbook();
                $model->user_id = $user_id;
                $model->{$model_type} = $model_id;
                $model->given_date = time();
                $model->save();
            } else {
                Yii::$app->session->setFlash('error', 'Издание занято или списано.');
                return $this->redirect(Yii::$app->request->referrer);
            }
        } else {
            throw new NotFoundHttpException('Пользователь не найден.');
        }
        Yii::$app->session->setFlash('success', 'Издание успешно выдано.');
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Массовая выдача изданий на руки
     * 
     * @param int $user_id индекс пользователя
     * 
     * @throws NotFoundHttpException если пользователь не найден
     * 
     * @return array[bool] Обновляет содержимое страницы
     */
    public function actionBulkAdd($user_id)
    {
        $this->getAccess('give');

        if ($user_id) {
            $selectedItems = Yii::$app->request->post('selection');
            if ($selectedItems) {
                foreach ($selectedItems as $item) {
                    if ($item[0] != "infoarticle_id") {
                        $type = $item[0];
                        $model_id = $item[1];

                        if ($item[0] == "article_id") {
                            $type = 'issue_id';
                            $model_id = Article::findOne(['id' => $item[1]])->issue_id;
                        }
                        $validate = $this->ValidateReservatedEdition($type, $model_id);
                        if ($validate) {
                            $model = new Logbook();
                            $model->user_id = $user_id;
                            $model->{$type} = $model_id;
                            $model->given_date = time();
                            $model->save();
                        } 
                    }
                }
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => true];
            } else {
                return ['success' => false];
            }
        } else {
            throw new NotFoundHttpException('Пользователь не найден.');
        }
    }

    /**
     * Проверяет не занято ли это издание другим пользователем или списано
     * 
     * @param string $type строковое имя поля
     * @param int $cartEdiotion_id индекс записи издания в корзине
     * @return bool Можно выдавать издание или нет
     */
    public function ValidateReservatedEdition($type, $id)
    {
        switch ($type) {
            case "book_id":
                $model = Book::findOne(['id' => $id]);
                break;
            case "issue_id":
                $model = Issue::findOne(['id' => $id]);
                break;
            case "statrelease_id":
                $model = Statrelease::findOne(['id' => $id]);
                break;
        }
        if ($model->isBorrowed() || $model->withraw) return false;
        return true;
    }

    /**
     * Выдача издания из корзины
     * 
     * @param int $user_id индекс пользователя
     * @return array[bool] Обновляет содержимое страницы
     */
    public function actionGiveFromCart($user_id)
    {
        $this->getAccess('give');

        if ($user_id) {
            $selectedIds = Yii::$app->request->post('selection');
            if ($selectedIds) {
                foreach ($selectedIds as $cartEdition_id) {
                    $cartEdition = CartEditions::findOne(['id' => $cartEdition_id]);
                    if (!$cartEdition->infoarticle_id) {
                        if ($cartEdition->book_id) {
                            $type = 'book_id';
                            $id = $cartEdition->book_id;
                        } elseif ($cartEdition->article_id) {
                            $type = 'issue_id';
                            $id = $cartEdition->article->issue_id;
                        } elseif ($cartEdition->issue_id) {
                            $type = 'issue_id';
                            $id = $cartEdition->issue_id;
                        } elseif ($cartEdition->statrelease_id) {
                            $type = 'statrelease_id';
                            $id = $cartEdition->statrelease_id;
                        } else {
                            throw new NotFoundHttpException('Ошибка в проверке корзины');
                        }
                        $validate = $this->ValidateReservatedEdition($type, $id);
    
                        if ($validate) {
                            $model = new Logbook();
                            $model->user_id = $user_id;
                            $model->book_id = $cartEdition->book_id;
                            $model->issue_id = $cartEdition->issue_id;
                            $model->statrelease_id = $cartEdition->statrelease_id;
                            if ($cartEdition->article_id) {
                                $model->issue_id = $cartEdition->article->issue->id;
                            }
                            $model->given_date = time();
                            $model->save();
                        }
                    }
                }
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => true];
            } else {
                return ['success' => false];
            }
        } else {
            throw new NotFoundHttpException('Пользователь не найден.');
        }
    }

    /**
     * Возврат издания из формуляра
     * 
     * @return array[bool] Обновляет содержимое страницы
     */
    public function actionReturnEditions()
    {
        $this->getAccess('return');

        $selectedIds = Yii::$app->request->post('selection');
        if ($selectedIds) {
            foreach ($selectedIds as $logbook_id) {
                $logbook = Logbook::findOne([
                    'id' => $logbook_id,
                    'return_date' => null
                ]);
                $logbook->return_date = time();
                $logbook->save();
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true];
        } else {
            return ['success' => false];
        }
    }

    /**
     * Генерация pdf-файла для формуляра пользователя
     * 
     * @param int $user_id индекс пользователя
     */
    public function actionPrintEditions($user_id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Logbook::find()
                ->where(['user_id' => $user_id, 'return_date' => null]),
            'pagination' => false
        ]);
        $user = User::findOne($user_id);

        $content = $this->renderPartial('pdfUserLogbook', [
            'dataProvider' => $dataProvider,
            'user' => $user
        ]);
        $mpdf = new TCPDF();

        $mpdf->SetFont('dejavusans', '', 7, '', true);
        $mpdf->AddPage();

        $mpdf->WriteHTML($content);

        $mpdf->Output("Формуляр пользователя.pdf", 'I');
    }

    public function actionChangeOwner($new_user)
    {
        $this->getAccess('give');
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($new_user) {
            $selectedIds = Yii::$app->request->post('selection');
            if ($selectedIds) {
                foreach ($selectedIds as $item_id) {
                    $item = Logbook::findOne(['id' => $item_id]);
                    if (empty($item->return_date)) {
                        $item->return_date = time();
                        if ($item->save()) {
                            $model = new Logbook();
                            $model->user_id = $new_user;
                            $model->book_id = $item->book_id;
                            $model->issue_id = $item->issue_id;
                            $model->statrelease_id = $item->statrelease_id;
                            $model->given_date = time();
                            if (!$model->save()) {
                                Yii::$app->session->setFlash('error', 'Некоторые издания не были переданы.');
                            }
                        }
                    } 
                }
                return ['success' => true];
            }
            return ['success' => false];
        } else {
            throw new NotFoundHttpException('Пользователь не найден.');
        }
    }
}
