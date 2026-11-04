<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\FootballMatch $model */

$this->title = 'Update Football Match: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Football Matches', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="football-match-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
