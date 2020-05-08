import preactCustomElement from '@fn/preact'
import {FetchForm, FormField, FormPrimaryButton} from '@comp/Form'
import {useState} from 'preact/hooks'
import {FloatingAlert} from '@comp/Alert'

function FormNotification () {

  const initialData = {message: '', url: window.location.origin}
  const [data, setData] = useState(initialData)
  const [success, setSuccess] = useState(false)

  const onSuccess = function () {
    setSuccess(true)
    setData(initialData)
  }

  return <FetchForm action="/api/notifications" value={data} className="stack" onChange={setData} onSuccess={setSuccess}>
    {success && <FloatingAlert type="success">Notification envoyée avec succès</FloatingAlert>}
    <FormField name="message" type="textarea"/>
    <FormField name="url"/>
    <FormPrimaryButton>Notifier</FormPrimaryButton>
  </FetchForm>

}

preactCustomElement(FormNotification, 'form-notification')
