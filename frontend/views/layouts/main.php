<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => 'Каталоги', 'url' => ['/site/index'],'items' => [
            ['label' => 'Книги', 'url' => ['book/index']],
            ['label' => 'Статистические сборники', 'url' => ['statrelease/index']],
            ['label' => 'Журналы', 'url' => ['journal/index']],
            ['label' => 'Информационные серии', 'url' => ['seria/index']],
        ]],
        //['label' => 'About', 'url' => ['/site/about']],
        //['label' => 'Contact', 'url' => ['/site/contact']],
    ];
    if (Yii::$app->user->can('manageUsers')) {
        $menuItems[1] = ['label' => 'Администрирование', 'url' => ['/site/index'], 'items' => [
            ['label' => 'Создать пользователя', 'url' => ['/user/signup']],
            ['label' => 'Управление пользователем', 'url' => ['/user/update']]
        ]];
    }
    $menuItems[2] = ['label' => 'Ещё', 'url' => ['/site/index'],'items' => [
        ['label' => 'Авторы', 'url' => ['author/index']],
        ['label' => 'Рубрики', 'url' => ['rubric/index']]
    ]];
    if (Yii::$app->user->can('inventorybook/access')) {
        $menuItems[2]['items'][] = ['label' => 'Инвентарная книга', 'url' => ['book/inventory']];
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav me-auto mb-2 mb-md-0'],
        'items' => $menuItems,
    ]);
    if (Yii::$app->user->isGuest) {
        echo Html::tag('div',Html::a('Вход',['/user/login'],['class' => ['btn btn-link login text-decoration-none']]),['class' => ['d-flex']]);
    } else {
        echo Html::beginForm(['/user/logout'], 'post', ['class' => 'd-flex'])
            . Html::submitButton(
                'Выйти (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout text-decoration-none']
            )
            . Html::endForm();

        echo Html::beginForm([''], 'post', ['class' => 'd-flex'])
            . Html::Button('Личный кабинет',[
                'class' => 'btn btn-link text-decoration-none personal-account',
                'data-bs-toggle' => "offcanvas",
                'data-bs-target' => "#offcanvasRight",
                "aria-controls" => "offcanvasRight",
                "id" => "offcanvas-click"
            ])
            . Html::endForm();

        ?>
        <?php
    }
    NavBar::end();
    ?>
</header>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
    <div id="offcanvasContent">
        <?php if (!Yii::$app->user->isGuest) {
            echo $this->render('../user/_userInfo');
        } ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <p class="float-start">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
        <p class="float-end">
            <a href="http://www.vscc.ac.ru/" target="_blank">Вологодский научный центр Российской академии наук</a>
        </p>
    </div>
</footer>

<div id="loader" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only"></span>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
?>