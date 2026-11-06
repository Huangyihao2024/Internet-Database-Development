<?php
/** 课程项目文件；黄奕浩 2413581集成维护（AI辅助），原始来源见目录说明。 */

declare(strict_types=1);

namespace common\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Color mode toggle asset bundle.
 *
 * Provides the Bootstrap 5.3+ dark/light theme toggle script shared by frontend and backend.
 */
class ColorModeAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/colormode';
    public $js = [
        'js/color-mode.js',
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}
