<?php
/** Yii2框架文件；世界杯项目集成维护。原作者许可见LICENSE.md。 */

declare(strict_types=1);

namespace common\fixtures;

use common\models\User;
use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = User::class;
}
