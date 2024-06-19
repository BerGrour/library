<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\RoleAssignmentForm $formModel */
/** @var common\models\User $user */
/** @var bool $active_user */
?>

<?php if (!$active_user) { ?>
    <div class="alert-danger alert alert-dismissible" role="alert">
        Пользователь не подтвердил почту!<br>
        Для аквивации аккаунта требуется зайти под логином <strong><?= $user->username ?></strong> с паролем <strong>12345678</strong>.<br>
        Затем появиться окно с вводом почты, на которую будет отправлено письмо с ссылкой.<br>
        После подтверждения почты, при повторной авторизации можно будет сменить пароль.
    </div>
<?php } ?>

<?php $form = ActiveForm::begin(['id' => 'update-user-selected']); ?>    

    <?= $form->field($formModel, 'role')->radioList([
        'librarian' => 'Библиотекарь',
        'reader' => 'Читатель',
    ]); ?>

    <div class="accordion user-history-logbook" id="accordion-user-history">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                История формуляра
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordion-user-history">
                <div class="accordion-body">
                    <?php if($dataProvider->totalCount > 0) { ?>
                        <?php Pjax::begin(['enablePushState' => false]); ?>

                            <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'tableOptions' => ['class' => 'table'],
                            'columns' => [
                                    [
                                        'class' => 'yii\grid\SerialColumn',
                                        'headerOptions' => ['class' => 'grid_column-serial']
                                    ],
                                    [
                                        'label' => 'Издание',
                                        'format' => 'raw',
                                        'value' => function($model) {
                                            return $model->getEditionInfo(true, true, true, false, '_blank');
                                        }
                                    ],
                                    [
                                        'attribute' => 'given_date',
                                        'format' => ['datetime', 'php:d.m.Y H:i'],
                                        'headerOptions' => ['style' => 'width:12%']
                                    ],
                                    [
                                        'attribute' => 'return_date',
                                        'format' => 'raw',
                                        'value' => function($model) {
                                            if ($model->return_date) {
                                                return Yii::$app->formatter->asDatetime(
                                                    $model->return_date,
                                                    'php:d.m.Y H:i'
                                                );
                                            }
                                            return '<div style="color:red">На руках<div>';
                                        },
                                        'headerOptions' => ['style' => 'width:12%']
                                    ]
                                ],
                                'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> изданий',
                            ]);?>

                        <?php Pjax::end(); ?>
                    <?php } else { ?>
                        <p>Формуляр пользователя пуст!</p>
                    <?php } ?>            
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-success',
            'form' => 'update-user-search'
        ]); ?>

        <?= Html::a('Удалить', ['delete', 'user_id' => $user->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить этого пользователя?',
                'method' => 'post',
            ],
        ]); ?>
    </div>

<?php ActiveForm::end(); ?>
