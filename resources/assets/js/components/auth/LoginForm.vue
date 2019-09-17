<template>
    <div>
        <loading
            :active.sync="submitting"
            :can-cancel="false"
            :is-full-page="true"
        >
        </loading>
        <div class="form-group">
            <div>
                <label for="email" class="col-form-label">E-Mail Address</label>
            </div>
            <div class="input-group">
                <input
                    id="email"
                    type="email"
                    class="form-control"
                    :class="{ 'is-invalid': email.error.length > 0 }"
                    name="email"
                    v-model="email.value"
                    required
                    autocomplete="off"
                    maxlength="255"
                >
                <span v-if="email.error.length > 0" class="invalid-feedback" role="alert">
                    <strong>{{ email.error }}</strong>
                </span>
            </div>
        </div>
        <div class="form-group">
            <div>
                <label for="password" class="col-form-label">Password</label>
            </div>
            <div class="input-group">
                <input
                    id="password"
                    type="password"
                    class="form-control"
                    :class="{ 'is-invalid': password.error.length > 0 }"
                    name="password"
                    v-model="password.value"
                    required
                    autocomplete="off"
                    maxlength="255"
                >
                <span v-if="password.error.length > 0" class="invalid-feedback" role="alert">
                    <strong>{{ password.error }}</strong>
                </span>
            </div>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="remember"
                    id="remember"
                    v-model="remember.checked"
                >
                <label for="password" class="form-check-label">Remember Me</label>
            </div>
        </div>
        <div class="form-group">
            <a
                href="/login"
                class="btn btn-lg btn-primary btn-block"
                role="button"
                @click.prevent="submit()"
            >
                Login
            </a>
        </div>
        <div class="form-group">
            <a
                href="/register"
                class="btn btn-lg btn-secondary btn-block"
                role="button"
                @click="submitting = true"
            >
                Register
            </a>
        </div>
        <div class="form-group mb-0 text-right">
            <a
                class="btn btn-small btn-link"
                href="/password/reset"
                @click="submitting = true"
            >
                Forgot Your Password?
            </a>
        </div>
    </div>
</template>

<script>
import Loading from 'vue-loading-overlay';

export default {
    name: 'login-form',
    components: {
        'loading': Loading
    },
    data() {
        return {
            authenticated: false,
            submitting: false,
            form_error: '',
            email: {
                value: '',
                error: ''
            },
            password: {
                value: '',
                error: ''
            },
            remember: {
                checked: false,
                error: ''
            }
        }
    },
    methods: {
        setFieldError(error) {
            this[error.field].error = error.error;
        },
        submit() {
            this.form_error = '';

            this.submitting = true;

            const parameters = {
                email: this.email.value,
                password: this.password.value
            };

            if(this.remember.checked) {
                parameters['remember'] = 'on';
            }

            axios.post('/login', parameters)
                .then(response => {
                    window.location.href = '/';
                })
                .catch(error => {
                    if(
                        error.response != null &&
                        error.response['data'] != null &&
                        error.response.data['errors'] != null
                    ) {
                        if(error.response.data.errors['email'] != null) {
                            this.email.error = error.response.data.errors.email[0];
                        }

                        if(error.response.data.errors['password'] != null) {
                            this.password.error = error.response.data.errors.password[0];
                        }

                        if(error.response.data.errors['remember'] != null) {
                            this.remember.error = error.response.data.errors.remember[0];
                        }

                        this.submitting = false;
                    }
                    else {
                        this.submitting = false;

                        this.form_error = 'There was a problem communicating with the server. Please try again.';
                    }
                });
        }
    }
};
</script>
