import { onMounted, onBeforeUnmount, nextTick } from 'vue'

export function useClickOutside(targetRef, callback, excludedRef = null) {
  let enabled = false

  const handleClick = (event) => {
    if (!enabled) return

    const targetEl = targetRef.value
    const excludedEl = excludedRef?.value

    if (
      targetEl &&
      !targetEl.contains(event.target) &&
      (!excludedEl || !excludedEl.contains(event.target))
    ) {
      callback(event)
    }
  }

  onMounted(async () => {
    await nextTick()
    enabled = true
    document.addEventListener('click', handleClick)
  })

  onBeforeUnmount(() => {
    document.removeEventListener('click', handleClick)
  })
}
