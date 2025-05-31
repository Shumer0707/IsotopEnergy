const imageUrls = new Set()
let promises = []

export function registerImage(url) {
  if (!imageUrls.has(url)) {
    imageUrls.add(url)
    const promise = new Promise((resolve) => {
      const img = new Image()
      img.onload = img.onerror = () => resolve()
      img.src = url
    })
    promises.push(promise)
  }
}

export async function waitForAllImages() {
  await Promise.all(promises)
  // Очищаем, чтобы не тянуть старые при повторной загрузке
  imageUrls.clear()
  promises = []
}
