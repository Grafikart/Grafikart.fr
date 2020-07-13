import { FetchForm, FormField, FormPrimaryButton } from '/components/Form.jsx'
import { Stack } from '/components/Layout.jsx'
import { useState } from 'preact/hooks'
import { isAuthenticated } from '/functions/auth.js'
import { strToDom } from '/functions/dom.js'
import { slideDown } from '/functions/animation.js'

export function CreateMessage ({ topic, parent }) {
  const [value, setValue] = useState({ content: '' })
  const endpoint = `/api/topics/${topic}/messages`
  const onSuccess = function (data) {
    const message = strToDom(data.html)
    parent.insertAdjacentElement('beforebegin', message)
    slideDown(message)
    setValue({ content: '' })
  }

  return (
    isAuthenticated() && (
      <FetchForm action={endpoint} value={value} onChange={setValue} onSuccess={onSuccess}>
        <Stack>
          <FormField placeholder='Votre message' name='content' type='editor'>
            Votre message
          </FormField>
          <FormPrimaryButton>Répondre</FormPrimaryButton>
        </Stack>
      </FetchForm>
    )
  )
}
