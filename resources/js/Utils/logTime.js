function logTime(label) {
  const now = new Date()
  const time = now.toLocaleTimeString('ru-RU', { hour12: false }) + ':' + String(now.getMilliseconds()).padStart(3, '0')
  console.log(`[${time}] ${label}`)
}
