<template>
    <div class="form-group">
        <div class="d-flex">
            <div class="flex-grow-1">
                <label :for="field_name">{{ label }}</label>
            </div>
            <div>
                <loading
                    class="vld-parent"
                    :active.sync="check_state_loading"
                    :is-full-page="false"
                    :can-cancel="false"
                    :height="15"
                    :width="15"
                >
                </loading>
                <i v-if="!check_state_loading && check_state == 'good'" class="fas fa-check-circle"></i>
                <i v-if="!check_state_loading && check_state == 'bad'" class="fas fa-minus-circle"></i>
            </div>
        </div>
        <div class="input-group">
            <slot name="field-prepend"></slot>
            <input
                :name="field_name"
                :value="value"
                v-on:input="$emit('input', $event.target.value)"
                :type="input_type"
                class="form-control"
                :class="{ 'is-invalid': error.length > 0 }"
                :id="field_name"
                :maxlength="maxlength"
                :aria-describedby="`${field_name}_append`"
                autocomplete="off"
                @focus="show_help_text = true"
                @blur="show_help_text = false"
            />
            <slot name="field-append"></slot>
            <div v-if="error.length > 0" class="invalid-feedback">
                <strong>{{ error }}</strong>
            </div>
        </div>
        <small v-if="help_text.length > 0 && error.length === 0 && show_help_text" class="form-text text-muted">
            {{ help_text }}
        </small>
    </div>
</template>

<script>
import Loading from 'vue-loading-overlay';
import debounce from 'tiny-debounce';

const AsyncValidationField = {
    name: 'async-validation-field',
    components: {
        'loading': Loading
    },
    props: {
        input_type: {
            type: String,
            default: 'text'
        },
        field_name: {
            type: String,
            required: true
        },
        label: {
            type: String,
            required: true
        },
        value: {
            type: String,
            default: ''
        },
        error: {
            type: String,
            default: ''
        },
        validation_url: {
            type: String,
            required: true
        },
        help_text: {
            type: String,
            default: ''
        },
        maxlength: {
            type: String,
            default: '100'
        }
    },
    data() {
        return {
            debounced_value: '',
            show_help_text: false,
            check_state_loading: false,
            check_state: '',
            check_promise: null
        }
    },
    computed: {
        localError: {
            get() {
                return this.error;
            },
            set(error) {
                this.$emit('error', {
                    'field': this.field_name,
                    'error': error
                });
            }
        }
    },
    watch: {
        value: debounce(function(new_value, old_value) {
            this.debounced_value = new_value;

            if(new_value.length == 0) {
                this.check_state_loading = false;
                this.check_state = '';
                this.localError = '';
            }

            if(new_value.length > 0 && !this.check_state_loading) {
                this.check_state_loading = true;
                this.check_state = '';

                const params = {};

                params[this.field_name] = new_value;

                axios.post(this.validation_url, params)
                    .then(response => {
                        this.check_state = response.data.data.state;

                        if(response.data.data.exists) {
                            this.localError = `This ${this.field_name} is unavailable.`;
                        }

                        this.check_state_loading = false;
                        this.localError = '';
                    })
                    .catch(error => {
                        if(error.response.data[this.field_name] != null) {
                            this.localError = error.response.data[this.field_name][0];
                        }
                        else {
                            this.localError = 'There was a problem communicating with the server. Please try again.';
                        }

                        this.check_state = 'bad';

                        this.check_state_loading = false;
                    });
            }
        }, 300)
    }
};

export default AsyncValidationField;
</script>
