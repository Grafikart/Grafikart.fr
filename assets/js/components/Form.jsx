import {createContext} from 'preact'
import {jsonFetch} from '@fn/api'
import {useEffect, useRef, useState} from 'preact/hooks'

export const FormContext = createContext({
  data: {},
  errors: {},
  loading: false,
  setValue: (name, value) => {},
  emptyError: (name) => {},
})

/**
 * Hook permettant de gérer le traitement Ajax d'un formulaire
 *
 * @param {string[]} method
 * @param {string} url
 * @return {array}
 */
function useForm (method, url, value, onSuccess) {
  const [errors, setErrors] = useState({})
  const [loading, setLoading] = useState(false)
  const emptyError = (name) => {
    delete errors[name]
    setErrors(errors)
  }
  const onSubmit = async e => {
    e.preventDefault()
    setLoading(true)
    try {
      const data = await jsonFetch(url, {method, body: JSON.stringify(value)})
      onSuccess(data)
    } catch (e) {
      if (e.violations) {
        setErrors(
          e.violations.reduce((acc, {propertyPath, message}) => {
            if (propertyPath === '') {
              propertyPath = 'main'
            }
            acc[propertyPath] = message
            return acc
          }, {})
        )
      } else {
        throw e
      }
    }
    setLoading(false)
  }

  return [
    errors, // Object contenant les erreurs {[champ]: message}
    loading, // Booleen représentant l'état du chargement
    onSubmit, // Evènement à lancer pour déclencher une soumission du formulaire
    emptyError // Fonction permettant de supprimer une erreur emptyError(champ)
  ]
}

/**
 * Formulaire Ajax
 *
 * @param {object} value Donnée à transmetter au serveur et aux champs
 * @param onChange
 * @param className
 * @param children
 * @param action
 * @param method
 * @param onSuccess Fonction appelée en cas de retour valide de l'API (reçoit les données de l'API en paramètre)
 */
export function FetchForm ({value, onChange, className, children, action, method, onSuccess}) {
  const setValue = (name, newValue) => onChange({...value, [name]: newValue})
  const [errors, loading, onSubmit, emptyError] = useForm(method, action, value, onSuccess)
  const contextData = { data: value, errors, loading, setValue, emptyError}
  const mainError = errors['main'] || null

  return <FormContext.Provider value={contextData}>
    <form onSubmit={onSubmit} className={className}>
      {mainError && <alert-message type="error" duration="4" onClose={() => emptyError('main')}>{mainError}</alert-message>}
      {children}
    </form>
  </FormContext.Provider>
}

/**
 * Représente un champs, dans le contexte du formulaire
 *
 * @param {string} type
 * @param {string} name
 * @return {*}
 * @constructor
 */
export function FormField ({type, name, children, ...props}) {

  return <FormContext.Consumer>
    {({data, setValue, errors, emptyError, loading}) => {
      const value = data[name] || null
      const error = errors[name] || null
      const onInput = function (e) {
        if (error) {
          emptyError(name)
        }
        setValue(name, e.target.value)
      }

      return <Field type={type} name={name} value={value} error={error} onInput={onInput} readonly={loading} {...props}>{children}</Field>
    }}
  </FormContext.Consumer>
}

/**
 * Représente un champs, dans le contexte du formulaire
 *
 * @param type
 * @param name
 * @param onInput
 * @param value
 * @param error
 * @param props
 * @return {*}
 * @constructor
 */
export function Field ({type, name, onInput, value, error, children, ...props}) {
  const inputClass = error ? 'is-invalid' : null
  const inputRef = useRef(null)

  useEffect(function () {
    if (props.autofocus && inputRef.current) {
      inputRef.current.focus()
    }
  }, [props.autofocus])


  return <div className="form-group">
    <label htmlFor={name}>{children}</label>
    <textarea name={name} id={name} is="textarea-autogrow"
              onInput={onInput}
              className={inputClass}
              ref={inputRef}
              {...props}
    >{value}</textarea>
    {error && <div className="invalid-feedback">{error}</div>}
  </div>
}

/**
 * Représente un bouton, dans le contexte du formulaire
 *
 * @param children
 * @param props
 * @return {*}
 * @constructor
 */
export function PrimaryButton ({children, ...props}) {
  return <FormContext.Consumer>
    {({loading, errors}) => {

      const disabled = loading || Object.keys(errors).filter(k => k !== 'main').length > 0

      return <button className="btn btn-primary" disabled={disabled} {...props}>
        {loading && <Loader className="icon" />}
        {children}
      </button>
    }}
  </FormContext.Consumer>
}

/**
 * Loader
 *
 * @constructor
 */
export function Loader ({...props}) {
  return <spinning-dots {...props}></spinning-dots>
}
