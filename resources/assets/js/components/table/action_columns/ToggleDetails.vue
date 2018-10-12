<template>
    <b-button @click.stop="toggleDetailsRow">
        {{ detailsRowVisible(row_index) ? 'Hide' : 'Show' }} Details
    </b-button>
</template>

<script>
const ToggleDetails = {
    name: 'toggle-details',
    props: {
        row_index: {
            type: Number
        },
        detailsRowVisible: {
            type: Function
        }
    },
    methods: {
        toggleDetailsRow() {
            this.$emit('detailsRowToggled', this.row_index);
        }
    }
};

export default ToggleDetails;
</script>
