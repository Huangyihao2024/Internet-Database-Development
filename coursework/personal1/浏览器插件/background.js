chrome.alarms.onAlarm.addListener(async alarm => {
  if (alarm.name === 'focusEnd') {
    await chrome.storage.local.set({focusEnd:0});
    await chrome.action.setBadgeText({text:'完成'});
    await chrome.action.setBadgeBackgroundColor({color:'#205c47'});
  }
});
