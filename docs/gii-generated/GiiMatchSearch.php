<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\FootballMatch;

/**
 * GiiMatchSearch represents the model behind the search form of `common\models\FootballMatch`.
 */
class GiiMatchSearch extends FootballMatch
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tournament_id', 'stage_id', 'group_id', 'stadium_id', 'match_number', 'home_team_id', 'away_team_id', 'normal_home', 'normal_away', 'extra_home', 'extra_away', 'penalty_home', 'penalty_away'], 'integer'],
            [['kicks_off_at', 'status', 'source_key', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = FootballMatch::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'tournament_id' => $this->tournament_id,
            'stage_id' => $this->stage_id,
            'group_id' => $this->group_id,
            'stadium_id' => $this->stadium_id,
            'match_number' => $this->match_number,
            'home_team_id' => $this->home_team_id,
            'away_team_id' => $this->away_team_id,
            'kicks_off_at' => $this->kicks_off_at,
            'normal_home' => $this->normal_home,
            'normal_away' => $this->normal_away,
            'extra_home' => $this->extra_home,
            'extra_away' => $this->extra_away,
            'penalty_home' => $this->penalty_home,
            'penalty_away' => $this->penalty_away,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'source_key', $this->source_key]);

        return $dataProvider;
    }
}
