<?php

/** @var yii\web\View $this */
/** @var common\models\User $user */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['user/verify-email', 'token' => $user->verification_token]);
?>
Здравствуйте, <?= $user->fio ?>,

Перейдите по ссылке ниже, чтобы подтвердить свою почту:

<?= $verifyLink ?>
