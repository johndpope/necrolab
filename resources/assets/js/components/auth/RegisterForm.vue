<template>
    <div>
        <loading
            :active.sync="submitting"
            :can-cancel="false"
            :is-full-page="true"
        >
        </loading>
        <div class="text-left">
            <div v-if="form_error.length > 0" class="alert alert-danger">
                {{ form_error }}
            </div>
            <username-field
                v-model="username.value"
                :error="username.error"
                @error="setFieldError"
                @keyup.enter.native="submit"
            >
            </username-field>
            <email-field
                v-model="email.value"
                :error="email.error"
                @error="setFieldError"
                @keyup.enter.native="submit"
            >
            </email-field>
            <password-field
                v-model="password.value"
                :error="password.error"
                @error="setFieldError"
                @keyup.enter.native="submit"
            >
            </password-field>
        </div>
        <a
            href="/register"
            class="btn btn-lg btn-primary btn-block"
            role="button"
            @click.prevent="submit"
        >
            Register
        </a>
    </div>
</template>

<script>
import Loading from 'vue-loading-overlay';
import UsernameField from '../fields/UsernameField.vue';
import EmailField from '../fields/EmailField.vue';
import PasswordField from '../fields/PasswordField.vue';

export default {
    name: 'register-form',
    components: {
        'loading': Loading,
        'username-field': UsernameField,
        'email-field': EmailField,
        'password-field': PasswordField
    },
    data() {
        return {
            registered: false,
            submitting: false,
            form_error: '',
            username: {
                value: '',
                error: ''
            },
            email: {
                value: '',
                error: ''
            },
            password: {
                value: '',
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
            this.username.error = '';
            this.email.error = '';
            this.password.error = '';

            this.submitting = true;

            axios.post('/register', {
                username: this.username.value,
                email: this.email.value,
                password: this.password.value,
                password_confirmation: this.password.value
            })
                .then(response => {
                    window.location.href = '/email/verify';
                })
                .catch(error => {
                    if(
                        error.response != null &&
                        error.response['data'] != null &&
                        error.response.data['errors'] != null
                    ) {
                        if(error.response.data.errors['username'] != null) {
                            this.username.error = error.response.data.errors.username[0];
                        }

                        if(error.response.data.errors['email'] != null) {
                            this.email.error = error.response.data.errors.email[0];
                        }

                        if(error.response.data.errors['password'] != null) {
                            this.password.error = error.response.data.errors.password[0];
                        }

                        this.submitting = false;
                    }
                    else {
                        this.form_error = 'There was a problem communicating with the server. Please try again.';
                    }
                });
        }
    }
};
</script>
