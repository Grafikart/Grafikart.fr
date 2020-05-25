import {Fragment} from 'preact'
import {FetchForm, FormField, FormPrimaryButton} from '@comp/Form'
import {useClickOutside, useJsonFetchOrFlash, useToggle} from '@fn/hooks'
import {useCallback, useRef, useState} from 'preact/hooks'
import {SlideIn} from '@comp/Animation/SlideIn'
import {flash} from '@el/Alert'
import {Icon} from '@comp/Icon'
import {RoundedButton} from '@comp/Button'
import {slideUp} from '@fn/animation'
import {closest} from '@fn/dom'

export function ForumActions ({message, topic}) {
  // On récupère le endpoint à appeler pour la suppresion (ou l'édition)
  let endpoint = null
  if (message) {
    endpoint = `/api/forum/messages/${message}`
  } else if (topic) {
    endpoint = `/api/forum/topics/${topic}`
  } else {
    console.error('Impossible de charger le formulaire de signalement')
    return
  }

  // On prépare les hooks
  const [deleteLoading, deleteCalled, deleteFetch] = useJsonFetchOrFlash(endpoint, {method: 'DELETE'})

  // Handler
  const handleDeleteClick = useCallback(async function (e) {
    if (!confirm('Voulez vous vraiment supprimer ce message ?')) {
      return
    }
    const message = closest(e.target, '.forum-message')
    await deleteFetch()
    flash('Votre message a bien été supprimé')
    await slideUp(message)
    message.remove()
  }, [endpoint])

  // Rendu
  return <Fragment>
    {deleteCalled === null && <DeleteButton loading={deleteLoading} onClick={handleDeleteClick}/>}
    <ReportButton message={message} topic={topic}/>
  </Fragment>
}

function DeleteButton (props) {
  return <RoundedButton type="danger" title="Supprimer ce message" {...props}><Icon name="trash"/></RoundedButton>
}


/**
 * Bouton de signalement avec formulaire en tooltip
 */
function ReportButton ({message, topic}) {
  // Hooks
  const ref = useRef(null)
  const [showForm, toggleForm] = useToggle(false)
  const [success, toggleSuccess] = useToggle(false)
  useClickOutside(ref, toggleForm)

  return <div style={{position: 'relative'}}>
    <button className="rounded-button" onClick={toggleForm} disabled={success} aria-label="Signaler le message" data-microtip-position="top" role="tooltip">
      <span>!</span>
    </button>
    <SlideIn show={showForm && !success} className="forum-report__form" forwardedRef={ref}>
      <ReportForm message={message} topic={topic} onSuccess={toggleSuccess}></ReportForm>
    </SlideIn>
  </div>
}

/**
 * Formulaire de signalement
 */
function ReportForm ({onSuccess, message, topic}) {
  const initialData = {reason: 'Ceci est un spam', message: null, topic: null}
  if (message) {
    initialData.message = `/api/forum/messages/${message}`
  } else if (topic) {
    initialData.topic = `/api/forum/topics/${topic}`
  } else {
    console.error('Impossible de charger le forumulaire de signalement')
    return
  }
  const placeholder = 'Indiquez en quoi ce sujet ne convient pas'
  const action = '/api/forum/reports'
  const [value, setValue] = useState(initialData)
  const onReportSuccess = function () {
    setValue(initialData)
    flash('Merci pour votre signalement')
    onSuccess()
  }

  return <FetchForm value={value} onChange={setValue} className="forum-report stack" action={action} onSuccess={onReportSuccess}>
    <FormField type="textarea" name="reason" required placeholder={placeholder} autofocus>Raison du
      signalement</FormField>
    <FormPrimaryButton>Envoyer</FormPrimaryButton>
  </FetchForm>
}
