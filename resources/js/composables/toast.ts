import { reactive } from 'vue'

export const toastState = reactive({
  messages: [] as Array<{ id: number; title?: string; body: string; type?: 'success'|'error'|'info' }>,
})

let id = 1

export function showToast(body: string, type: 'success'|'error'|'info' = 'info', title?: string) {
  const tid = id++
  toastState.messages.push({ id: tid, title, body, type })
  setTimeout(() => {
    const idx = toastState.messages.findIndex(m => m.id === tid)
    if (idx !== -1) toastState.messages.splice(idx, 1)
  }, 4000)
}

export function clearToasts() {
  toastState.messages.splice(0, toastState.messages.length)
}
