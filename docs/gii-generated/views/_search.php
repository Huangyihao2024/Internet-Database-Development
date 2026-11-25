<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\GiiMatchSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="football-match-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'tournament_id') ?>

    <?= $form->field($model, 'stage_id') ?>

    <?= $form->field($model, 'group_id') ?>

    <?= $form->field($model, 'stadium_id') ?>

    <?php // echo $form->field($model, 'match_number') ?>

    <?php // echo $form->field($model, 'home_team_id') ?>

    <?php // echo $form->field($model, 'away_team_id') ?>

    <?php // echo $form->field($model, 'kicks_off_at') ?>

    <?php // echo $form->field($model, 'normal_home') ?>

    <?php // echo $form->field($model, 'normal_away') ?>

    <?php // echo $form->field($model, 'extra_home') ?>

    <?php // echo $form->field($model, 'extra_away') ?>

    <?php // echo $form->field($model, 'penalty_home') ?>

    <?php // echo $form->field($model, 'penalty_away') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'source_key') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
