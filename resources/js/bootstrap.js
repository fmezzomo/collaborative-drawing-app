import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import store from './store';
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import './echo';

window.Echo.channel('drawing')
.listen('DrawingEvent', (event) => {
    console.log('Event arrived:', event.tool);
    store.dispatch('updateDrawingData', event);
});