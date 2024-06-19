<?php

namespace backend\controllers;

use common\models\LoginForm;
use common\models\NewEmailForm;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
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
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string|Response
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
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
