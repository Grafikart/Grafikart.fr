import {FetchForm, FormField, FormPrimaryButton} from '../Form'
import {Stack} from '../Layout'
import {useState} from 'preact/hooks'
import {Fragment} from 'preact'
import {usePush} from '@fn/hooks'
import SlideToggle from '../Animation/SlideToggle'


function Message ({html}) {
  return <SlideToggle visible={true}>
    <div dangerouslySetInnerHTML={{__html: html}}/>
  </SlideToggle>
}

export default function MessageCreate ({topic}) {

  const [value, setValue] = useState({content: ''})
  const endpoint =`/api/topics/${topic}/messages`
  const [messages, pushMessage] = usePush()
  const onSuccess = function (data) {
    pushMessage(data.html)
    setValue({content: ''})
  }

  return <Fragment>
    {messages.map(message => <Message html={message}/> )}
    <FetchForm action={endpoint} value={value} onChange={setValue} onSuccess={onSuccess}>
      <Stack>
        <FormField placeholder="Votre message" name="content" type="editor">Votre message</FormField>
        <FormPrimaryButton>Répondre</FormPrimaryButton>
      </Stack>
    </FetchForm>
  </Fragment>

}
