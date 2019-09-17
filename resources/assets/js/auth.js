
/* ---------- Load the bootstrapper ---------- */

require('./bootstrap');


/* ---------- Register Vue with the window object ---------- */

window.Vue = require('vue');


/* ---------- Register Components ---------- */

import RegisterForm from './components/auth/RegisterForm.vue';
import VerifyForm from './components/auth/VerifyForm.vue';
import LoginForm from './components/auth/LoginForm.vue';

/* ---------- Initialize Vue ---------- */

const app = new Vue({
    el: '#app',
    components: {
        'register-form': RegisterForm,
        'verify-form': VerifyForm,
        'login-form': LoginForm
    }
});
