import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { copyFileSync, readFileSync, unlinkSync, writeFileSync } from 'node:fs';
import { defineConfig, type Plugin } from 'vite';

const inputs = {
    front: ['resources/js/front.ts'],
    cms: ['resources/js/cms.tsx'],
};

const entry = process.env.BUILD_ENTRY as keyof typeof inputs;

function manifestMerge(): Plugin {
    return {
        name: 'manifest-merge',
        apply: 'build',
        closeBundle() {
            if (!entry) {
                return;
            }

            const dir = 'public/build';
            const manifestPath = `${dir}/manifest.json`;
            const manifestTempPath = `${dir}/manifesttmp.json`;

            // Build the final manifest combining the previous one
            if (entry === 'cms') {
                writeFileSync(
                    manifestPath,
                    JSON.stringify(
                        {
                            ...JSON.parse(
                                readFileSync(manifestTempPath, 'utf-8'),
                            ),
                            ...JSON.parse(readFileSync(manifestPath, 'utf-8')),
                        },
                        null,
                        2,
                    ),
                );
                unlinkSync(manifestTempPath);
            } else {
                copyFileSync(manifestPath, manifestTempPath);
            }
        },
    };
}

export default defineConfig({
    plugins: [
        laravel({
            input: entry ? inputs[entry] : [...inputs.front, ...inputs.cms],
            refresh: false,
        }),
        react({
            babel: {
                plugins: ['babel-plugin-react-compiler'],
            },
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
        manifestMerge(),
    ],
    build: {
        emptyOutDir: entry === 'front',
        sourcemap: true,
        rolldownOptions: {
            output: {
                advancedChunks: {
                    groups: [
                        {
                            name: 'vendor',
                            test: /node_modules\/(react|react-dom|tailwind|@tanstack|@base-ui|sonner)/,
                        },
                    ],
                },
            },
        },
    },
});
