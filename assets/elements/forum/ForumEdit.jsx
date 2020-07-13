import { canManage } from '/functions/auth.js'
import { Loader } from '/components/Loader.jsx'
import { useRef, useState } from 'preact/hooks'
import { createPortal } from 'preact/compat'
import { FetchForm, FormField, FormPrimaryButton } from '/components/Form.jsx'
import { Flex, Stack } from '/components/Layout.jsx'
import { resolveEndpoint } from '/api/forum.js'
import { jsonFetch } from '/functions/api.js'
import { SecondaryButton } from '/components/Button.jsx'

export function ForumEdit ({ message, topic, owner }) {
  const element = useRef()
  const container = useRef()
  const [rawContent, setRawContent] = useState(null)
  const [loading, setLoading] = useState(false)
  const [state, setState] = useState('view')
  const endpoint = resolveEndpoint({ message, topic })

  // Handler
  async function startEditing () {
    // On récupère le contenu original
    if (rawContent === null) {
      setLoading(true)
      const response = await jsonFetch(endpoint)
      setLoading(false)
      setRawContent(response.content)
    }
    const message = element.current.closest('.forum-message')
    container.current = message.querySelector('.js-forum-edit')
    message.querySelector('.js-content').style.display = 'none'
    setState('edit')
  }

  function handleCancel () {
    setState('view')
    const message = element.current.closest('.forum-message')
    message.querySelector('.js-content').style.display = null
  }

  function handleSuccess (data) {
    // On met à jour le contenu dans la div
    const message = element.current.closest('.forum-message')
    setRawContent(data.content)
    message.querySelector('.js-content').innerHTML = data.formattedContent
    // On revient sur l'affichage du message
    handleCancel()
  }

  // L'utilisateur ne peut pas intervenir sur ce sujet
  if (!canManage(owner)) {
    return null
  }

  if (topic) {
    return (
      <>
        - <a href={`/forum/${topic}/edit`}>Editer</a>
      </>
    )
  }

  return (
    <span ref={element}>
      {state === 'view' && (
        <>
          -{' '}
          <button onClick={startEditing}>
            {loading && <Loader style={{ width: 12, marginRight: 5 }} />}
            Editer
          </button>
        </>
      )}
      {state === 'edit' && (
        <ForumEditor
          container={container.current}
          endpoint={endpoint}
          content={rawContent}
          onSuccess={handleSuccess}
          onCancel={handleCancel}
        />
      )}
    </span>
  )
}

/**
 * Génère un éditeur pour l'édition d'un message sur le forum
 */
function ForumEditor ({ container, endpoint, onCancel, content, onSuccess }) {
  return createPortal(
    <FetchForm action={endpoint} method='PUT' onSuccess={onSuccess}>
      <Stack>
        <FormField name='content' defaultValue={content} type='editor' />
        <Flex>
          <FormPrimaryButton>Editer</FormPrimaryButton>
          <SecondaryButton onClick={onCancel} type='button'>
            Annuler
          </SecondaryButton>
        </Flex>
      </Stack>
    </FetchForm>,
    container
  )
}
