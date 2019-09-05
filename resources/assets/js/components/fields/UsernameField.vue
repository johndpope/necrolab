<template>
    <async-validation-field
        field_name="username"
        label="Username"
        v-model="value"
        :error="error"
        validation_url="/api/1/users/username_exists"
        help_text="This is the name people will see you as on NecroLab. You can always change it later."
        maxlength="25"
        @error="emitError"
        @input="emitInput"
    >
    </async-validation-field>
</template>

<script>
import AsyncValidationField from './AsyncValidationField.vue';

const UsernameField = {
    name: 'username-field',
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

export default UsernameField;
</script>
