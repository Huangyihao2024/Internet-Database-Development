<?php
/** 世界杯公共布局静态资源；本人负责，AI辅助编写。 */
namespace frontend\assets;
class WorldcupAsset extends \yii\web\AssetBundle {
 public $basePath='@webroot';public $baseUrl='@web';public $css=['css/worldcup.css','css/worldcup-module.css'];public $depends=[AppAsset::class];
}
