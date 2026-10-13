<?php
/** 赛事数据后台维护。负责人：王乐之（2411302）；AI辅助开发。 */
namespace backend\controllers;
use Yii;
use common\controllers\AdminController;
use common\models\{Tournament,Stage,TournamentGroup,Stadium,NationalTeam,Player,Squad,FootballMatch,MatchEvent};
use yii\web\{BadRequestHttpException,NotFoundHttpException};
class DataController extends AdminController {
    private const MODELS=['tournament'=>Tournament::class,'stage'=>Stage::class,'group'=>TournamentGroup::class,'stadium'=>Stadium::class,'team'=>NationalTeam::class,'player'=>Player::class,'squad'=>Squad::class,'match'=>FootballMatch::class,'event'=>MatchEvent::class];
    /** 取得白名单中的赛事实体，避免任意表操作。 */
    private function model(string $type): string {if (!isset(self::MODELS[$type]))throw new BadRequestHttpException('维护类型无效');return self::MODELS[$type];}
    /** 分页展示九类赛事表。 */
    public function actionIndex(string $type='match'): string {$class=$this->model($type);$q=$class::find();$pages=new \yii\data\Pagination(['totalCount'=>(clone $q)->count(),'pageSize'=>20]);return $this->render('index',['types'=>array_keys(self::MODELS),'type'=>$type,'rows'=>$q->orderBy(['id'=>SORT_DESC])->offset($pages->offset)->limit($pages->limit)->all(),'pages'=>$pages]);}
    /** 创建或编辑，使用模型 Rules 和数据库约束共同校验。 */
    public function actionEdit(string $type,int $id=0): string|\yii\web\Response {$class=$this->model($type);$model=$id?$class::findOne($id):new $class();if (!$model)throw new NotFoundHttpException('记录不存在');$fields=$this->fields($model);if (Yii::$app->request->isPost) {$data=Yii::$app->request->post('fields',[]);foreach ($fields as $field=>$meta) if (array_key_exists($field,$data))$model->$field=$data[$field]===''?null:$data[$field];if ($model->validate())try {$model->save(false);Yii::$app->session->setFlash('success','记录已保存');return $this->redirect(['index','type'=>$type]);}catch (\Throwable $e){$model->addError('id','数据库约束不允许保存：'.$e->getMessage());}}return $this->render('edit',['model'=>$model,'type'=>$type,'fields'=>$fields]);}
    /** 删除无关联记录，外键冲突时反馈错误。 */
    public function actionDelete(string $type,int $id): \yii\web\Response {$class=$this->model($type);$model=$class::findOne($id);if (!$model)throw new NotFoundHttpException('记录不存在');try {$model->delete();Yii::$app->session->setFlash('success','记录已删除');}catch (\Throwable $e){Yii::$app->session->setFlash('error','存在关联记录，无法删除');}return $this->redirect(['index','type'=>$type]);}
    /** 从现有 SQL 结构读取可编辑字段。 */
    private function fields($model): array {$result=[];foreach ($model::getTableSchema()->columns as $name=>$column)if (!in_array($name,['id','created_at','updated_at'],true))$result[$name]=['required'=>!$column->allowNull && $column->defaultValue===null,'type'=>$column->type];return $result;}
}
