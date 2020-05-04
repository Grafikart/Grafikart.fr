import {useState} from 'preact/hooks'
import {Field, Formart, PrimaryButton} from '../Form'
import {Stack} from '../Layout'

export function Report () {
  const [value, onChange] = useState({reason: 'reason :('})
  return <Formart value={value} onChange={onChange}>
    <Stack>
      <Field name="reason">Raison du signalement</Field>
      <div>
        <PrimaryButton>Envoyer</PrimaryButton>
      </div>
    </Stack>
    {JSON.stringify(value)}
  </Formart>
}
