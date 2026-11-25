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
    const box = document.getElementById(id); box.replaceChildren();
    if (!rows.length) { box.textContent = '当前筛选无数据'; return; }
    const width = 700, height = Math.max(180, rows.length * 29 + 28), max = Math.max(1, ...rows.map(r => Number(r[field])));
    const svg = make('svg', {viewBox:`0 0 ${width} ${height}`, role:'img', 'aria-label': '数据库统计柱状图'});
    rows.forEach((row, index) => {
      const y = 12 + index * 29, value = Number(row[field]);
      svg.append(make('text',{x:2,y:y+16,'font-size':12,fill:'#183648'},row.name));
      svg.append(make('rect',{x:170,y,width:Math.round(value/max*475),height:20,rx:3,fill:'#168765'}));
      svg.append(make('text',{x:Math.min(652,175+Math.round(value/max*475)),y:y+16,'font-size':12,fill:'#183648'},String(value)));
    });box.append(svg);
  };
  const drawTrend = rows => {
    const box = document.getElementById('wc-trend');box.replaceChildren();
    if (!rows.length) {box.textContent='当前筛选无数据';return;}
    const width=700,height=210,max=Math.max(1,...rows.map(r=>Number(r.goals)));
    const svg=make('svg',{viewBox:`0 0 ${width} ${height}`,role:'img','aria-label':'比赛进球趋势折线图'});
    const points=rows.map((r,i)=>`${20+i*660/Math.max(1,rows.length-1)},${175-Number(r.goals)*140/max}`).join(' ');
    svg.append(make('polyline',{points,fill:'none',stroke:'#168765','stroke-width':3}));
    rows.forEach((r,i)=>{const x=20+i*660/Math.max(1,rows.length-1),y=175-Number(r.goals)*140/max;const dot=make('circle',{cx:x,cy:y,r:4,fill:'#123341'});dot.append(make('title',{},`第${r.match_number}场 ${r.date}：${r.goals}球`));svg.append(dot);});
    box.append(svg);
  };
  const refresh = async () => {
    status.textContent='正在读取数据库…';
    try {
      const query=new URLSearchParams(new FormData(form));
      const response=await fetch(form.dataset.url+'?'+query.toString(),{headers:{Accept:'application/json'}});
      if (!response.ok) throw new Error(`请求失败（HTTP ${response.status}）`);
      const data=await response.json();
      drawBars('wc-teams',data.teams,'goals');drawTrend(data.trend);drawBars('wc-stages',data.stages,'goals');drawBars('wc-players',data.players,'goals');
      status.textContent=data.scope;
    } catch (error) {status.textContent=error.message;status.setAttribute('data-error','true');}
  };
  form.addEventListener('submit',e=>{e.preventDefault();refresh();});
  form.addEventListener('change',refresh);
  refresh();
})();
