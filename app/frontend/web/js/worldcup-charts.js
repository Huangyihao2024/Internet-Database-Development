/* 世界杯动态图表。负责人：王乐之（2411302）；AI辅助开发。 */
(() => {
  const form = document.querySelector('#wc-chart-filter');
  if (!form) return;
  const status = document.querySelector('#wc-chart-status');
  const make = (tag, attrs = {}, content = '') => {
    const node = document.createElementNS('http://www.w3.org/2000/svg', tag);
    for (const [key, value] of Object.entries(attrs)) node.setAttribute(key, value);
    node.textContent = content;
    return node;
  };
  const drawBars = (id, rows, field) => {
    const box = document.getElementById(id); box.replaceChildren();const unit=id==='wc-teams'?'支球队':id==='wc-stages'?'个阶段':'名球员';document.getElementById(id+'-summary').textContent=(id==='wc-players'?'前':'')+rows.length+unit+(rows.length>9?'，可滚动查看全部':'');
    if (!rows.length) { box.textContent = '当前筛选无数据'; return; }
    const width = Math.max(200, box.clientWidth), left = width < 400 ? 128 : 168, barWidth = width-left-40, height = Math.max(180, rows.length * 32 + 20), max = Math.max(1, ...rows.map(r => Number(r[field])));
    const svg = make('svg', {viewBox:`0 0 ${width} ${height}`, role:'img', 'aria-label': '数据库统计柱状图'});
    rows.forEach((row, index) => {
      const y = 10 + index * 32, value = Number(row[field]);
      const label=make('text',{x:2,y:y+16,'font-size':14,fill:'#183648'},row.name.length>Math.floor((left-12)/7.5)?row.name.slice(0,Math.floor((left-12)/7.5)-1)+'…':row.name);label.append(make('title',{},row.name));svg.append(label);
      svg.append(make('rect',{x:left,y,width:Math.round(value/max*barWidth),height:20,rx:3,fill:'#168765'}));
      svg.append(make('text',{x:Math.min(width-25,left+5+Math.round(value/max*barWidth)),y:y+16,'font-size':14,fill:'#183648'},String(value)));
    });box.append(svg);
  };
  const drawTrend = rows => {
    const box = document.getElementById('wc-trend');box.replaceChildren();document.getElementById('wc-trend-summary').textContent=rows.length+'场比赛';
    if (!rows.length) {box.textContent='当前筛选无数据';return;}
    const width=Math.max(200,box.clientWidth),height=240,plotWidth=width-54,max=Math.max(1,...rows.map(r=>Number(r.goals)));
    const svg=make('svg',{viewBox:`0 0 ${width} ${height}`,role:'img','aria-label':'比赛进球趋势折线图'});
    const points=rows.map((r,i)=>`${32+i*plotWidth/Math.max(1,rows.length-1)},${190-Number(r.goals)*155/max}`).join(' ');
    for(const tick of new Set([0,Math.ceil(max/2),max])){const y=190-tick*155/max;svg.append(make('line',{x1:30,y1:y,x2:width-18,y2:y,stroke:'#dce7e1'}));svg.append(make('text',{x:4,y:y+5,'font-size':14,fill:'#345364'},String(tick)));}svg.append(make('text',{x:32,y:222,'font-size':14,fill:'#345364'},rows[0].date));if(rows.length>1)svg.append(make('text',{x:width-18,y:222,'text-anchor':'end','font-size':14,fill:'#345364'},rows[rows.length-1].date));
    svg.append(make('polyline',{points,fill:'none',stroke:'#168765','stroke-width':3}));
    rows.forEach((r,i)=>{const x=32+i*plotWidth/Math.max(1,rows.length-1),y=190-Number(r.goals)*155/max;const dot=make('circle',{cx:x,cy:y,r:4,fill:'#123341'});dot.append(make('title',{},`第${r.match_number}场 ${r.date}：${r.goals}球`));svg.append(dot);});
    box.append(svg);
  };
  let activeRequest, revision=0, latestData;
  const draw=data=>{drawBars('wc-teams',data.teams,'goals');drawTrend(data.trend);drawBars('wc-stages',data.stages,'goals');drawBars('wc-players',data.players,'goals');};
  const refresh = async () => {
    activeRequest?.abort();activeRequest=new AbortController();const sequence=++revision;
    status.removeAttribute('data-error');
    status.textContent='正在读取数据库…';
    try {
      const query=new URLSearchParams(new FormData(form));
      const response=await fetch(form.dataset.url+'?'+query.toString(),{headers:{Accept:'application/json'},signal:activeRequest.signal});
      if (!response.ok) throw new Error(`请求失败（HTTP ${response.status}）`);
      const data=await response.json();
      if(sequence!==revision)return;latestData=data;draw(data);
      status.removeAttribute('data-error');status.textContent=data.scope;
    } catch (error) {if(error.name==='AbortError'||sequence!==revision)return;status.textContent=error.message;status.setAttribute('data-error','true');for(const id of ['wc-teams','wc-trend','wc-stages','wc-players']){document.getElementById(id).textContent='数据加载失败，请重试';document.getElementById(id+'-summary').textContent='';}latestData=null;}
  };
  form.addEventListener('submit',e=>{e.preventDefault();refresh();});
  form.addEventListener('change',refresh);
  let resizeTimer;window.addEventListener('resize',()=>{clearTimeout(resizeTimer);resizeTimer=setTimeout(()=>{if(latestData)draw(latestData);},120);});
  refresh();
})();
