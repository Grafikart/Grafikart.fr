import { Loader } from './Loader.jsx'

export function PrimaryButton ({ children, ...props }) {
  return (
    <Button className='btn-primary' {...props}>
      {children}
    </Button>
  )
}

export function SecondaryButton ({ children, ...props }) {
  return (
    <Button className='btn-secondary' {...props}>
      {children}
    </Button>
  )
}

/**
 *
 * @param {*} children
 * @param {string} className
 * @param {string} size
 * @param {boolean} loading
 * @param {Object} props
 * @return {*}
 */
export function Button ({ children, className = '', loading = false, size, ...props }) {
  return (
    <button className={`btn ${className} ${size && `btn-${size}`}`} disabled={loading} {...props}>
      {loading && <Loader className='icon' />}
      {children}
    </button>
  )
}

export function RoundedButton ({ children, loading = true, type = '', title = '', ...props }) {
  return (
    <button
      className={`rounded-button ${type}`}
      aria-label={title}
      data-microtip-position='top'
      role='tooltip'
      {...props}
    >
      <span>{loading ? <Loader /> : children}</span>
    </button>
  )
}
