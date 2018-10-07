<template>
    <select v-model="selected" class="form-control form-control-lg">
        <option v-for="(option, index) in options" :value="index">
            {{ option[option_display_name] }}
        </option>
    </select>
</template>

<script>
const DropdownFilter = {
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
        label: {
            type: String,
            default: ''
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
        default_selected_index: {
            type: Number,
            default: 0
        },
        option_value_name: {
            type: String,
            default: 'value'
        },
        option_display_name: {
            type: String,
            default: 'label'
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
            }
        },
        selected: {
            get() {
                return this.selected_index;
            },
            set(index) {
                this.selected_index = index;
                
                this.selected_option = {};
                
                if(this.all_options[index] != null) {
                    this.selected_option = this.all_options[index];
                }
                
                this.$emit("selectedValueChanged", this.field_name, this.selected_option[this.option_value_name]);
            }
        }
    },
    data() {
        return {
            all_options: this.default_options,
            selected_index: this.default_selected_index,
            selected_option: {}
        };
    },
    methods: {
        getAllOptions() {
            return this.all_options;
        }
    },
    mounted() {
        if(this.options.length == 0) {
            axios.get(this.api_endpoint_url, {
                params: this.api_request_parameters
            })
            .then(response =>{
                this.options = response.data.data;
            })
            .catch(error =>{
                console.log(error);
                
                alert('There was a problem trying to retrieve data for this filter. Please wait a moment and try again.');
                
                this.$emit('filterServerResponseFailed', error);
            })
            .then(function () {
                
            });
        }
        
        this.$emit('initialValueSet', this.field_name, this.all_options[this.selected]);
    }
};
export default DropdownFilter;
</script>
