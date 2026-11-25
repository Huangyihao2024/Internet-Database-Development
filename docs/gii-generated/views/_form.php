<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\FootballMatch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="football-match-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'tournament_id')->textInput() ?>

    <?= $form->field($model, 'stage_id')->textInput() ?>

    <?= $form->field($model, 'group_id')->textInput() ?>

    <?= $form->field($model, 'stadium_id')->textInput() ?>

    <?= $form->field($model, 'match_number')->textInput() ?>

    <?= $form->field($model, 'home_team_id')->textInput() ?>

    <?= $form->field($model, 'away_team_id')->textInput() ?>

    <?= $form->field($model, 'kicks_off_at')->textInput() ?>

    <?= $form->field($model, 'normal_home')->textInput() ?>

    <?= $form->field($model, 'normal_away')->textInput() ?>

    <?= $form->field($model, 'extra_home')->textInput() ?>

    <?= $form->field($model, 'extra_away')->textInput() ?>

    <?= $form->field($model, 'penalty_home')->textInput() ?>

    <?= $form->field($model, 'penalty_away')->textInput() ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
