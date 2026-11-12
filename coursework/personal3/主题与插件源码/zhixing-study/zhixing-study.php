<?php
/*
Plugin Name: 知行学习计划
Description: 使用短代码展示学习计划并通过浏览器本地存储保存勾选状态。
Version: 1.0.0
Author: 黄奕浩
*/
add_shortcode('zhixing_plan', function(){
 ob_start(); ?>
 <section class="panel"><h2>本周学习计划</h2><p>勾选状态保存在当前浏览器中，不会同步到其他设备。</p><div id="zx-plan">
 <?php foreach(array('完成 GET 与 POST 请求分析','完成五项 jQuery 交互练习','整理页面设计与模板选择','完成 WordPress 建站记录') as $i=>$task): ?>
 <label><input type="checkbox" data-task="<?php echo esc_attr($i); ?>"> <?php echo esc_html($task); ?></label>
 <?php endforeach; ?></div><p id="zx-progress" role="status"></p></section>
 <script>(function(){const boxes=[...document.querySelectorAll('#zx-plan input')];let state={};try{state=JSON.parse(localStorage.getItem('zx-plan')||'{}')}catch(e){}function show(){document.getElementById('zx-progress').textContent='已完成 '+boxes.filter(x=>x.checked).length+' / '+boxes.length+' 项'}boxes.forEach(x=>{x.checked=!!state[x.dataset.task];x.addEventListener('change',()=>{state[x.dataset.task]=x.checked;try{localStorage.setItem('zx-plan',JSON.stringify(state))}catch(e){}show()})});show()})();</script>
 <?php return ob_get_clean();
});
