import { dump } from '/functions/preact'

export function Dump ({ object }) {
  return <div dangerouslySetInnerHTML={{ __html: dump(object) }}></div>
}
