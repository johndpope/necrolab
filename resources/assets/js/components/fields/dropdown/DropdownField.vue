<template>
    <div class="dropdown-filter">
        <div class="form-control form-control-lg" :class="{ 'pt-1 pb-1 pl-3 pr-3': hasOptionFormatter }" @click="toggleModal()">
            <div class="container-fluid h-100">
                <div class="row h-100">
                    <div class="col-10 h-100 pl-0 pr-0">
                        <slot name="selected-option" :selected="selected" :option_groups="option_groups">
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
            <template v-for="(option, option_index) in options">
                <div v-if="option_groups[option_index] != null" class="h4 font-weight-bold pt-3 pb-3">
                    {{ option_groups[option_index] }}
                </div>
                <dropdown-option
                    :key="option_index"
                    :value="option[option_value_name]"
                    :display_name="option[option_display_name]"
                    :selected="option[option_value_name] == selected[option_value_name]"
                    :highlighted="optionIsHighlighted(option_index)"
                    @hovered="highlighted_option_index = option_index"
                    @unhovered="highlighted_option_index = null"
                    @clicked="optionSelected(option)"
                >
                </dropdown-option>
            </template>
        </b-modal>
    </div>
</template>

<script>
    import bModal from 'bootstrap-vue/es/components/modal/modal';
    import DownArrow from '../../formatting/DownArrow.vue';
    import DropdownOption from './DropdownOption.vue';

    const DropdownField = {
        name: 'dropdown-field',
        components: {
            'b-modal': bModal,
            'down-arrow': DownArrow,
            'dropdown-option': DropdownOption
        },
        props: {
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
            },
            default_selected_option: {
                type: Object,
                default: () => {}
            }
        },
        data() {
            return {
                modal_show: false,
                highlighted_option_index: null,
                options_initialized: false,
                hidden: false,
                selected_option: {},
                option_groups: {}
            };
        },
        computed: {
            options() {
                const options = this.getFlattenedOptions(this.getDefaultOptions());

                if(this.has_blank_option) {
                    let contains_blank_option = false;

                    options.forEach((option) => {
                        if(option[this.option_value_name] == '') {
                            contains_blank_option = true;
                        }
                    });

                    if(!contains_blank_option) {
                        const blank_option = {};

                        blank_option[this.option_value_name] = '';
                        blank_option[this.option_display_name] = this.blank_option_display;

                        options.unshift(blank_option);
                    }
                }

                const options_length = options.length;

                if(!this.options_initialized) {
                    options.forEach((option) => {
                        if(option['is_default'] != null && option.is_default == 1) {
                            this.selected = option;
                        }
                    });

                    if(this.selected['name'] == null && options[0]['name'] != null) {
                        this.selected = options[0];
                    }

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

                    let selected = {};

                    if(this.selected_option[this.option_value_name] != null) {
                        selected = this.selected_option[this.option_value_name];
                    }

                    this.setSelectedState(this.selected_option);

                    this.$emit("selectedValueChanged", this.field_name, selected);
                }
            },
            hasOptionFormatter() {
                return this.option_formatter != null && this.option_formatter['props'] != null;
            }
        },
        methods: {
            getFlattenedOptions(options) {
                this.option_groups = {};

                let flattened_options = [];

                options.forEach((option) => {
                    if(option['options'] != null) {
                        let group_start_index = flattened_options.length;

                        this.option_groups[group_start_index] = option.display_name;

                        option.options.forEach((group_option) => {
                            group_option.group_index = group_start_index;

                            flattened_options.push(group_option);
                        });
                    }
                    else {
                        flattened_options.push(option);
                    }
                });

                return flattened_options;
            },
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
            },
            toggleModal() {
                this.modal_show = !this.modal_show
            }
        },
        created() {
            if(this.default_selected_option[this.option_value_name] != null) {
                this.selected = this.default_selected_option;
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

    export default DropdownField;
</script>