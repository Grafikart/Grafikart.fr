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

const langs = [
    'apache',
    'astro',
    'awk',
    'bash',
    'blade',
    'c',
    'cmd',
    'cpp',
    'crystal',
    'csharp',
    'css',
    'csv',
    'dart',
    'diff',
    'docker',
    'dockerfile',
    'dotenv',
    'edge',
    'elixir',
    'erb',
    'erlang',
    'fish',
    'git-commit',
    'git-rebase',
    'glsl',
    'go',
    'graphql',
    'haml',
    'handlebars',
    'html',
    'http',
    'ini',
    'javascript',
    'js',
    'json',
    'jsx',
    'kotlin',
    'log',
    'lua',
    'make',
    'makefile',
    'markdown',
    'md',
    'mdx',
    'mermaid',
    'mjs',
    'nginx',
    'objective-c',
    'php',
    'po',
    'pot',
    'potx',
    'powershell',
    'py',
    'python',
    'regex',
    'regexp',
    'rs',
    'ruby',
    'rust',
    'sass',
    'scss',
    'sh',
    'shell',
    'sql',
    'ssh-config',
    'svelte',
    'swift',
    'systemd',
    'toml',
    'ts',
    'tsx',
    'twig',
    'typescript',
    'vim',
    'vue',
    'wasm',
    'wiki',
    'xml',
    'yaml',
    'yml',
    'zig',
    'zsh',
];

const loadedLanguages = new Set();

/**
 * Load a new highlighter language using a CDN
 */
async function loadLanguage(lang: string, code: string): Promise<string> {
    // Resolve the right language to use
    if (!lang) {
        lang = flourite(code, { shiki: true }).language;
    }
    if (!langs.includes(lang)) {
        lang = 'bash';
    }

    if (!loadedLanguages.has(lang)) {
        // Load the language asynchronously
        const url = `https://esm.sh/@shikijs/langs/${lang}.mjs`;
        await highlighter.loadLanguage(import(url));
        loadedLanguages.add(lang);
    }

    return lang;
}
