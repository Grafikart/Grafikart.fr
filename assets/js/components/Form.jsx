import {createContext} from 'preact'

export const FormContext = createContext({
  data: {},
  errors: {},
  setValue: (name, value) => {}
})

export function Formart ({value, onChange, children}) {
  const setValue = (name, newValue) => {
    onChange({...value, [name]: newValue})
  }
  const contextData = {
    data: value,
    errors: {},
    setValue
  }

  const onSubmit = e => {
    e.preventDefault()
    console.log(value)
  }

  return <FormContext.Provider value={contextData}>
    <form onSubmit={onSubmit}>
      {children}
    </form>
  </FormContext.Provider>
}

/**
 *
 * @param {string} type
 * @param {string} name
 * @return {*}
 * @constructor
 */
export function Field ({type, name, children}) {
  return <FormContext.Consumer>
    {({data, setValue}) => {
      const value = data[name] || null
      return <div className="form-group">
        <label htmlFor={name}>{children}</label>
        <textarea name={name} id={name} value={value} is="textarea-autogrow" onInput={e => setValue(name, e.target.value)}></textarea>
      </div>
    }}
  </FormContext.Consumer>
}

export function PrimaryButton ({children}) {
  return <button className="btn btn-primary">{children}</button>
}
