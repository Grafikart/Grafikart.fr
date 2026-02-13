import {
  NativeSelect,
  NativeSelectOption,
} from "@/components/ui/native-select.tsx"

type Props = {
  defaultValue?: number
}

const levels = [
  { value: 0, label: "Junior" },
  { value: 1, label: "Intermédiaire" },
  { value: 2, label: "Senior" },
]

export function LevelSelector(props: Props) {
  return (
    <NativeSelect {...props} name="level">
      {levels.map((level) => (
        <NativeSelectOption key={level.label} value={level.value}>
          {level.label}
        </NativeSelectOption>
      ))}
    </NativeSelect>
  )
}
