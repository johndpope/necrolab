<template>
    <div>
        <loading
            :active.sync="submitting"
            :can-cancel="false"
            :is-full-page="true"
        >
        </loading>
        <div v-if="resent" class="alert alert-success" role="alert">
            A fresh verification link has been sent to your email address.
        </div>
        <div v-if="form_error.length > 0" class="alert alert-danger" role="alert">
            {{ form_error }}
        </div>
        <p>
            Before proceeding, please check your email for a verification link.
        </p>
        <p>
            If you did not receive this email, please click the Resend button below.
        </p>
        <a
                href="/verify"
                class="btn btn-lg btn-secondary"
                role="button"
                @click.prevent="submit"
        >
            Resend
        </a>
    </div>
</template>

<script>
import Loading from 'vue-loading-overlay';

export default {
    name: 'verify-form',
    components: {
        'loading': Loading,
    },
    data() {
        return {
            submitting: false,
            resent: false,
            form_error: '',
        }
    },
    methods: {
        submit() {
            this.submitting = true;
            this.resent = false;

            axios.post('/email/resend', {})
                .then(response => {
                    this.submitting = false;
                    this.resent = true;
                })
                .catch(error => {
                    this.submitting = false;
                    this.resent = false;

                    this.form_error = 'There was a problem communicating with the server. Please try again.';
                });
        }
    }
};
</script>
