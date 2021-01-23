import { canManage, isAuthenticated } from '/functions/auth.js'
import { Icon } from '/components/Icon.jsx'
import { memo } from 'preact/compat'
import { useCallback, useEffect, useMemo, useRef, useState } from 'preact/hooks'
import { addComment, deleteComment, findAllComments, updateComment } from '/api/comments.js'
import { PrimaryButton, SecondaryButton } from '/components/Button.jsx'
import { Flex } from '/components/Layout.jsx'
import { Field } from '/components/Form.jsx'
import { scrollTo } from '/functions/animation.js'
import { catchViolations } from '/functions/api.js'
import { useVisibility, useAsyncEffect } from '/functions/hooks.js'
import { Markdown } from '/components/Markdown.jsx'

/**
 * Affiche les commentaires associé à un contenu
 *
 * @param {{target: number}} param0
 */
export function Comments ({ target, parent }) {
  target = parseInt(target, 10)
  const element = useRef(null)
  const [state, setState] = useState({
    editing: null, // ID du commentaire en cours d'édition
    comments: null, // Liste des commentaires
    focus: null, // Commentaire à focus
    reply: null // Commentaire auquel on souhaite répondre
  })
  const count = state.comments ? state.comments.length : null
  const isVisible = useVisibility(parent)
  const comments = useMemo(() => {
    if (state.comments === null) {
      return null
    }
    return state.comments.filter(c => c.parent === 0).sort((a, b) => b.createdAt - a.createdAt)
  }, [state.comments])

  // Trouve les commentaire enfant d'un commentaire
  function repliesFor (comment) {
    return state.comments.filter(c => c.parent === comment.id)
  }

  // On commence l'édition d'un commentaire
  const handleEdit = useCallback(comment => {
    setState(s => ({ ...s, editing: s.editing === comment.id ? null : comment.id }))
  }, [])

  // On met à jour (via l'API un commentaire)
  const handleUpdate = useCallback(async (comment, content) => {
    const newComment = await updateComment(comment.id, content)
    setState(s => ({
      ...s,
      editing: null,
      comments: s.comments.map(c => (c === comment ? newComment : c))
    }))
  }, [])

  // On supprime un commentaire
  const handleDelete = useCallback(async comment => {
    await deleteComment(comment.id)
    setState(s => ({
      ...s,
      comments: s.comments.filter(c => c !== comment)
    }))
  }, [])

  // On répond à un commentaire
  const handleReply = useCallback(comment => {
    setState(s => ({ ...s, reply: comment.parent || comment.id }))
  }, [])
  const handleCancelReply = useCallback(() => {
    setState(s => ({ ...s, reply: null }))
  }, [])

  // On crée un nouveau commentaire
  const handleCreate = useCallback(
    async (data, parent) => {
      data = { ...data, target, parent }
      const newComment = await addComment(data)
      setState(s => ({
        ...s,
        focus: newComment.id,
        reply: null,
        comments: [...s.comments, newComment]
      }))
    },
    [target]
  )

  // On scroll jusqu'à l'élément si l'ancre commence par un "c"
  useAsyncEffect(async () => {
    if (window.location.hash.startsWith('#c')) {
      const comments = await findAllComments(target)
      setState(s => ({
        ...s,
        comments,
        focus: window.location.hash.replace('#c', '')
      }))
    }
  }, [element])

  // On charge les commentaire dès l'affichage du composant
  useAsyncEffect(async () => {
    if (isVisible) {
      const comments = await findAllComments(target)
      setState(s => ({ ...s, comments }))
    }
  }, [target, isVisible])

  // On se focalise sur un commentaire
  useEffect(() => {
    if (state.focus && comments) {
      scrollTo(document.getElementById(`c${state.focus}`))
      setState(s => ({ ...s, focus: null }))
    }
  }, [state.focus, comments])

  // On rend la liste des commentaires
  return (
    <div className='comment-area' ref={element}>
      <div className='comments__title'>
        {count === null ? (
          <skeleton-box text='3 Commentaires' />
        ) : (
          <>
            {count} commentaire{count > 1 ? 's' : ''}
          </>
        )}
      </div>
      <CommentForm onSubmit={handleCreate} />
      <hr />
      <div className='comment-list'>
        {comments ? (
          comments.map(comment => (
            <Comment
              key={comment.id}
              comment={comment}
              editing={state.editing === comment.id}
              onEdit={handleEdit}
              onUpdate={handleUpdate}
              onDelete={handleDelete}
              onReply={handleReply}
            >
              {repliesFor(comment).map(reply => (
                <Comment
                  key={reply.id}
                  comment={reply}
                  editing={state.editing === reply.id}
                  onEdit={handleEdit}
                  onUpdate={handleUpdate}
                  onDelete={handleDelete}
                  onReply={handleReply}
                />
              ))}
              {state.reply === comment.id && (
                <CommentForm onSubmit={handleCreate} parent={comment.id} onCancel={handleCancelReply} />
              )}
            </Comment>
          ))
        ) : (
          <>
            <FakeComment />
            <FakeComment />
            <FakeComment />
          </>
        )}
      </div>
    </div>
  )
}

const FakeComment = memo(() => {
  return (
    <div class='comment'>
      <skeleton-box className='comment__avatar' width='40' height='40' rounded />
      <div className='comment__meta'>
        <skeleton-box className='comment__author' text='John Doe comm' />
        <div className='comment_actions'>
          <skeleton-box className='comment__date' text='Il y a 9 mois' />
        </div>
      </div>
      <skeleton-box className='comment__content' width='921' height='90' />
    </div>
  )
})

/**
 * Affiche un commentaire
 */
const Comment = memo(({ comment, editing, onEdit, onUpdate, onDelete, onReply, children }) => {
  const anchor = `#c${comment.id}`
  const canEdit = canManage(comment.userId)
  const className = ['comment']
  const textarea = useRef(null)
  const [loading, setLoading] = useState(false)

  const handleEdit = canEdit
    ? e => {
        e.preventDefault()
        onEdit(comment)
      }
    : null

  async function handleUpdate (e) {
    e.preventDefault()
    setLoading(true)
    await onUpdate(comment, textarea.current.value)
    setLoading(false)
  }

  async function handleDelete (e) {
    e.preventDefault()
    if (confirm('Voulez vous vraiment supprimer ce commentaire ?')) {
      setLoading(true)
      await onDelete(comment)
    }
  }

  function handleReply (e) {
    e.preventDefault()
    onReply(comment)
  }

  // On focus automatiquement le champs quand il devient visible
  useEffect(() => {
    if (textarea.current) {
      textarea.current.focus()
    }
  }, [editing])

  let content = <Markdown children={comment.content} class='formatted' onDoubleClick={handleEdit}/>
  if (editing) {
    content = (
      <form onSubmit={handleUpdate} className='form-group stack'>
        <textarea is='textarea-autogrow' ref={textarea} defaultValue={comment.content} />
        <Flex>
          <PrimaryButton type='submit' loading={loading}>
            Modifier
          </PrimaryButton>
          <SecondaryButton type='reset' onClick={handleEdit}>
            Annuler
          </SecondaryButton>
        </Flex>
      </form>
    )
  }
  if (loading) {
    className.push('is-loading')
  }

  return (
    <div className={className.join(' ')} id={`c${comment.id}`}>
      <img src={comment.avatar} alt='' className='comment__avatar' />
      <div className='comment__meta'>
        <div className='comment__author'>{comment.username}</div>
        <div className='comment__actions'>
          <a className='comment__date' href={`#c${comment.id}`}>
            <time-ago time={comment.createdAt} />
          </a>
          <a href={anchor} onClick={handleReply}>
            <Icon name='reply' />
            Répondre
          </a>
          {canEdit && (
            <a href={anchor} onClick={handleEdit}>
              <Icon name='edit' />
              Editer
            </a>
          )}
          {canEdit && (
            <a href={anchor} onClick={handleDelete} className='text-danger'>
              <Icon name='trash' />
              Supprimer
            </a>
          )}
        </div>
      </div>
      <div className='comment__content'>{content}</div>
      <div className='comment__replies'>{children}</div>
    </div>
  )
})

/**
 * Formulaire de commentaire
 * @params {{onSubmit: function, parent: number}} props
 */
function CommentForm ({ onSubmit, parent, onCancel = null }) {
  const [loading, setLoading] = useState(false)
  const [errors, setErrors] = useState({})
  const ref = useRef(null)

  const handleSubmit = useCallback(
    async e => {
      const form = e.target
      e.preventDefault()
      setLoading(true)
      const errors = (await catchViolations(onSubmit(Object.fromEntries(new FormData(form)), parent)))[1]
      if (errors) {
        setErrors(errors)
      } else {
        form.reset()
      }
      setLoading(false)
    },
    [onSubmit, parent]
  )

  const handleCancel = function (e) {
    e.preventDefault()
    onCancel()
  }

  useEffect(() => {
    if (parent && ref.current) {
      scrollTo(ref.current)
    }
  }, [parent])

  return (
    <form className='grid' onSubmit={handleSubmit} ref={ref}>
      {!isAuthenticated() && (
        <>
          <Field name='username' error={errors.username} required>
            Nom d'utilisateur
          </Field>
          <Field name='email' type='email' required error={errors.email}>
            Email
          </Field>
        </>
      )}
      <div className='full'>
        <Field type='textarea' name='content' error={errors.content} required>
          Votre message
        </Field>
      </div>
      <Flex className='full'>
        <PrimaryButton type='submit' loading={loading}>
          Envoyer
        </PrimaryButton>
        {onCancel && <SecondaryButton onClick={handleCancel}>Annuler</SecondaryButton>}
      </Flex>
    </form>
  )
}
