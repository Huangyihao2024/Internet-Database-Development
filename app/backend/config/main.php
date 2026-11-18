<?php
/**
 * 绿茵数据团队：backend/config/main.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'as adminGate' => [
        'class' => \yii\filters\AccessControl::class,
        'except' => ['site/login','site/logout','site/error'],
        'rules' => [['allow'=>true,'roles'=>['@'],'matchCallback'=>static fn()=>Yii::$app->user->identity->isAdmin]],
    ],
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => \common\models\User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-worldcup-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'worldcup-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                ['class'=>\yii\log\FileTarget::class,'levels'=>['info','profile'],'categories'=>['team.*'],'logVars'=>[],'logFile'=>'@runtime/logs/team.log'],
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];
