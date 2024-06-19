<?php

namespace frontend\controllers;

use common\models\Logbook;
use common\models\LogbookSearch;
use common\models\NewEmailForm;
use common\models\RoleAssignmentForm;
use Yii;
use common\models\User;
use common\models\UserSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\SignupForm;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResendVerificationEmailForm;
use yii\filters\AccessControl;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use frontend\models\ResetPasswordForm;
use frontend\models\VerifyEmailForm;
use yii\helpers\Url;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
   
    /**
     * Проверяет доступ пользователя
     * @throws ForbiddenHttpException если доступ закрыт
     */
    public function getAccess()
    {
        if (!Yii::$app->user->can('manageUsers')) {
            throw new ForbiddenHttpException('У вас нет разрешения!');
        }
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $this->getAccess();
        
        $model = new User();

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
     * Updates an existing User model.
     * 
     * @param int|null $user_id индекс пользователя
     * @return string render 
     */
    public function actionUpdate($user_id = null)
    {
        $this->getAccess();

        $formModel = new RoleAssignmentForm();
        $active_user = true;

        if ($user_id) {
            $user = $this->findModel($user_id);
            if ($user->status == 9 || $user->second_email) {
                $active_user = false;
            }
            $user_roles = Yii::$app->authManager->getRolesByUser($user_id);
            foreach ($user_roles as $user_role) {
                $formModel->role = $user_role->name;
                break;
            }
            $searchModel = new LogbookSearch();
            $dataProvider =  $searchModel->search(Yii::$app->request->queryParams, $user_id);

            return $this->renderAjax('_updateForm', [
                'formModel' => $formModel,
                'user' => $user,
                'active_user' => $active_user,
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel
            ]);
        }
        if ($formModel->load(Yii::$app->request->post()) && $formModel->assignRole()) {
            Yii::$app->session->setFlash('success', 'Роль успешно назначена');
            $formModel->user_id = null;
        }
        return $this->render('userUpdate', [
            'formModel' => $formModel,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $user_id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($user_id)
    {
        $this->getAccess();
        
        $user = User::findOne($user_id);
        if ($user) {
            if (!Logbook::findOne(['user_id' => $user_id, 'return_date' => null])) {
                $user->status = User::STATUS_DELETED;
                if ($user->save(false)) {
                    Yii::$app->session->setFlash('success', 'Пользователь успешно удален');
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка при удалении пользователя');
                }
            } else {
                Yii::$app->session->setFlash('error', 'На пользователе числятся издания!');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Пользователь не найден');
        }
        return $this->redirect(Url::to(['update']));
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Страницы не существует');
    }

    /**
     * Создание пользователя
     * 
     * @throws ForbiddenHttpException
     * @return string|yii\web\Response render|redirect on HomePage
     */
    public function actionSignup()
    {
        if (Yii::$app->user->can('manageUsers')) {
            $model = new SignupForm();
            if ($model->load(Yii::$app->request->post()) && $model->signup()) {
                Yii::$app->session->setFlash('success', 'Пользователь зарегистрирован. Требуется подтвердить почту.');
                return $this->goHome();
            }

            return $this->render('signup', [
                'model' => $model,
            ]);
        } else {
            throw new ForbiddenHttpException('У вас нет разрешения на создание новых пользователей');
        }
    }

    /**
     * Авторизация
     * 
     * @return string|yii\web\Response render|redirect on HomePage
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model_login = new LoginForm();

        if ($model_login->load(Yii::$app->request->post())) {
            if ($model_login->validateSecondEmail()) {
                if ($model_login->login()) {
                    return $this->goBack();
                }
            } else {
                $this->redirect(Url::to(['write-new-email', 'login' => $model_login->username]));
            }
        }

        $model_login->password = '';

        return $this->render('login', [
            'model' => $model_login,
        ]);
    }

    /**
     * Ввод почты для пользователей перенесенных из старой библиотеки
     * 
     * @param string $login логин пользователя
     * @return string|yii\web\Response render|redirect on HomePage
     */
    public function actionWriteNewEmail($login)
    {
        $model_email = new NewEmailForm();
        $model_email->login = $login;

        if ($model_email->load(Yii::$app->request->post()) && $model_email->setNewEmail()) {
            Yii::$app->session->setFlash('success', 'Пользователь зарегистрирован. Требуется подтвердить почту.');
            return $this->goHome();
        }

        return $this->render('newEmail', [
            'model' => $model_email
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return string|yii\web\Response render|redirect on HomePage
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Проверьте свою почту, для дальнейших действий.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Извините, но по указанной почте восстановление пароля невозможно.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if (($user = $model->verifyEmail()) && Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('Ваша почта была подтверждена!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'К сожалению, мы не можем подтвердить вашу учетную запись с помощью предоставленного токена.');
        return $this->goHome();
    }

    /**
     * Сброс пароля
     * 
     * @param string $token токен
     * @return mixed
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Новый пароль сохранен.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Отправка сообщения с подтверждением почты
     * 
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Проверьте свою почту для дальнейших инструкций.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'К сожалению, мы не можем отправить письмо с подтверждением на указанный адрес электронной почты.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
    

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        // return $this->goHome();
        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

    /**
     * Находит записи которые содержат указанную строку и ограничивает
     * количество одновременно отображаемых элементов
     * 
     * @param string $term искомая строка
     * @param int $page номер страницы
     * @param int $limit количество элементов на странице
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionList($term = null, $page = 1, $limit = 20)
    {
        if (Yii::$app->request->isAjax) {
            $out = ['more' => false, 'results' => []];
            $query = User::find()->where(['not', ['status' => 0]])
                ->andWhere(['not', ['username' => 'admin']])
                ->orderBy(['fio' => SORT_ASC]);
            $data = $query
                ->select([
                    'id' => '[[id]]',
                    'text' => '[[fio]]',
                ])
                ->andFilterWhere(['like', 'fio', $term])
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
