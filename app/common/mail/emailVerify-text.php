<?php
/** Yii2框架文件；世界杯项目集成维护。原作者许可见LICENSE.md。 */

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var common\models\User $user */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
Hello <?= $user->username ?>,

Follow the link below to verify your email:

<?= $verifyLink ?>
