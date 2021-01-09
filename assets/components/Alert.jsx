export function Alert ({ type = 'success', children, duration }) {
  return (
    <alert-message type={type} className='full' duration={type === 'success' ? duration : undefined}>
      {children}
    </alert-message>
  )
}
