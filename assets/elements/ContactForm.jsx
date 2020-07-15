import { FetchForm, FormField, FormPrimaryButton } from '/components/Form.jsx'
import { useState } from 'preact/hooks'
import { Alert } from '/components/Alert.jsx'

export function ContactForm () {
  const [success, setSuccess] = useState(false)

  if (success) {
    return <Alert>Votre mail a bien été envoyé, vous recevrez une réponse dans les plus bref délais.</Alert>
  }

  return (
    <FetchForm action='/api/contact' onSuccess={() => setSuccess(true)} className='grid2'>
      <FormField name='name' required>
        Votre nom
      </FormField>
      <FormField name='email' type='email' required>
        Votre email
      </FormField>
      <FormField name='content' type='textarea' required wrapperClass='full'>
        Votre message
      </FormField>
      <div className='full'>
        <FormPrimaryButton>Envoyer</FormPrimaryButton>
      </div>
    </FetchForm>
  )
}
