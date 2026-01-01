export function adminPath (s: string) {
  return '/' + window.location.pathname.split('/')[1] + s
}
