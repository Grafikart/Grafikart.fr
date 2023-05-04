import {Icon} from '/components/Icon'
import {useEffect, useRef, useState} from 'preact/hooks'
import Fuse from 'fuse.js'
import {useMemo} from 'preact/compat'
import {clamp} from '/functions/math'
import clsx from 'clsx'
import {highlightFuse} from '/functions/fuse'
import {useClickOutside} from '/functions/hooks'
import {redirect} from '/functions/url'

/**
 * @typedef {{name: string, synonyms: string, href: string}} Word
 */

const searchOptions = {
  includeScore: false,
  includeMatches: true,
  minMatchCharLength: 2,
  threshold: 0.4,
  keys: ['name', 'synonyms']
}

/**
 * Champs de recherche avec suggest pour la partie glossaire
 */
export function GlossarySearch () {
  const input = useRef()
  const form = useRef()
  const [focused, setFocused] = useState(true)
  const [search, setSearch] = useState('')
  const words = useWords(search)
  const index = useKeyboardNavigation(input, words)
  useClickOutside(form, () => setFocused(false))
  const hasSuggest = focused && words.length > 0
  const onSubmit = (e) => {
    e.preventDefault()
    if (!words[index]) {
      return
    }
    redirect(words[index].href)
  }
  return <form className="form-group flex relative" action="" ref={form} onSubmit={onSubmit}>
    <input
      className={hasSuggest ? 'has-suggest' : null}
      ref={input}
      value={search}
      onChange={e => setSearch(e.target.value)}
      onFocus={() => setFocused(true)}
      autoComplete="off"
      type="text"
      name="q"
      placeholder="Rechercher un mot"
      required/>
    <button type="submit">
      <span className="reader-only">Rechercher</span>
      <Icon name="search"/>
    </button>
    {hasSuggest && <ul className="glossary-search__dropdown">
      {words.map((word, k) => <GlossarySearchItem key={word.href} word={word} selected={k === index}/>)}
    </ul>}
  </form>
}

/**
 * @param {{word: {name: JSX.Element, synonyms: JSX.Element, href: string}, selected: boolean}}
 */
function GlossarySearchItem ({word, selected}) {
  return <li>
    <a href={word.href} className={clsx("glossary-word", selected && "is-selected")}>
      <span className="glossary-word__name">{word.name}</span>{word.synonyms.length > 0 ? ', ' : ''}
      <span className="glossary-word__synonyms">{word.synonyms}</span>
    </a>
  </li>
}

/**
 * @param {string} search
 * @return {Array<{name: JSX.Element, synonyms: JSX.Element, href: string}>}
 */
function useWords (search) {
  const fuse = useRef()

  useEffect(() => {
    const words = Array.from(document.querySelectorAll('.glossary-word')).map(extractWordFromDom)
    fuse.current = new Fuse(words, searchOptions)
  }, [])

  return useMemo(() => fuse.current?.search(search) ?? [], [search])
    .slice(0, 5)
    .map(result => ({
      href: result.item.href,
      name: highlightFuse(result, 'name'),
      synonyms: highlightFuse(result, 'synonyms')
    }))
}

/**
 * @param {HTMLElement} el
 * @return {Word}
 */
function extractWordFromDom (el) {
  return {
    name: el.querySelector('.glossary-word__name').innerText,
    href: el.getAttribute('href'),
    synonyms: el.querySelector('.glossary-word__synonyms')?.innerText?.split(', ') ?? ''
  }
}

function useKeyboardNavigation (ref, list) {
  const [index, setIndex] = useState(0)
  const max = useRef(list.length)
  max.current = list.length - 1
  useEffect(() => {
    ref.current.addEventListener('keydown', (e) => {
      let direction = 0
      if (e.key === 'ArrowDown') {
        e.preventDefault()
        direction = 1
      }else if (e.key === 'ArrowUp') {
        e.preventDefault()
        direction = -1
      }
      if (direction === 0) {
        return;
      }
      setIndex((v) => clamp(v + direction, 0, max.current, true))
    })
    ref.current.addEventListener('input', (e) => {
      console.log('input')
    })
  }, [])

  return index
}
