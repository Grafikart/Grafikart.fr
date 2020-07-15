export function Alert ({ type = 'success', children, duration }) {
  return (
    <alert-message type={type} className='full' duration={duration}>
      {children}
    </alert-message>
  )
}
