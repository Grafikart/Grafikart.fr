import preactCustomElement from '@fn/preact'
import {FetchForm, FormField, FormPrimaryButton} from '@comp/Form'
import {useState} from 'preact/hooks'
import {flash} from '@el/Alert'

function FormNotification () {

  const initialData = {message: '', url: window.location.origin}
  const [data, setData] = useState(initialData)

  const onSuccess = function () {
    flash('Notification envoyée avec succès')
    setData(initialData)
  }

  return <FetchForm action="/api/notifications" value={data} className="stack" onChange={setData} onSuccess={setSuccess}>
    <FormField name="message" type="textarea"/>
    <FormField name="url"/>
    <FormPrimaryButton>Notifier</FormPrimaryButton>
  </FetchForm>

}

preactCustomElement(FormNotification, 'form-notification')
