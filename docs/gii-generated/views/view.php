<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\FootballMatch $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Football Matches', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="football-match-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tournament_id',
            'stage_id',
            'group_id',
            'stadium_id',
            'match_number',
            'home_team_id',
            'away_team_id',
            'kicks_off_at',
            'normal_home',
            'normal_away',
            'extra_home',
            'extra_away',
            'penalty_home',
            'penalty_away',
            'status',
            'source_key',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
