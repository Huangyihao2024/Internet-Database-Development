<?php
/** Yii2原始框架文件；知行学习团队集成维护：黄奕浩 2413581（AI辅助）。 */

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var common\models\User $user */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
Hello <?= $user->username ?>,

Follow the link below to reset your password:

<?= $resetLink ?>
