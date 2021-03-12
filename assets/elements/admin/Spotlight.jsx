import { Modal } from '/components/Modal.jsx'
import { Field } from '/components/Form.jsx'
import { useEffect, useState } from 'preact/hooks'
import { classNames } from '/functions/dom.js'
import { useToggle } from '/functions/hooks.js'

/**
 * Barre permettant un accès rapide à certaines pages de l'administration
 */
export function Spotlight () {
  const [links, setLinks] = useState([])
  const [isVisible, toggleVisibility] = useToggle()
  const [search, setSearch] = useState()
  const [index, setIndex] = useState(0)
  const matches = getMatches(links, search)

  const handleInput = e => {
    setIndex(0)
    setSearch(e.target.value)
  }

  const handleKeyPress = e => {
    let direction
    if (e.key === 'ArrowUp') {
      direction = -1
    } else if (e.key === 'ArrowDown') {
      direction = 1
    } else if (e.key === 'Enter') {
      window.location.href = matches[index].link
      return
    } else {
      return
    }
    const newIndex = index + direction
    if (newIndex >= 0 && newIndex < matches.length) {
      setIndex(newIndex)
    }
  }

  useEffect(() => {
    const handler = e => {
      if (['k', ' '].includes(e.key) && e.ctrlKey === true) {
        e.preventDefault()
        toggleVisibility()
      }
    }
    window.addEventListener('keydown', handler)
    return () => window.removeEventListener('keydown', handler)
  }, [toggleVisibility])

  useEffect(() => {
    setLinks(
      Array.from(document.querySelectorAll('.header-nav a')).map(a => {
        const text = a.innerText.trim()
        return {
          link: a.getAttribute('href'),
          name: text === '' ? 'Dashboard' : text
        }
      })
    )
  }, [])

  return (
    isVisible && (
      <Modal class='spotlight' onClose={toggleVisibility}>
        <Field placeholder='Où voulez vous aller ?' onInput={handleInput} onKeyDown={handleKeyPress} autofocus />
        {matches.length > 0 && (
          <ul class='spotlight-suggestions'>
            {matches.map((match, i) => (
              <li key={match.link}>
                <a class={classNames(i === index && 'active')} href={match.link}>
                  {match.highlight}
                </a>
              </li>
            ))}
          </ul>
        )}
      </Modal>
    )
  )
}

/**
 *
 * @param {{name: string, link: string}[]} search
 * @param {string} search
 * @return {{highlight: JSX.Element, link: string}}
 */
function getMatches (links, search) {
  if (!search) {
    return []
  }
  let regexp = '\\b(.*)'
  for (const i in search) {
    regexp += `(${search[i]})(.*)`
  }
  regexp += '\\b'

  return links
    .map(link => {
      const results = link.name.match(new RegExp(regexp, 'i'))
      if (results) {
        const highlight = []
        for (const i in results) {
          if (i > 0) {
            highlight.push(i % 2 === 0 ? <mark>{results[i]}</mark> : results[i])
          }
        }
        return {
          ...link,
          highlight
        }
      }
      return null
    })
    .filter(link => link !== null)
}
