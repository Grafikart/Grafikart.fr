import {FetchForm, FormField, FormPrimaryButton} from '@comp/Form'
import {Stack} from '@comp/Layout'
import {useState} from 'preact/hooks'
import {Fragment} from 'preact'
import {isAuthenticated} from '@fn/auth'
import {strToDom} from '@fn/dom'
import {slideDown} from '@fn/animation'

export function CreateMessage ({topic, parent}) {
  const [value, setValue] = useState({content: ''})
  const endpoint =`/api/topics/${topic}/messages`
  const onSuccess = function (data) {
    const message = strToDom(data.html)
    parent.insertAdjacentElement('beforebegin', message)
    slideDown(message)
    setValue({content: ''})
  }

  return (isAuthenticated() && <Fragment>
    <FetchForm action={endpoint} value={value} onChange={setValue} onSuccess={onSuccess}>
      <Stack>
        <FormField placeholder="Votre message" name="content" type="editor">Votre message</FormField>
        <FormPrimaryButton>Répondre</FormPrimaryButton>
      </Stack>
    </FetchForm>
  </Fragment>)

}
