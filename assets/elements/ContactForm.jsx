import { FetchForm, FormField, FormPrimaryButton } from '/components/Form.jsx'

export function ContactForm () {

  const handleSuccess = () => {
    console.log('bravo !')
  }

  return <FetchForm action="/contact" onSuccess={handleSuccess} className="grid2">
    <FormField name="name" required defaultValue="John doe">Votre nom</FormField>
    <FormField name="email" type="email" required defaultValue="john@doe.fr">Votre email</FormField>
    <FormField name="content" type="textarea" required wrapperClass="full" defaultValue="Ceci est un exemple de message">Votre message</FormField>
    <div className="full">
      <FormPrimaryButton>Envoyer</FormPrimaryButton>
    </div>
  </FetchForm>
}
