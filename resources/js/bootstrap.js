/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

console.log('[Bootstrap.js] Echo kurulumu başlıyor...');
console.log('[Bootstrap.js] VITE_REVERB_APP_KEY:', import.meta.env.VITE_REVERB_APP_KEY);
console.log('[Bootstrap.js] VITE_REVERB_HOST:', import.meta.env.VITE_REVERB_HOST);
console.log('[Bootstrap.js] VITE_REVERB_PORT:', import.meta.env.VITE_REVERB_PORT);


try {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT,
        wssPort: import.meta.env.VITE_REVERB_PORT,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
    console.log('[Bootstrap.js] Echo kurulumu BAŞARILI. window.Echo:', window.Echo);
} catch (e) {
    console.error('[Bootstrap.js] Echo kurulumu sırasında KRİTİK HATA:', e);
}
