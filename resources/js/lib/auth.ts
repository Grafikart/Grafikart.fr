export function isAuthenticated(): boolean {
    return document.body.hasAttribute('data-user');
}

export function isPremium(): boolean {
    return document.body.hasAttribute('data-premium');
}
