<template>
    <td>
        <slot name="cell-value" slot-scope="value">
              {{ value }}
        </slot>
    </td>    
</template>

<script>

const TableCell = {
    name: 'table-cell',
    data() {
        return {
            value: ''
        }
    }
};

export default TableCell;
</script>
