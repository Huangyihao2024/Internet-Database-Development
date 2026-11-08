<?php
/**
 * 绿茵数据团队：common/config/main.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */

declare(strict_types=1);

return [
    'name'=>'绿茵数据团队',
    'language'=>'zh-CN',
    'timeZone'=>'Asia/Shanghai',
    'bootstrap' => [
        \common\bootstrap\MailerBootstrap::class,
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'on beforeRequest'=>static function(){ if (!(Yii::$app instanceof \yii\web\Application)) return; $lang=Yii::$app->request->get('lang'); if (in_array($lang,['zh-CN','en-US'],true)) Yii::$app->session->set('language',$lang); Yii::$app->language=Yii::$app->session->get('language','zh-CN'); },
    'components' => [
        'i18n'=>['translations'=>['app'=>['class'=>\yii\i18n\PhpMessageSource::class,'basePath'=>'@common/messages','sourceLanguage'=>'zh-CN']]],
        'urlManager'=>['class'=>\common\components\CompatibleUrlManager::class],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
    ],
];
