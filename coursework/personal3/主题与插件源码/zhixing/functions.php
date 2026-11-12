<?php
/** 课程项目文件；黄奕浩 2413581集成维护（AI辅助），原始来源见目录说明。 */
add_action('wp_enqueue_scripts',function(){wp_enqueue_style('zhixing',get_stylesheet_uri(),array(),'1.0.0');});
add_theme_support('title-tag');
add_theme_support('post-thumbnails');
register_nav_menus(array('primary'=>'主导航'));
