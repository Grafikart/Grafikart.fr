import {useCallback, useEffect, useRef, useState} from 'preact/hooks'
import {clamp, randomBetween} from '/functions/math'
import {useMemo} from 'preact/compat'
import {Icon} from '/components/Icon'
import clsx from 'clsx'
import {ApiError, HTTP_FORBIDDEN, jsonFetch} from '/functions/api'
import {useRefSync} from '/functions/hooks'

/**
 * Génère un captcha sous forme de puzzle
 *
 * @param {{width: string, height: string, piecewidth: string, pieceheight: string}} props
 * @param {string} name
 */
export function Captcha ({name, ...props}) {
  const {width, height, piecewidth, pieceheight} = castInt(props)
  const max = useMemo(() => [width - piecewidth, height - pieceheight], [])
  const [position, movePosition] = usePosition(max)
  const positionRef = useRefSync(position)
  const {state, cacheKey, guess} = useValidateCaptcha()
  const src = `/captcha?key=${cacheKey}`
  const isPointerDown = useRef(false)
  const cursorOffsetPosition = useRef({x: 0, y: 0})
  const framePosition = useRef({x: 0, y: 0})
  /** @var {import('preact').RefObject<HTMLDivElement>} pieceRef */
  const pieceRef = useRef()
  /** @param {PointerEvent} e */
  const handleUp = useCallback((e) => {
    isPointerDown.current = false
    guess(positionRef.current).catch(console.error)
  }, [])

  // Confetti
  useEffect(() => {
    pieceRef.current.closest('form').querySelector('button').removeAttribute('disabled')
  }, [state])

  // Désactive le bouton submit
  useEffect(() => {
    pieceRef.current.closest('form').querySelector('button').setAttribute('disabled', 'disabled')
  }, [])

  /** @param {PointerEvent} e */
  const handleDown = (e) => {
    isPointerDown.current = true
    const rect = pieceRef.current.getBoundingClientRect();
    framePosition.current = pieceRef.current.parentElement.getBoundingClientRect()
    cursorOffsetPosition.current = {
      x: e.clientX - rect.x,
      y: e.clientY - rect.y
    }
    document.addEventListener('pointerup', handleUp, {once: true})
  }

  /** @param {PointerEvent} e */
  const handleMove = useCallback((e) => {
    if (!isPointerDown.current) {
      return
    }
    movePosition(
      e.clientX - framePosition.current.x - cursorOffsetPosition.current.x,
      e.clientY - framePosition.current.y - cursorOffsetPosition.current.y
    )
  }, [movePosition])

  const transform = `translate3d(${position[0]}px, ${position[1]}px, 0)`

  const isLoading = state === 'loading'
  const isSolved = state === 'solved'
  const isError = state === 'error'

  return <div>
    <div className="form-text text-muted text-small mb1">
      Placez la pièce du puzzle pour vérifier que vous n’êtes pas un
      robot
    </div>
    <div
      className={clsx('relative captcha', isSolved && 'captcha--success', isLoading && 'captcha--loading', isError && 'captcha--error')}
      style={{width: width, height: height, '--width': width}}>
      <div className="captcha__background"
           style={{backgroundImage: `url(${src})`}}
           onPointerMove={handleMove}>
        <div
          ref={pieceRef}
          className="captcha__piece"
          style={{backgroundImage: `url(${src})`, width: piecewidth, height: pieceheight, transform: transform}}
          onPointerDown={handleDown}/>
      </div>
      {isSolved && <Icon name="check"/>}
      {isLoading && <spinning-dots className="captcha__loader"/>}
    </div>
    <input type="hidden" name={name} value={`${Math.round(position[0])}-${Math.round(position[1])}`}/>
  </div>
}

function useCacheKey () {
  const [key, setKey] = useState(() => Date.now())
  const generate = () => {
    setKey(Date.now())
  }
  return [key, generate]
}

function usePosition ([maxX, maxY]) {
  const [position, setPosition] = useState(() => [randomBetween(0, maxX), randomBetween(0, maxY)])
  const movePosition = useCallback((x, y) => {
    setPosition([
      clamp(x, 0, maxX),
      clamp(y, 0, maxY)
    ])
  }, [maxX, maxY, setPosition])
  return [position, movePosition]
}

/**
 * Valide le captcha côté serveur
 */
function useValidateCaptcha () {
  const [cacheKey, regenerateCacheKey] = useCacheKey()
  const [state, setState] = useState('default')
  const guess = async ([x, y]) => {
    setState('loading')
    try {
      await jsonFetch('/captcha/validate', {
        method: 'post',
        body: {
          response: `${Math.round(x)}-${Math.round(y)}`
        }
      })
      setState('solved')
    } catch (e) {
      if (e instanceof ApiError) {
        setState('error')
        if (e.status === HTTP_FORBIDDEN) {
          setTimeout(() => setState('loading'), 500)
          setTimeout(() => regenerateCacheKey(), 500)
          setTimeout(() => setState('default'), 1000)
        } else {
          setTimeout(() => setState('default'), 500)
        }
      } else {
        throw e
      }
    }
  }

  return {state, guess, cacheKey}
}

/**
 * Convert property to int
 *
 * @param {Record<string, string>} obj
 * @return {Record<string, number>}
 */
function castInt (obj) {
  return Object.fromEntries(Object.entries(obj).map(([k, v]) => [k, parseInt(v, 10)]))
}
