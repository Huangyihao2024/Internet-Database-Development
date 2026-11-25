<?php
/** 管理员数据导入入口。负责人：王乐之（2411302）；AI辅助开发。 */
namespace backend\controllers;
use Yii;
use common\controllers\AdminController;
class ImportController extends AdminController {
    /** 展示解析预览及源数据警告。 */
    public function actionIndex(): string {$file=dirname(__DIR__,3).'/data/raw/worldcup_2022_parsed.json';$data=json_decode(file_get_contents($file),true);return $this->render('index',['data'=>$data]);}
    /** 仅管理员 POST 执行事务导入。 */
    public function actionRun(): \yii\web\Response {if (!Yii::$app->request->isPost)throw new \yii\web\MethodNotAllowedHttpException('仅支持POST');$controller=new \console\controllers\WorldcupImportController('worldcup-import',Yii::$app);[$code,$message]=$controller->runImport();Yii::$app->session->setFlash($code===0?'success':'error',$message);return $this->redirect(['index']);}
}
