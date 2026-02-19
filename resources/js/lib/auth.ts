export function isAuthenticated(): boolean {
  return document.body.hasAttribute("data-user")
}

export function userId(): number | null {
  const userId = document.body.getAttribute('data-user')
  if (!userId) {
    return null;
  }
  return parseInt(userId, 10);
}

export function isPremium(): boolean {
  return document.body.hasAttribute("data-premium")
}
