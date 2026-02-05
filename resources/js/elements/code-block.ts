import flourite from 'flourite';
import { createCssVariablesTheme, createHighlighterCore } from 'shiki/core';
import { createOnigurumaEngine } from 'shiki/engine/oniguruma';

import { LazyComponent } from '@/lib/custom-element.ts';

const cssTheme = createCssVariablesTheme({
    name: 'css-variables',
    variablePrefix: '--shiki-',
    variableDefaults: {},
    fontStyle: true,
});

const highlighter = await createHighlighterCore({
    themes: [cssTheme],
    langs: [],
    engine: createOnigurumaEngine(import('shiki/wasm')),
});

export default class CodeBlock extends LazyComponent {
    async onMount() {
        const code = this.el.textContent;
        const lang = await loadLanguage(
            this.el.getAttribute('lang') ?? '',
            code,
        );
        this.el.outerHTML = highlighter.codeToHtml(code, {
            lang: lang,
            theme: cssTheme,
        });
    }
}

const langs = {
    apache: () => import('@shikijs/langs/apache'),
    astro: () => import('@shikijs/langs/astro'),
    awk: () => import('@shikijs/langs/awk'),
    bash: () => import('@shikijs/langs/bash'),
    blade: () => import('@shikijs/langs/blade'),
    c: () => import('@shikijs/langs/c'),
    cmd: () => import('@shikijs/langs/cmd'),
    cpp: () => import('@shikijs/langs/cpp'),
    crystal: () => import('@shikijs/langs/crystal'),
    csharp: () => import('@shikijs/langs/csharp'),
    css: () => import('@shikijs/langs/css'),
    csv: () => import('@shikijs/langs/csv'),
    dart: () => import('@shikijs/langs/dart'),
    diff: () => import('@shikijs/langs/diff'),
    docker: () => import('@shikijs/langs/docker'),
    dockerfile: () => import('@shikijs/langs/dockerfile'),
    dotenv: () => import('@shikijs/langs/dotenv'),
    edge: () => import('@shikijs/langs/edge'),
    elixir: () => import('@shikijs/langs/elixir'),
    erb: () => import('@shikijs/langs/erb'),
    erlang: () => import('@shikijs/langs/erlang'),
    fish: () => import('@shikijs/langs/fish'),
    'git-commit': () => import('@shikijs/langs/git-commit'),
    'git-rebase': () => import('@shikijs/langs/git-rebase'),
    glsl: () => import('@shikijs/langs/glsl'),
    go: () => import('@shikijs/langs/go'),
    graphql: () => import('@shikijs/langs/graphql'),
    haml: () => import('@shikijs/langs/haml'),
    handlebars: () => import('@shikijs/langs/handlebars'),
    html: () => import('@shikijs/langs/html'),
    http: () => import('@shikijs/langs/http'),
    ini: () => import('@shikijs/langs/ini'),
    javascript: () => import('@shikijs/langs/javascript'),
    js: () => import('@shikijs/langs/js'),
    json: () => import('@shikijs/langs/json'),
    jsx: () => import('@shikijs/langs/jsx'),
    kotlin: () => import('@shikijs/langs/kotlin'),
    log: () => import('@shikijs/langs/log'),
    lua: () => import('@shikijs/langs/lua'),
    make: () => import('@shikijs/langs/make'),
    makefile: () => import('@shikijs/langs/makefile'),
    markdown: () => import('@shikijs/langs/markdown'),
    md: () => import('@shikijs/langs/md'),
    mdx: () => import('@shikijs/langs/mdx'),
    mermaid: () => import('@shikijs/langs/mermaid'),
    mjs: () => import('@shikijs/langs/mjs'),
    nginx: () => import('@shikijs/langs/nginx'),
    'objective-c': () => import('@shikijs/langs/objective-c'),
    php: () => import('@shikijs/langs/php'),
    po: () => import('@shikijs/langs/po'),
    pot: () => import('@shikijs/langs/pot'),
    potx: () => import('@shikijs/langs/potx'),
    powershell: () => import('@shikijs/langs/powershell'),
    py: () => import('@shikijs/langs/py'),
    python: () => import('@shikijs/langs/python'),
    regex: () => import('@shikijs/langs/regex'),
    regexp: () => import('@shikijs/langs/regexp'),
    rs: () => import('@shikijs/langs/rs'),
    ruby: () => import('@shikijs/langs/ruby'),
    rust: () => import('@shikijs/langs/rust'),
    sass: () => import('@shikijs/langs/sass'),
    scss: () => import('@shikijs/langs/scss'),
    sh: () => import('@shikijs/langs/sh'),
    shell: () => import('@shikijs/langs/shell'),
    sql: () => import('@shikijs/langs/sql'),
    'ssh-config': () => import('@shikijs/langs/ssh-config'),
    svelte: () => import('@shikijs/langs/svelte'),
    swift: () => import('@shikijs/langs/swift'),
    systemd: () => import('@shikijs/langs/systemd'),
    toml: () => import('@shikijs/langs/toml'),
    ts: () => import('@shikijs/langs/ts'),
    tsx: () => import('@shikijs/langs/tsx'),
    twig: () => import('@shikijs/langs/twig'),
    typescript: () => import('@shikijs/langs/typescript'),
    vim: () => import('@shikijs/langs/vim'),
    vue: () => import('@shikijs/langs/vue'),
    wasm: () => import('@shikijs/langs/wasm'),
    wiki: () => import('@shikijs/langs/wiki'),
    xml: () => import('@shikijs/langs/xml'),
    yaml: () => import('@shikijs/langs/yaml'),
    yml: () => import('@shikijs/langs/yml'),
    zig: () => import('@shikijs/langs/zig'),
    zsh: () => import('@shikijs/langs/zsh'),
};

const loadedLanguages = new Set();

/**
 * Load a new highlighter language using a CDN
 */
async function loadLanguage(lang: string, code: string): Promise<string> {
    // Resolve the right language to use
    if (!lang) {
        lang = flourite(code, { shiki: true }).language;
    }
    if (!(lang in langs)) {
        lang = 'bash';
    }

    if (!loadedLanguages.has(lang)) {
        // Load the language asynchronously
        // @ts-expect-error we know the module
        const module = await langs[lang]();
        await highlighter.loadLanguage(module.default);
        loadedLanguages.add(module.default);
    }

    return lang;
}
