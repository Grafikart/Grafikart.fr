import preactCustomElement from '@@/functions/preact.js'
import {FetchForm, FormField, FormPrimaryButton} from '@@/components/Form.jsx'
import {useState} from 'preact/hooks'
import {flash} from '@@/elements/Alert.js'

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
