<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\FootballMatch $model */

$this->title = 'Create Football Match';
$this->params['breadcrumbs'][] = ['label' => 'Football Matches', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="football-match-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
