import { wayfinder } from "@laravel/vite-plugin-wayfinder"
import tailwindcss from "@tailwindcss/vite"
import react from "@vitejs/plugin-react"
import laravel from "laravel-vite-plugin"
import { copyFileSync, readFileSync, unlinkSync, writeFileSync } from "node:fs"
import { defineConfig, type Plugin, lazyPlugins } from "vite-plus"
import { resolve } from "node:path"

const inputs = {
  front: ["resources/js/front.ts"],
  cms: ["resources/js/cms.tsx"],
}

const entry = process.env.BUILD_ENTRY as keyof typeof inputs

function manifestMerge(): Plugin {
  return {
    name: "manifest-merge",
    apply: "build",
    closeBundle() {
      if (!entry) {
        return
      }

      const manifestPath = resolve("public/build/manifest.json")
      const manifestTempPath = resolve("storage/manifesttmp.json")

      // Build the final manifest combining the previous one
      if (entry === "cms") {
        writeFileSync(
          manifestPath,
          JSON.stringify(
            {
              ...JSON.parse(readFileSync(manifestTempPath, "utf-8")),
              ...JSON.parse(readFileSync(manifestPath, "utf-8")),
            },
            null,
            2,
          ),
        )
        unlinkSync(manifestTempPath)
      } else {
        copyFileSync(manifestPath, manifestTempPath)
      }
    },
  }
}

export default defineConfig({
  fmt: {
    useTabs: false,
    tabWidth: 2,
    printWidth: 80,
    singleQuote: false,
    jsxSingleQuote: false,
    quoteProps: "as-needed",
    trailingComma: "all",
    semi: false,
    arrowParens: "always",
    bracketSameLine: false,
    bracketSpacing: true,
    ignorePatterns: ["resources/views/mail/*", "*.yml", "*.md"],
    overrides: [
      {
        files: ["*.json"],
        options: { tabWidth: 4 },
      },
    ],
  },
  lint: {
    plugins: ["react", "typescript", "unicorn"],
    categories: {},
    env: {
      builtin: true,
      es2018: true,
    },
    rules: {
      "no-children-prop": "off",
      "constructor-super": "error",
      "for-direction": "error",
      "no-async-promise-executor": "error",
      "no-case-declarations": "error",
      "no-class-assign": "error",
      "no-compare-neg-zero": "error",
      "no-cond-assign": "error",
      "no-const-assign": "error",
      "no-constant-binary-expression": "error",
      "no-constant-condition": "error",
      "no-control-regex": "error",
      "no-debugger": "error",
      "no-delete-var": "error",
      "no-dupe-class-members": "error",
      "no-dupe-else-if": "error",
      "no-dupe-keys": "error",
      "no-duplicate-case": "error",
      "no-empty": "error",
      "no-empty-character-class": "error",
      "no-empty-pattern": "error",
      "no-empty-static-block": "error",
      "no-ex-assign": "error",
      "no-extra-boolean-cast": "error",
      "no-fallthrough": "error",
      "no-func-assign": "error",
      "no-global-assign": "error",
      "no-import-assign": "error",
      "no-invalid-regexp": "error",
      "no-irregular-whitespace": "error",
      "no-loss-of-precision": "error",
      "no-misleading-character-class": "error",
      "no-new-native-nonconstructor": "error",
      "no-nonoctal-decimal-escape": "error",
      "no-obj-calls": "error",
      "no-prototype-builtins": "error",
      "no-redeclare": "error",
      "no-regex-spaces": "error",
      "no-self-assign": "error",
      "no-setter-return": "error",
      "no-shadow-restricted-names": "error",
      "no-sparse-arrays": "error",
      "no-this-before-super": "error",
      "no-unsafe-finally": "error",
      "no-unsafe-negation": "error",
      "no-unsafe-optional-chaining": "error",
      "no-unused-labels": "error",
      "no-unused-private-class-members": "error",
      "no-unused-vars": "error",
      "no-useless-backreference": "error",
      "no-useless-catch": "error",
      "no-useless-escape": "error",
      "no-with": "error",
      "require-yield": "error",
      "use-isnan": "error",
      "valid-typeof": "error",
      "react-hooks/rules-of-hooks": "error",
      "react-hooks/exhaustive-deps": "warn",
      "@typescript-eslint/ban-ts-comment": "error",
      "no-array-constructor": "error",
      "@typescript-eslint/no-duplicate-enum-values": "error",
      "@typescript-eslint/no-empty-object-type": "error",
      "@typescript-eslint/no-explicit-any": "error",
      "@typescript-eslint/no-extra-non-null-assertion": "error",
      "@typescript-eslint/no-misused-new": "error",
      "@typescript-eslint/no-namespace": "error",
      "@typescript-eslint/no-non-null-asserted-optional-chain": "error",
      "@typescript-eslint/no-require-imports": "error",
      "@typescript-eslint/no-this-alias": "error",
      "@typescript-eslint/no-unnecessary-type-constraint": "error",
      "@typescript-eslint/no-unsafe-declaration-merging": "error",
      "@typescript-eslint/no-unsafe-function-type": "error",
      "no-unused-expressions": "error",
      "@typescript-eslint/no-wrapper-object-types": "error",
      "@typescript-eslint/prefer-as-const": "error",
      "@typescript-eslint/prefer-namespace-keyword": "error",
      "@typescript-eslint/triple-slash-reference": "error",
      "vite-plus/prefer-vite-plus-imports": "error",
    },
    ignorePatterns: [
      "vendor",
      "node_modules",
      "public",
      "bootstrap/ssr",
      "tailwind.config.js",
      "generated.d.ts",
    ],
    options: {
      typeAware: true,
      typeCheck: true,
    },
    jsPlugins: [
      {
        name: "vite-plus",
        specifier: "vite-plus/oxlint-plugin",
      },
    ],
  },
  plugins: lazyPlugins(() => [
    laravel({
      input: entry ? inputs[entry] : [...inputs.front, ...inputs.cms],
      refresh: false,
    }),
    react({ compiler: true }),
    tailwindcss(),
    wayfinder({
      formVariants: true,
    }),
    manifestMerge(),
  ]),
  build: {
    emptyOutDir: entry === "front",
    sourcemap: false,
    minify: false,
    rolldownOptions: {
      output: {
        codeSplitting: {
          groups: [
            {
              name: "vendor",
              test: /node_modules\/(react|react-dom|tailwind|@tanstack|@base-ui|sonner)/,
            },
          ],
        },
      },
    },
  },
})
