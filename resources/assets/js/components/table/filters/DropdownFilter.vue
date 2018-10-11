<template>
    <v-select :label="option_display_name" v-model="selected" :options="all_options" :searchable="false" :filterable="false" :clearable="false" class="nt-site-filter">
    </v-select>
</template>

<script>
import vSelect from 'vue-select/src';

const DropdownFilter = {
    name: 'nt-dropdown-filter',
    components: {
        'v-select': vSelect
    },
    props: {
        api_endpoint_url: {
            type: String,
            default: ''
        },
        api_request_parameters: {
            type: Object,
            default: () => {}
        },
        field_name: {
            type: String
        },
        has_blank_option: {
            type: Boolean,
            default: false
        },
        blank_option_display: {
            type: String,
            default: ''
        },
        default_options: {
            type: Array,
            default: () => []
        },
        default_selected_value: {
            type: String,
            default: ''
        },
        option_value_name: {
            type: String,
            default: 'name'
        },
        option_display_name: {
            type: String,
            default: 'display_name'
        }
    },
    computed: {
        options: {
            get() {
                return this.all_options;
            },
            set(options) {
                if(this.has_blank_option) {
                    let blank_option = {};
                    
                    blank_option[this.option_value_name] = '';
                    blank_option[this.option_display_name] = this.blank_option_display;
                    
                    options.unshift(blank_option);
                }
                
                this.all_options = options;
                
                if(this.default_selected_value != null) {
                    let options_length = options.length;
                    
                    for(var options_index = 0; options_index < options_length; options_index++) {
                        let option = options[options_index];
                        
                        if(option[this.option_value_name] == this.default_selected_value) {
                            this.selected = option;
                            
                            break;
                        }
                    }
                }
            }
        },
        selected: {
            get() {
                return this.selected_option;
            },
            set(selected_option) {
                this.selected_option = selected_option;
                
                this.$emit("selectedValueChanged", this.field_name, this.selected_option[this.option_value_name]);
            }
        }
    },
    data() {
        return {
            all_options: [],
            selected_option: {}
        };
    },
    mounted() {
        if(this.default_options.length > 0) {
            this.options = this.default_options;
        }
    
        if(this.api_endpoint_url.length > 0) {
            axios.get(this.api_endpoint_url, {
                params: this.api_request_parameters
            })
            .then(response =>{
                this.options = response.data.data;
                
                this.$emit("loaded", this.field_name);
            })
            .catch(error =>{
                console.log(error);
                
                alert('There was a problem trying to retrieve data for this filter. Please wait a moment and try again.');
                
                this.$emit('filterServerResponseFailed', error);
            })
            .then(function () {
                
            });
        }
        else {
            if(this.default_options.length > 0) {
                this.options = this.default_options;
                
                this.$emit("loaded", this.field_name);
            }
        }
    }
};
export default DropdownFilter;
</script>
