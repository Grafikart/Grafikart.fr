import { useEffect, useState } from 'preact/hooks'
import { EVENT_MESSAGE, EVENT_MESSAGE_DELETE } from '/elements/forum/constants.js'

export function ForumCount ({ count: countProp }) {
  const [count, setCount] = useState(parseInt(countProp, 10))

  const increment = () => setCount(n => n + 1)
  const decrement = () => setCount(n => n - 1)

  useEffect(() => {
    document.addEventListener(EVENT_MESSAGE, increment)
    document.addEventListener(EVENT_MESSAGE_DELETE, decrement)
    return () => {
      document.removeEventListener(EVENT_MESSAGE, increment)
      document.removeEventListener(EVENT_MESSAGE_DELETE, decrement)
    }
  })

  if (count === 0) {
    return 'Aucune réponse'
  } else if (count === 1) {
    return '1 réponse'
  }
  return `${count} réponses`
}
