const $ = id => document.getElementById(id);
let pageUrl = '', pageTitle = '';
const ready = (async () => {
  const [tab] = await chrome.tabs.query({active:true,currentWindow:true});
  pageUrl = tab?.url || ''; pageTitle = tab?.title || '';
  const {notes={}} = await chrome.storage.local.get('notes');
  $('note').value = notes[pageUrl]?.text || '';
})();
async function tick() {
  const {focusEnd=0} = await chrome.storage.local.get('focusEnd');
  const seconds = Math.max(0,Math.ceil((focusEnd-Date.now())/1000));
  $('timer').textContent = seconds ? Math.floor(seconds/60)+':'+String(seconds%60).padStart(2,'0') : '尚未开始或已结束';
}
$('start').onclick = async () => {
  const minutes = Number($('minutes').value);
  if (!Number.isInteger(minutes)||minutes<1||minutes>120) { $('status').textContent='请输入1到120之间的整数'; return; }
  const focusEnd=Date.now()+minutes*60000;
  await chrome.storage.local.set({focusEnd});
  await chrome.alarms.create('focusEnd',{when:focusEnd});
  await chrome.action.setBadgeText({text:'专注'}); await tick();
};
$('stop').onclick = async () => {await chrome.alarms.clear('focusEnd');await chrome.storage.local.set({focusEnd:0});await chrome.action.setBadgeText({text:''});await tick();};
$('save').onclick = async () => {await ready;const {notes={}}=await chrome.storage.local.get('notes');notes[pageUrl]={title:pageTitle,text:$('note').value,updatedAt:new Date().toISOString()};await chrome.storage.local.set({notes});$('status').textContent='笔记已保存';};
$('export').onclick = async () => {const {notes={}}=await chrome.storage.local.get('notes');const url=URL.createObjectURL(new Blob([JSON.stringify(notes,null,2)],{type:'application/json'}));const a=document.createElement('a');a.href=url;a.download='知行学习笔记.json';a.click();setTimeout(()=>URL.revokeObjectURL(url),1000);};
$('reading').onclick = async () => {
  try {const [tab]=await chrome.tabs.query({active:true,currentWindow:true});await chrome.scripting.executeScript({target:{tabId:tab.id},func:()=>{
    const old=document.getElementById('zhixing-reader-style');if(old){old.remove();return;}
    const style=document.createElement('style');style.id='zhixing-reader-style';style.textContent='body{max-width:1000px!important;margin:auto!important;background:#f5f0e3!important;color:#24392b!important;line-height:1.9!important}p,li{font-size:18px!important}video,iframe{display:none!important}';document.head.append(style);
  }});$('status').textContent='阅读模式已切换';}catch(e){$('status').textContent='此页面不允许注入，请在普通网页中使用';}
};
tick();setInterval(tick,1000);
