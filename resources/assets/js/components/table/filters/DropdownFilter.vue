<template>
    <div class="dropdown-filter">
        <div class="form-control form-control-lg" :class="{ 'pt-1 pb-1 pl-3 pr-3': hasOptionFormatter }" @click="modal_show = !modal_show">
            <div class="container-fluid h-100">
                <div class="row h-100">
                    <div class="col-10 h-100 pl-0 pr-0">
                        <slot name="selected-option" :selected="selected">
                            <template v-if="hasOptionFormatter">
                                <component :is="option_formatter" :name="selected[option_value_name]" :display_name="selected[option_display_name]">
                                </component>
                            </template>
                            <template v-else>
                                {{ selected[option_display_name] }}
                            </template>
                        </slot>
                    </div>
                    <div class="col-2 text-right d-flex align-items-center pr-0">
                        <div class="w-100 text-right">
                        <down-arrow></down-arrow>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <b-modal v-model="modal_show" :centered="true" :hide-footer="true">
            <div slot="modal-header" class="w-100">
                <span class="h4">
                    {{ label }}
                </span>
            </div>
            <div 
                v-for="(option, option_index) in options" 
                :key="option_index" 
                class="border-top" 
                :class="{ 'bg-secondary': option[option_value_name] == selected[option_value_name], 'bg-info': optionIsHighlighted(option_index) }"
                @mouseover="highlighted_option_index = option_index"
                @mouseout="highlighted_option_index = null"
                @click="optionSelected(options[option_index])"
            >
                <slot name="option" :option="option" :option_index="option_index">
                    <div class="pt-4 pb-4 pl-2 pr-2">
                        <template v-if="hasOptionFormatter">
                            <component :is="option_formatter" :name="option[option_value_name]" :display_name="option[option_display_name]">
                            </component>
                        </template>
                        <template v-else>
                            <span class="h5">
                                {{ option[option_display_name] }}
                            </span>
                        </template>
                    </div>
                </slot>
            </div>
        </b-modal>
    </div>
</template>

<script>

import bModal from 'bootstrap-vue/es/components/modal/modal';
import DownArrow from '../../formatting/DownArrow.vue';

const DropdownFilter = {
    name: 'nt-dropdown-filter',
    components: {
        'b-modal': bModal,
        'down-arrow': DownArrow
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
        label: {
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
        },
        option_formatter: {
            type: Object,
            default: () => {}
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
                
                let selected = this.selected_option[this.option_value_name];
                
                this.$emit("selectedValueChanged", this.field_name, selected);
                
                this.setSelectedState(selected);
            }
        },
        hasOptionFormatter() {
            return this.option_formatter != null && this.option_formatter['props'] != null;
        }
    },
    data() {
        return {
            modal_show: false,
            highlighted_option_index: null,
            all_options: [],
            selected_option: {}
        };
    },
    methods: {
        setSelectedState(selected) {},
        optionIsHighlighted(option_index) {
            return option_index == this.highlighted_option_index;
        },
        optionSelected(option) {
            this.selected = option;
            
            this.modal_show = false;
        }
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
