<?php

namespace frontend\controllers;

use common\models\AdvancedBookSearch;
use common\models\AdvancedJournalSearch;
use common\models\AdvancedSeriaSearch;
use common\models\AdvancedStatreleaseSearch;
use common\models\CatalogSearch;
use Yii;
use yii\web\Controller;
use frontend\models\ContactForm;
use common\models\Book;
use common\models\Issue;
use common\models\Statrelease;

/**
 * Site controller
 */
class SiteController extends Controller
{
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {   
        $books = Book::find()
        ->orderBy(['id' => SORT_DESC])      
        ->limit(10)
        ->all();

        $statreleases = Statrelease::find()
        ->orderBy(['id' => SORT_DESC])      
        ->limit(10)
        ->all();

        $issues = Issue::find()
        ->orderBy(['id' => SORT_DESC])
        ->limit(10)
        ->all();

        $searchModel = new CatalogSearch();
        $searchModel->bool_book = 1;
        $searchModel->bool_journal = 1;
        $searchModel->bool_inforelease = 1;
        $searchModel->bool_statrelease = 1;
        list($dataProvider, $countTypes) = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index',[
            'books' => $books,
            'statreleases' => $statreleases,
            'issues' => $issues,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'countTypes' => $countTypes
        ]);
    }    

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Благодарим Вас за обращение к нам. Мы ответим вам как можно скорее.');
            } else {
                Yii::$app->session->setFlash('error', 'При отправке вашего сообщения произошла ошибка.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionAdvancedSearch() {
        $books = Book::find()
        ->orderBy(['id' => SORT_DESC])->limit(10)->all();

        $statreleases = Statrelease::find()
        ->orderBy(['id' => SORT_DESC])->limit(10)->all();

        $issues = Issue::find()
        ->orderBy(['id' => SORT_DESC])->limit(10)->all();
        
        $journalSearch = new AdvancedJournalSearch();
        $journalProvider = $journalSearch->search(Yii::$app->request->queryParams);

        $seriaSearch = new AdvancedSeriaSearch();
        $seriaProvider = $seriaSearch->search(Yii::$app->request->queryParams);

        $bookSearch = new AdvancedBookSearch();
        $bookProvider = $bookSearch->search(Yii::$app->request->queryParams);

        $statreleaseSearch = new AdvancedStatreleaseSearch();
        $statreleaseProvider = $statreleaseSearch->search(Yii::$app->request->queryParams);

        return $this->render('advanced_search.php', [
            'books' => $books,
            'statreleases' => $statreleases,
            'issues' => $issues,

            'journal' => $journalProvider,
            'journalSearch' => $journalSearch,
            'seria' => $seriaProvider,
            'seriaSearch' => $seriaSearch,
            'book' => $bookProvider,
            'bookSearch' => $bookSearch,
            'statrelease' => $statreleaseProvider,
            'statreleaseSearch' => $statreleaseSearch,
        ]);
    }
}
