<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="front">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="view-transition" content="same-origin">
    <link rel="alternate" type="application/rss+xml" title="Grafikart.fr | Flux" href="{{ url('rss') }}"/>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" sizes="128x128" href="/favicons/icon-128x128.png">
    <title>@yield('title') | {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=crimson-pro:700|inter:400,600,700" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        :root {
            color-scheme: light dark;
            --title: light-dark(#121c42, #f0f3ff);
            --text: light-dark(
                color-mix(in oklab, var(--title) 85%, transparent),
                #adbef3
            );
            --muted: light-dark(
                color-mix(in oklab, var(--title) 60%, transparent),
                #8491c7
            );
            --primary: light-dark(#4869ee, #5396e7);
            --primary-foreground: #effbec;
            --background: light-dark(#f7fafb, #171933);
            --background-light: light-dark(#fff, #1b1e3d);
            --background-dark: light-dark(#e6eff4, #1b1e3d);
            --card: light-dark(#fff, #22244d);
            --white: #fff;
            --card-footer: color-mix(in oklab, var(--background) 50%, transparent);
            --border: light-dark(#d5e3ec, #2a2e5e);
            --destructive: #f25353;
            --destructive-foreground: #fff3f3;
            --green: light-dark(#2aa75e, #41cf7c);
            --yellow: light-dark(#f49f01, #feb32b);
            --edge: light-dark(#acb8c0, #595e9d);
            --overlay: #000000b3;

            --success-bg: light-dark(hsl(143, 85%, 96%), hsl(150, 100%, 6%));
            --success-border: light-dark(hsl(145, 92%, 87%), hsl(147, 100%, 12%));
            --success-text: light-dark(hsl(140, 100%, 27%), hsl(150, 86%, 65%));
            --info-bg: light-dark(hsl(208, 100%, 97%), hsl(215, 100%, 6%));
            --info-border: light-dark(hsl(221, 91%, 93%), hsl(223, 43%, 17%));
            --info-text: light-dark(hsl(210, 92%, 45%), hsl(216, 87%, 65%));
            --warning-bg: light-dark(hsl(49, 100%, 97%), hsl(64, 100%, 6%));
            --warning-border: light-dark(hsl(49, 91%, 84%), hsl(60, 100%, 9%));
            --warning-text: light-dark(hsl(31, 92%, 45%), hsl(46, 87%, 65%));
            --error-bg: light-dark(hsl(359, 100%, 97%), hsl(358, 76%, 10%));
            --error-border: light-dark(hsl(359, 100%, 94%), hsl(357, 89%, 16%));
            --error-text: light-dark(hsl(360, 100%, 45%), hsl(358, 100%, 81%));
            --video: #121c42;
            --sidebar: light-dark(#fbfcfd, #1e1f3f);
            --list-hover: color-mix(in srgb, var(--primary) 8%, transparent);
        }
        @theme inline {
            --container-*: initial;
            --font-sans: "Inter", sans-serif;
            --font-serif: "Crimson Pro", serif;
            --font-mono: monospace;

            --color-*: initial;
            --color-foreground-title: var(--title);
            --color-foreground: var(--text);
            --color-muted: var(--muted);
            --color-muted-foreground: var(--muted);
            --color-primary: var(--primary);
            --color-ring: var(--primary);
            --color-primary-foreground: var(--primary-foreground);
            --color-background: var(--background);
            --color-background-light: var(--background-light);
            --color-background-dark: var(--background-dark);
            --color-card: var(--card);
            --color-popover: var(--card);
            --color-white: #f7fafb;
            --color-card-footer: var(--card-footer);
            --color-border: var(--border);
            --color-secondary: var(--background);
            --color-secondary-foreground: var(--muted);
            --color-destructive: var(--destructive);
            --color-destructive-foreground: var(--destructive-foreground);
            --color-success: var(--green);
            --color-warning: var(--yellow);
            --color-edge: var(--edge);
            --color-overlay: var(--overlay);

            --color-success-bg: var(--success-bg);
            --color-success-border: var(--success-border);
            --color-success-text: var(--success-text);
            --color-info-bg: var(--info-bg);
            --color-info-border: var(--info-border);
            --color-info-text: var(--info-text);
            --color-warning-bg: var(--warning-bg);
            --color-warning-border: var(--warning-border);
            --color-warning-text: var(--warning-text);
            --color-error-bg: var(--error-bg);
            --color-error-border: var(--error-border);
            --color-error-text: var(--error-text);
            --color-video: var(--video);
            --color-sidebar: var(--sidebar);
            --color-list-hover: var(--list-hover);

            --shadow-focus: 0 0 0 3px
            color-mix(in srgb, var(--color-primary) 25%, transparent);
            --shadow-success: 0 0 0 3px color-mix(in srgb, var(--green) 25%, transparent);
            --shadow-destructive: 0 0 0 3px
            color-mix(in srgb, var(--color-destructive) 25%, transparent);
            --shadow-toast: 0 4px 12px rgba(0, 0, 0, 0.1);
            --shadow-button: 0 1px 2px #24398d66;
            --default-transition-duration: 300ms;
            --header-height: 60px;
            --breakpoint-3xl: 105rem;
        }
        @utility container {
            max-width: unset;
            width: 100%;
            --width: 1330px;
            padding-inline: calc(50% - min(calc(100% - 6rem), var(--width)) * 0.5);
        }

        @layer base {
            html {
                scrollbar-gutter: stable;
            }
            label {
                display: block;
            }
            button,
            a {
                cursor: pointer;
            }
        }

        @utility text-page-title {
            font-size: var(--text-6xl);
            line-height: var(--text-6xl--line-height);
            font-family: var(--font-serif);
            font-weight: 700;
            color: var(--color-foreground-title);
        }
    </style>
</head>
<body
    class="font-sans antialiased text-foreground bg-background min-h-screen flex flex-col">
    @yield('body')
</body>
</html>
