import { FetchForm, FormField, FormPrimaryButton } from '/components/Form.jsx'
import { Stack } from '/components/Layout.jsx'
import { useState } from 'preact/hooks'
import { isAuthenticated } from '/functions/auth.js'
import { strToDom } from '/functions/dom.js'
import { slideDown } from '/functions/animation.js'
import { Icon } from '/components/Icon.jsx'

export function CreateMessage ({ topic, parent, disabled }) {
  const [value, setValue] = useState({
    content: '',
    notification: true
  })
  const endpoint = `/api/forum/topics/${topic}/messages`
  const onSuccess = function (data) {
    const message = strToDom(data.html)
    parent.insertAdjacentElement('beforebegin', message)
    slideDown(message)
    setValue({ content: '' })
  }

  if (!isAuthenticated()) {
    return null
  }

  if (disabled !== undefined) {
    return (
      <div class='forum-locked-form'>
        <Stack>
          <FormField placeholder='Votre message' name='content' type='editor'>
            Votre message
          </FormField>
          <FormPrimaryButton disabled>Répondre</FormPrimaryButton>
        </Stack>
        <div class='forum-locked-form__message'>
          <Icon name='lock' /> Ce sujet est verrouillé
        </div>
      </div>
    )
  }

  return (
    <FetchForm action={endpoint} value={value} onChange={setValue} onSuccess={onSuccess}>
      <Stack>
        <FormField placeholder='Votre message' name='content' type='editor'>
          Votre message
        </FormField>
        <FormField name='notification' type='checkbox' defaultChecked={true}>
          Être notifié en cas de réponse
        </FormField>
        <FormPrimaryButton>Répondre</FormPrimaryButton>
      </Stack>
    </FetchForm>
  )
}
