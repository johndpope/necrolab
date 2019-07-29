<template>
    <div
        class="border-top"
        :class="{ 'bg-secondary': selected, 'bg-info': highlighted }"
        @mouseover="hovered"
        @mouseout="unhovered"
        @click="clicked"
    >
        <slot name="option">
            <div class="pt-4 pb-4 pl-2 pr-2">
                <template v-if="hasOptionFormatter">
                    <component :is="option_formatter" :name="value" :display_name="display_name">
                    </component>
                </template>
                <template v-else>
                    <span class="h5">
                        {{ display_name }}
                    </span>
                </template>
            </div>
        </slot>
    </div>
</template>

<script>
    const DropdownOption = {
        name: 'dropdown-option',
        props: {
            value: {
                type: String,
                default: ''
            },
            display_name: {
                type: String,
                default: ''
            },
            selected: {
                type: Boolean,
                default: false
            },
            highlighted: {
                type: Boolean,
                default: false
            },
            option_formatter: {
                type: Object,
                default: () => {}
            }
        },
        computed: {
            hasOptionFormatter() {
                return this.option_formatter != null && this.option_formatter['props'] != null;
            }
        },
        methods: {
            hovered() {
                this.$emit("hovered");
            },
            unhovered() {
                this.$emit("unhovered");
            },
            clicked() {
                this.$emit("clicked");
            }
        }
    };
    export default DropdownOption;
</script>