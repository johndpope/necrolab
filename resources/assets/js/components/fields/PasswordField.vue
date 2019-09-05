<template>
    <async-validation-field
        :input_type="fieldType"
        field_name="password"
        label="Password"
        v-model="value"
        :error="error"
        validation_url="/api/1/user/password_valid"
        help_text="Your password must be at least 10 characters and contain at least one number, one upper case letter, one lower case letter, and one special character."
        maxlength="255"
        @error="emitError"
        @input="emitInput"
    >
        <template slot="field-append">
            <div class="input-group-append">
                <a href="" class="input-group-text" id="password_append" @click.prevent="visible = !visible">
                    <i v-if="visible" class="fa fa-eye-slash" aria-hidden="true"></i>
                    <i v-if="!visible" class="fa fa-eye" aria-hidden="true"></i>
                </a>
            </div>
        </template>
    </async-validation-field>
</template>

<script>
import AsyncValidationField from './AsyncValidationField.vue';

const PasswordField = {
    name: 'password-field',
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
            value: '',
            visible: false
        };
    },
    computed: {
        fieldType() {
            return this.visible ? 'text' : 'password';
        }
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

export default PasswordField;
</script>
