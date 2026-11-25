<?php

use common\models\FootballMatch;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\GiiMatchSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Football Matches';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="football-match-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Football Match', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'tournament_id',
            'stage_id',
            'group_id',
            'stadium_id',
            //'match_number',
            //'home_team_id',
            //'away_team_id',
            //'kicks_off_at',
            //'normal_home',
            //'normal_away',
            //'extra_home',
            //'extra_away',
            //'penalty_home',
            //'penalty_away',
            //'status',
            //'source_key',
            //'created_at',
            //'updated_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, FootballMatch $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
