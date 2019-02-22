<template>
    <div class="col-sm-12 col-md-6 col-lg-4 pt-2" :class="{ 'd-none': hidden }">
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
                    @click="optionSelected(option)"
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
        initialize: {
            type: Boolean,
            default: false
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
        options() {            
            let options = this.getDefaultOptions();  

            let options_length = options.length;
                
            if(this.has_blank_option) {
                let contains_blank_option = false;
                
                for(let options_index = 0; options_index < options_length; options_index++) {
                    let option = options[options_index];
                    
                    if(option[this.option_value_name] == '') {
                        contains_blank_option = true;
                        
                        break;
                    }
                }
                
                if(!contains_blank_option) {
                    let blank_option = {};
                    
                    blank_option[this.option_value_name] = '';
                    blank_option[this.option_display_name] = this.blank_option_display;
                    
                    options.unshift(blank_option);
                }
            }
            
            options_length = options.length;
            
            if(!this.options_initialized) {
                for(let options_index = 0; options_index < options_length; options_index++) {
                    let option = options[options_index];

                    if(option['is_default'] != null && option.is_default == 1) {
                        this.selected = option;
                        
                        break;
                    }
                }
                
                if(this.selected == null && options[0] != null) {
                    this.selected = options[0];
                }
            }
            
            if(!this.options_initialized) {
                this.options_initialized = true;
            }
            
            if(options_length <= 1) {
                this.hidden = true;
            }
            else {
                this.hidden = false;
            }

            let selected_option = this.selected;
            
            if(selected_option[this.option_value_name] != null) {
                let selected_in_options = options.find(option => option[this.option_value_name] === selected_option[this.option_value_name]);
                
                if(selected_in_options == null) {
                    if(options_length > 0) {                        
                        this.selected = options[0];
                    }
                }
            }
            
            return options;
        },
        selected: {
            get() {
                return this.selected_option;
            },
            set(selected_option) {
                this.selected_option = selected_option;

                let selected = null;
                
                if(this.selected_option[this.option_value_name] != null) {
                    selected = this.selected_option[this.option_value_name];
                }
                
                this.$emit("selectedValueChanged", this.field_name, selected);
                
                this.setSelectedState(this.selected_option);
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
            options_initialized: false,
            hidden: false,
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
        },
        loadOptions(resolve, reject) {            
            resolve();
        },
        getDefaultOptions() {
            return this.default_options;
        }
    },
    mounted() {
        let load_options_promise = new Promise((resolve, reject) => {
            this.loadOptions(resolve, reject);
        });
    
        load_options_promise.then((success_message) => {
            this.$emit("loaded", this.field_name);
        });
    }
};
export default DropdownFilter;
</script>
