<?php
/** Yii2原始框架文件；知行学习团队集成维护：黄奕浩 2413581（AI辅助）。 */

declare(strict_types=1);

return yii\helpers\ArrayHelper::merge(
    require dirname(dirname(__DIR__)) . '/common/config/codeception-local.php',
    require __DIR__ . '/main.php',
    require __DIR__ . '/main-local.php',
    require __DIR__ . '/test.php',
    require __DIR__ . '/test-local.php',
    [],
);
