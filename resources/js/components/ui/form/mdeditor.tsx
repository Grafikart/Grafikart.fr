type Props = {
  defaultValue: string
  name: string
}
export function MDEditor(props: Props) {
  return (
    <textarea
      name={props.name}
      defaultValue={props.defaultValue}
      className="font-mono w-full field-sizing-content outline-none"
    />
  )
}
