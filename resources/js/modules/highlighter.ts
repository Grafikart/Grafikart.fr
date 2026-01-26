/**
 * Bind syntax highlighting on code elements
 */
export async function bindSyntaxHighlighting(root = document) {
    const elements = Array.from(
        root.querySelectorAll<HTMLElement>('pre code'),
    ).filter((v) => !v.classList.contains('language-mermaid'));
    console.log(elements);
    if (elements.length === 0) {
        return;
    }
    const codeToHtml = await import('https://esm.sh/shiki@3.0.0').then(
        (m) => m.codeToHtml,
    );

    for (const code of elements) {
        const pre = code.parentElement as HTMLPreElement;
        pre.outerHTML = await codeToHtml(code.innerText, {
            lang: getLangFromClass(code.getAttribute('class') ?? ''),
            theme: 'tokyo-night',
        });
    }
}

function getLangFromClass(cls: string = '') {
    const lang = cls.replace('language-', '');
    if (!lang) {
        return 'bash';
    }
    if (lang === 'tsx' || lang === 'jsx') {
        return 'javascript';
    }
    return lang;
}
