import scriptjs from 'scriptjs'
import { useEffect, useRef } from 'preact/hooks'

export function AddressField ({ onChange, onInput, ...props }) {
  const input = useRef(null)

  useEffect(() => {
    let placesAutocomplete = null
    scriptjs('https://cdn.jsdelivr.net/npm/places.js@1.19.0', () => {
      placesAutocomplete = window.places({
        appId: 'plAFU47KNT9R',
        apiKey: '82df8d2dee045f2337d35a00c7a868af',
        container: input.current,
        type: 'address'
      })
      placesAutocomplete.on('change', e => {
        onChange({
          country: e.suggestion.country,
          countryCode: e.suggestion.countryCode,
          city: e.suggestion.city,
          address: e.suggestion.name,
          postalCode: e.suggestion.postcode
        })
      })
    })
    return () => {
      if (placesAutocomplete) {
        placesAutocomplete.destroy()
      }
    }
  }, [onChange])

  return <input type='text' ref={input} {...props} />
}
