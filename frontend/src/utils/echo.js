import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios';

window.Pusher = Pusher;

const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
const reverbHost = import.meta.env.VITE_REVERB_HOST;
const reverbPort = import.meta.env.VITE_REVERB_PORT || 8080;
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';

let echo;

if (reverbKey && reverbHost) {
    echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: reverbHost,
        wsPort: reverbPort,
        wssPort: reverbPort,
        forceTLS: reverbScheme === 'https',
        enabledTransports: reverbScheme === 'https' ? ['ws', 'wss'] : ['ws'],
        authorizer: (channel, options) => {
            return {
                authorize: (socketId, callback) => {
                    axios.post('/broadcasting/auth', {
                        socket_id: socketId,
                        channel_name: channel.name
                    })
                    .then(response => {
                        callback(false, response.data);
                    })
                    .catch(error => {
                        console.error('Channel authorization failed:', error);
                        callback(true, error);
                    });
                }
            };
        },
    });

    // Debug connection
    echo.connector.pusher.connection.bind('connected', () => {
        console.log('Successfully connected to Reverb WebSocket server');
    });

    echo.connector.pusher.connection.bind('disconnected', () => {
        console.warn('Disconnected from Reverb WebSocket server');
    });

    echo.connector.pusher.connection.bind('error', (err) => {
        console.error('Reverb connection error:', err);
    });
} else {
    // If we're in development, we can fallback to localhost, but in production we should warn
    const isProd = import.meta.env.PROD;
    if (isProd) {
        console.error('Echo error: VITE_REVERB_APP_KEY or VITE_REVERB_HOST is missing. Real-time chat will not work in production.');
    }
    
    // Provide a dummy object to prevent errors in other components
    echo = {
        private: () => ({ listen: () => ({ on: () => ({}) }) }),
        leave: () => {},
        leaveAll: () => {},
        connector: { pusher: { connection: { bind: () => {} } } }
    };
}

export default echo;
