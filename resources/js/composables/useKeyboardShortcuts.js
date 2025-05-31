import { onMounted, onBeforeUnmount } from 'vue'

export function useKeyboardShortcuts(shortcuts = {}) {
  const handleKeydown = (e) => {
    const callback = shortcuts[e.key]
    if (typeof callback === 'function') {
      callback(e)
    }
  }

  onMounted(() => {
    window.addEventListener('keydown', handleKeydown)
  })

  onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeydown)
  })
}
