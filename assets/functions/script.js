const loadedScripts = [] // Cache les scripts qui ont déjà été chargées sur la page

/**
 * Charge un script de manière asynchrone
 *
 * @param {string|array<string>} url
 * @param {string|null} globalName Nom de la variable générée par le script
 * @return {Promise}
 */
export function importScript (url, globalName = null) {
  if (Array.isArray(url)) {
    return Promise.all(url.map(item => importScript(item)))
  }

  return new Promise((resolve, reject) => {
    if (loadedScripts.includes(url)) {
      resolve(globalName ? window[globalName] : null)
      return
    }
    const t = document.getElementsByTagName('script')[0]
    const script = document.createElement('script')

    script.type = 'text/javascript'
    script.src = url
    script.async = true
    script.onload = script.onreadystatechange = function () {
      if (!loadedScripts.includes(url) && (!this.readyState || this.readyState === 'complete')) {
        loadedScripts.push(url)
        resolve(globalName ? window[globalName] : null)
      }
    }
    script.onerror = script.onabort = reject
    t.parentNode.insertBefore(script, t)
  })
}
