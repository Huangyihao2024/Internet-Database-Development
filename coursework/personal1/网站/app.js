$(function () {
  let resources = [];
  function render() {
    const q = $('#search').val().trim().toLowerCase();
    const category = $('#category').val();
    const items = resources.filter(x => x.title.toLowerCase().includes(q) && (category === '全部' || x.category === category));
    $('#cards').empty();
    items.forEach(x => {
      const card = $('<article>').addClass('card');
      $('<span>').addClass('tag').text(x.category).appendTo(card);
      $('<h3>').text(x.title).appendTo(card);
      $('<p>').text(x.description).appendTo(card);
      card.appendTo('#cards');
    });
    $('#result').text('当前显示 ' + items.length + ' 项资源');
  }
  $('#search').on('input', render);
  $('#category').on('change', render);
  $('#expand').on('click', function () {
    const open = $(this).attr('aria-expanded') !== 'true';
    $('#details').stop(true,true).slideToggle(180);
    $(this).attr('aria-expanded', String(open)).text(open ? '收起成员介绍' : '展开成员介绍');
  });
  $('#theme').on('click', function () {
    $('body').toggleClass('dark');
    $(this).text($('body').hasClass('dark') ? '切换浅色模式' : '切换深色模式');
  });
  $('#messageForm').on('submit', function (event) {
    event.preventDefault();
    const name = $('#name').val().trim(), content = $('#content').val().trim();
    if (!name || !content) { $('#feedback').text('姓名和留言不能为空'); return; }
    $.ajax({ url:'/api/messages', method:'POST', contentType:'application/json', data:JSON.stringify({name,content}) })
      .done(function (data) {
        $('#feedback').addClass('notice').text('留言提交成功，编号：' + data.id);
        $('<li>').text(data.name + '：' + data.content).appendTo('#messages');
        $('#content').val('');
      }).fail(function (xhr) { $('#feedback').text('提交失败：' + xhr.status); });
  });
  $('#load').on('click', function () {
    $.getJSON('/api/resources?category=' + encodeURIComponent($('#category').val())).done(function (data) { resources = data.items; render(); }).fail(function () { $('#result').text('加载失败，请先启动服务'); });
  });
  $('#load').trigger('click');
});
