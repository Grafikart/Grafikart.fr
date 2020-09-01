import { Button } from '/components/Button.jsx'
import { Icon } from '/components/Icon.jsx'
import { Modal } from '/components/Modal.jsx'
import { useToggle } from '/functions/hooks.js'
import { Stack } from '/components/Layout.jsx'
import { FetchForm, FormButton, FormField } from '/components/Form.jsx'
import { flash } from '/elements/Alert.js'
import { redirect } from '/functions/url.js'

export default function DeleteAccount ({ url, csrf, days }) {
  const [modal, toggleModal] = useToggle(false)

  const handleSuccess = async ({ message }) => {
    toggleModal()
    await redirect('/')
    flash(message, 'success', null)
  }

  return (
    <>
      <Button className='btn btn-danger' onClick={toggleModal}>
        <Icon name='trash' />
        Supprimer mon compte
      </Button>
      {modal && (
        <Modal padding={5}>
          <FetchForm action={url} method='DELETE' onSuccess={handleSuccess} data={{ csrf }} floatingAlert>
            <Stack gap='3'>
              <h1 class='h1'>Confirmer la suppression</h1>
              <p class='small text-muted'>
                Vous êtes sur le point de supprimer votre compte Grafikart.
                <br />
                Pour confirmer cette demande merci de rentrer votre mot de passe. Le compte sera automatiquement
                supprimé au bout de {days} jours
              </p>
              <FormField type='password' name='password' placeholder='Entrez votre mot de passer pour confirmer' />
              <FormButton class='btn btn-block btn-danger mla'>Confirmer la suppression</FormButton>
            </Stack>
          </FetchForm>
        </Modal>
      )}
    </>
  )
}
