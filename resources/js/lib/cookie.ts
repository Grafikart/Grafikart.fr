type CookieOptions = {
  expires?: number
  domain?: string
  path?: string
}

export function cookie(
  name: string,
  value: string | null | undefined = undefined,
  options: CookieOptions = {},
): string | null | undefined {
  // On veut lire le cookie
  if (value === undefined) {
    const cookies = document.cookie.split(";")
    for (const cookie of cookies) {
      const [k, v] = cookie.split("=")
      if (k.trim() === name) {
        return v
      }
    }
    return null
  }

  // On veut écrire le cookie
  let cookieValue: string
  if (value === null) {
    cookieValue = ""
    options.expires = -365
  } else {
    cookieValue = encodeURIComponent(value)
  }
  if (options.expires) {
    const d = new Date()
    d.setDate(d.getDate() + options.expires)
    cookieValue += `; expires=${d.toUTCString()}`
  }
  if (options.domain) {
    cookieValue += `; domain=${options.domain}`
  }
  if (options.path) {
    cookieValue += `; path=${options.path}`
  } else {
    cookieValue += "; path=/"
  }
  document.cookie = `${name}=${cookieValue}`
}
