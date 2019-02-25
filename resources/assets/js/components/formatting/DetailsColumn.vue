<template>
    <span>
        <template v-if="details_column.data_type == 'seconds'">
            <seconds-to-time 
                :unformatted="details_value" 
                :include_hours="true"
                :zero_pad_hours="false"
            >
            </seconds-to-time>
        </template>
        <template v-else>
            {{ details_value }}
        </template>
    </span>
</template>

<script>
import SecondsToTime from './SecondsToTime.vue';

const DetailsColumn = {
    name: 'details-columns',
    components: {
        'seconds-to-time': SecondsToTime,
    },
    props: {
        details_name: {
            default: ''
        },
        details_value: {
            default: ''
        }
    },
    data() {
        return {
            details_column: {}
        };
    },
    created() {
        this.details_column = this.$store.getters['details_columns/getByName'](this.details_name);
    }
};

export default DetailsColumn;
</script>
