<template>
    <div 
        v-if="leaderboard_types.length > 0 && details_columns_loaded"
        class="table-responsive pb-0"
    >
        <table class="table table-sm table-bordered pb-0 mb-0">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th 
                        v-for="(leaderboard_type, leaderboard_type_index) in leaderboard_types"
                        v-if="leaderboard_type.name != 'daily'"
                        :key="leaderboard_type_index"
                        scope="col"
                    >
                        {{ leaderboard_type.display_name }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(row, row_index) in rows"
                >
                    <th scope="row">
                        {{ row.display_name }}
                    </th>
                    <td
                        v-for="(leaderboard_type, leaderboard_type_index) in leaderboard_types"
                        v-if="leaderboard_type.name != 'daily'"
                        :key="leaderboard_type_index"
                    >
                        <template v-if="row['rounded'] != null && row.rounded">
                            <rounded-decimal :original_number="getCategoryField(leaderboard_type.name, row.name)"></rounded-decimal>
                        </template>
                        <template v-else>
                            {{ getCategoryField(leaderboard_type.name, row.name) }}
                        </template>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        {{ categoryDetailsLabel }}
                    </th>
                    <td
                        v-for="(leaderboard_type, leaderboard_type_index) in leaderboard_types"
                        v-if="leaderboard_type.name != 'daily'"
                        :key="leaderboard_type_index"
                    >
                        <template
                            v-if="$store.getters['leaderboard_details_columns/getByName'](leaderboard_type.details_column_name).data_type == 'seconds'"
                        >
                            <seconds-to-time :unformatted="getCategoryField(leaderboard_type.name, leaderboard_type.details_column_name)" :include_hours="true" :zero_pad_hours="true">
                            </seconds-to-time>
                        </template>
                        <template v-else>
                            {{ getCategoryField(leaderboard_type.name, leaderboard_type.details_column_name) }}
                        </template>
                    </td>
                </tr> 
            </tbody>
        </table>
    </div>
</template>

<script>
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import SecondsToTime from '../formatting/SecondsToTime.vue';

const RankingSummaryDetailsTable = {
    name: 'ranking-summary-details-table',
    components: {
        'rounded-decimal': RoundedDecimal,
        'seconds-to-time': SecondsToTime
    },
    props: {
        record: {
            type: Object,
            default: () => {}
        },
        rows: {
            type: Array,
            default: () => []
        },
        leaderboard_types: {
            type: Array,
            default: () => []
        },
        details_column: {
            type: Object,
            default: () => {}
        }
    },
    data() {
        return {
           details_columns_loaded: false
        }
    },
    computed: {
        categoryDetailsLabel() {
            let category_details_segments = [];
            
            let leaderboard_types_length = this.leaderboard_types.length;
            
            for(let index = 0; index < leaderboard_types_length; index++) {
                let leaderboard_type = this.leaderboard_types[index];
                
                if(leaderboard_type.name != 'daily') {
                    let details_column = this.$store.getters['leaderboard_details_columns/getByName'](leaderboard_type.details_column_name);
                    
                    if(details_column['display_name'] != null) {
                        category_details_segments.push(details_column.display_name);
                    }
                }
            }
            
            let category_details = category_details_segments.join('/');
            
            return category_details;
        }
    },
    methods: {
        getCategoryField(category_name, field_name) {
            let field_value = '';
            
            if(this.record[category_name] != null) {
                field_value = this.record[category_name][field_name];
            }
            
            return field_value;
        }
    },
    created() {
        let promise = this.$store.dispatch('leaderboard_details_columns/loadAll');
        
        promise.then(() => {            
            this.details_columns_loaded = true;
        });
    }
};

export default RankingSummaryDetailsTable;
</script>
