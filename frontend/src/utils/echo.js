import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios';

window.Pusher = Pusher;

const reverbPort = import.meta.env.VITE_REVERB_PORT || 8080;
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';

const echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
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

export default echo;
