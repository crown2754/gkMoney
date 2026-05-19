import './bootstrap';

import Alpine from 'alpinejs';

const THEME_STORAGE_KEY = 'gk-theme';

function getSystemTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function getStoredTheme() {
    const stored = localStorage.getItem(THEME_STORAGE_KEY);

    return stored === 'light' || stored === 'dark' ? stored : null;
}

function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
}

function resolveTheme() {
    return getStoredTheme() ?? getSystemTheme();
}

applyTheme(resolveTheme());

document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        current: resolveTheme(),

        set(theme) {
            if (theme !== 'light' && theme !== 'dark') {
                return;
            }

            this.current = theme;
            localStorage.setItem(THEME_STORAGE_KEY, theme);
            applyTheme(theme);
        },

        toggle() {
            this.set(this.current === 'dark' ? 'light' : 'dark');
        },
    });
});

window.Alpine = Alpine;

Alpine.start();
