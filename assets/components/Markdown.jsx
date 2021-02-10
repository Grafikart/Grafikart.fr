import MarkdownToJSX from 'markdown-to-jsx';
import { useEffect, useRef } from 'preact/hooks'
import { bindHighlight } from '/modules/highlight.js'

const replacement = (className = 'bold') => ({
  component: 'p',
  props: {
    className
  }
})

export function Markdown ({children, ...props}) {
  const ref = useRef()
  console.log(ref)
  useEffect(() => {
    if (ref.current && ref.current.querySelectorAll('pre').length > 0) {
      bindHighlight(ref.current)
    }
  }, [children])
  return <div ref={ref} {...props}>
    <MarkdownToJSX children={children} options={{
      disableParsingRawHTML: true,
      forceBlock: true,
      wrapper: null,
      overrides: {
        h1: replacement('bold underline text-big'),
        h2: replacement('bold underline'),
        h3: replacement(),
        h4: replacement(),
        h5: replacement(),
        h6: replacement()
      }
    }}/>
  </div>
}

