<template>
    <input v-model.lazy="search" type="search" class="form-control form-control-lg" placeholder="Search" />
</template>

<script>
const TableSearch = {
    name: 'necrotable-search',
    data() {
        return {
            search: ''
        }
    },
    watch: {
        search() {
            this.$emit('searchSubmitted', 'search', this.search);
        }
    }
};

export default TableSearch;
</script>
