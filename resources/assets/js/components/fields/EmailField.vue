<template>
    <async-validation-field
        field_name="email"
        label="E-Mail Address"
        v-model="value"
        :error="error"
        validation_url="/api/1/users/email_exists"
        maxlength="255"
        @error="emitError"
        @input="emitInput"
    >
    </async-validation-field>
</template>

<script>
import AsyncValidationField from './AsyncValidationField.vue';

const EmailField = {
    name: 'email-field',
    components: {
        'async-validation-field': AsyncValidationField
    },
    props: {
        error: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            value: ''
        };
    },
    methods: {
        emitError(error) {
            this.$emit('error', error)
        },
        emitInput(value) {
            this.$emit('input', value);
        }
    }
};

export default EmailField;
</script>
