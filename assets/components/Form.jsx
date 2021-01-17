import { createContext } from 'preact'
import { ApiError, jsonFetch } from '/functions/api.js'
import { useContext, useEffect, useLayoutEffect, useRef, useState } from 'preact/hooks'
import { useAutofocus } from '/functions/hooks.js'
import { flash } from '/elements/Alert.js'
import { Button, PrimaryButton, SecondaryButton } from '/components/Button.jsx'
import { Flex } from '/components/Layout.jsx'
import { classNames } from '/functions/dom.js'
import { useMemo } from 'preact/compat'

/**
 * Représente un champs, dans le contexte du formulaire
 *
 * @param {string} type
 * @param {string} name
 * @param {function} onInput
 * @param {string} value
 * @param {string} error
 * @param {boolean} autofocus
 * @param {function} component
 * @param {React.Children} children
 * @param {string} className
 * @param {string} wrapperClass
 * @param props
 */
export function Field ({
  name,
  onInput,
  value,
  error,
  children,
  type = 'text',
  className = '',
  wrapperClass = '',
  component = null,
  ...props
}) {
  // Hooks
  const [dirty, setDirty] = useState(false)
  const ref = useRef(null)
  useAutofocus(ref, props.autofocus)
  const showError = error && !dirty

  function handleInput (e) {
    if (dirty === false) {
      setDirty(true)
    }
    if (onInput) {
      onInput(e)
    }
  }

  // Si le champs a une erreur et n'a pas été modifié
  if (showError) {
    className += ' is-invalid'
  }

  // Les attributs à passer aux champs
  const attr = {
    name,
    id: name,
    className,
    onInput: handleInput,
    type,
    ...(value === undefined ? {} : { value }),
    ...props
  }

  // On trouve le composant à utiliser
  const FieldComponent = useMemo(() => {
    if (component) {
      return component
    }
    switch (type) {
      case 'textarea':
        return FieldTextarea
      case 'editor':
        return FieldEditor
      default:
        return FieldInput
    }
  }, [component, type])

  // Si l'erreur change on considère le champs comme "clean"
  useLayoutEffect(() => {
    setDirty(false)
  }, [error])

  return (
    <div className={`form-group ${wrapperClass}`} ref={ref}>
      {children && <label htmlFor={name}>{children}</label>}
      <FieldComponent {...attr} />
      {showError && <div className='invalid-feedback'>{error}</div>}
    </div>
  )
}

/**
 * Bouton radio avec un label sur le côté
 */
export function Radio ({ children, ...props }) {
  return (
    <Flex center gap={1}>
      <span class={classNames('form-radio', props.checked && 'is-checked')}>
        <input type='radio' {...props} />
      </span>
      <label htmlFor={props.id} class='flex'>
        {children}
      </label>
    </Flex>
  )
}

/**
 * Bouton checkbox avec un label sur le côté
 */
export function Checkbox ({ children, ...props }) {
  return (
    <Flex center gap={1}>
      <span class={classNames('form-checkbox', props.checked && 'is-checked')}>
        <input type='checkbox' {...props} />
      </span>
      <label htmlFor={props.id} class='flex'>
        {children}
      </label>
    </Flex>
  )
}

function FieldTextarea (props) {
  return <textarea {...props} />
}

function FieldInput (props) {
  return <input {...props} />
}

function FieldEditor (props) {
  const ref = useRef(null)
  useEffect(() => {
    if (ref.current) {
      ref.current.syncEditor()
    }
  }, [props.value])
  return <textarea {...props} is='markdown-editor' ref={ref} />
}

/**
 * Version contextualisée des champs pour le formulaire
 */

export const FormContext = createContext({
  errors: {},
  loading: false,
  emptyError: () => {}
})

/**
 * Formulaire Ajax
 *
 * @param {object} value Donnée à transmettre au serveur et aux champs
 * @param onChange
 * @param className
 * @param children
 * @param {string} action URL de l'action à appeler pour traiter le formulaire
 * @param {object} data Données à envoyer à l'API et à fusionner avec les données du formulaire
 * @param {string} method Méthode d'envoie des données
 * @param {bool} floatingAlert
 * @param onSuccess Fonction appelée en cas de retour valide de l'API (reçoit les données de l'API en paramètre)
 */
export function FetchForm ({
  data = {},
  children,
  action,
  className,
  method = 'POST',
  onSuccess = () => {},
  floatingAlert = false
}) {
  const [{ loading, errors }, setState] = useState({
    loading: false,
    errors: []
  })
  const mainError = errors.main || null

  // Vide l'erreur associée à un champs donnée
  const emptyError = name => {
    if (!errors[name]) return null
    const newErrors = { ...errors }
    delete newErrors[name]
    setState(s => ({ ...s, errors: newErrors }))
  }

  // On soumet le formulaire au travers d'une requête Ajax
  const handleSubmit = async e => {
    e.preventDefault()
    setState({ loading: true, errors: [] })
    const form = e.target
    const formData = { ...data, ...Object.fromEntries(new FormData(form)) }
    try {
      const response = await jsonFetch(action, { method, body: formData })
      onSuccess(response)
      form.reset()
    } catch (e) {
      if (e instanceof ApiError) {
        setState(s => ({ ...s, errors: e.violations }))
      } else if (e.detail) {
        flash(e.detail, 'danger', null)
      } else {
        flash(e, 'danger', null)
        throw e
      }
    }
    setState(s => ({ ...s, loading: false }))
  }

  return (
    <FormContext.Provider value={{ loading, errors, emptyError }}>
      <form onSubmit={handleSubmit} className={className}>
        {mainError && (
          <alert-message
            type='danger'
            onClose={() => emptyError('main')}
            className={floatingAlert ? 'is-floating' : 'full'}
          >
            {mainError}
          </alert-message>
        )}
        {children}
      </form>
    </FormContext.Provider>
  )
}

/**
 * Représente un champs, dans le contexte du formulaire
 *
 * @param {string} type
 * @param {string} name
 * @param {React.Children} children
 * @param {object} props
 */
export function FormField ({ type = 'text', name, children, ...props }) {
  const { errors, emptyError, loading } = useContext(FormContext)
  const error = errors[name] || null
  return (
    <Field type={type} name={name} error={error} onInput={() => emptyError(name)} readonly={loading} {...props}>
      {children}
    </Field>
  )
}

/**
 * Représente un bouton, dans le contexte du formulaire
 *
 * @param children
 * @param props
 * @return {*}
 * @constructor
 */
export function FormPrimaryButton ({ children, ...props }) {
  const { loading, errors } = useContext(FormContext)
  const disabled = loading || Object.keys(errors).filter(k => k !== 'main').length > 0

  return (
    <PrimaryButton loading={loading} disabled={disabled} {...props}>
      {children}
    </PrimaryButton>
  )
}

/**
 * Représente un bouton, dans le contexte du formulaire
 *
 * @param children
 * @param props
 * @return {*}
 * @constructor
 */
export function FormSecondaryButton ({ children, ...props }) {
  const { loading, errors } = useContext(FormContext)
  const disabled = loading || Object.keys(errors).filter(k => k !== 'main').length > 0

  return (
    <SecondaryButton loading={loading} disabled={disabled} {...props}>
      {children}
    </SecondaryButton>
  )
}

/**
 * Représente un bouton, dans le contexte du formulaire
 *
 * @param children
 * @param props
 * @return {*}
 * @constructor
 */
export function FormButton ({ children, ...props }) {
  const { loading, errors } = useContext(FormContext)
  const disabled = loading || Object.keys(errors).filter(k => k !== 'main').length > 0

  return (
    <Button loading={loading} disabled={disabled} {...props}>
      {children}
    </Button>
  )
}
