import preactCustomElement from '/functions/preact.js'
import { FetchForm, FormField, FormPrimaryButton } from '/components/Form.jsx'
import { flash } from '/elements/Alert.js'

function FormNotification () {
  const onSuccess = function () {
    flash('Notification envoyée avec succès')
  }

  return (
    <FetchForm action='/api/notifications' className='stack' onSuccess={onSuccess}>
      <FormField name='message' type='textarea' />
      <FormField name='url' defaultValue={window.location.origin} />
      <FormPrimaryButton>Notifier</FormPrimaryButton>
    </FetchForm>
  )
}

preactCustomElement('form-notification', FormNotification)
