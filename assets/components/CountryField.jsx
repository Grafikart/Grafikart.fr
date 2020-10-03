import { useAsyncEffect } from '/functions/hooks.js'
import { jsonFetch } from '/functions/api.js'
import { useState } from 'preact/hooks'

let cachedCountries = null

export function CountryField ({ ...props }) {
  console.log(props)

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
      {Object.keys(countries).map(countryCode => (
        <option defaultSelected={countryCode === 'FR' ? true : undefined} value={countryCode}>
          {countries[countryCode]}
        </option>
      ))}
    </select>
  )
}
