import {FetchForm, FormField, FormPrimaryButton} from '@comp/Form'
import {Stack} from '@comp/Layout'
import {useState} from 'preact/hooks'
import {Fragment} from 'preact'
import {usePush} from '@fn/hooks'
import SlideToggle from '@comp/Animation/SlideToggle'
import {isAuthenticated} from '@fn/auth'


function Message ({html}) {
  return <SlideToggle visible={true}>
    <div dangerouslySetInnerHTML={{__html: html}}/>
  </SlideToggle>
}

export function CreateMessage ({topic}) {

  const [value, setValue] = useState({content: ''})
  const endpoint =`/api/topics/${topic}/messages`
  const [messages, pushMessage] = usePush()
  const onSuccess = function (data) {
    pushMessage(data.html)
    setValue({content: ''})
  }

  return (isAuthenticated() && <Fragment>
    {messages.map(message => <Message html={message}/> )}
    <FetchForm action={endpoint} value={value} onChange={setValue} onSuccess={onSuccess}>
      <Stack>
        <FormField placeholder="Votre message" name="content" type="editor">Votre message</FormField>
        <FormPrimaryButton>Répondre</FormPrimaryButton>
      </Stack>
    </FetchForm>
  </Fragment>)

}
