import { useAsyncEffect } from '/functions/hooks.js'
import { jsonFetch } from '/functions/api.js'
import { useState } from 'preact/hooks'

let cachedCountries = null

/**
 * Champs select contenant la liste des pays (avec code pays)
 * @param props comme un select
 * @return {JSX.Element}
 */
export function CountrySelect ({ ...props }) {
  const [countries, setCountries] = useState(null)

  useAsyncEffect(async () => {
    if (cachedCountries) {
      setCountries(cachedCountries)
      return
    }
    const data = await jsonFetch('/api/country')
    cachedCountries = data
    setCountries(data)
  })

  if (countries === null) {
    return <select {...props} />
  }

  return (
    <select {...props}>
      <option defaultSelected>Veuillez sélectionner un pays</option>
      {Object.keys(countries).map(countryCode => (
        <option
          key={countryCode}
          defaultSelected={countryCode === props.defaultValue ? true : undefined}
          value={countryCode}
        >
          {countries[countryCode]}
        </option>
      ))}
    </select>
  )
}
