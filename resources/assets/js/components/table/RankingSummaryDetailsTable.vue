<template>
    <div 
        v-if="leaderboard_types.length > 0"
        class="table-responsive pb-0"
    >
        <table class="table table-sm table-bordered pb-0 mb-0">
            <thead>
                <tr>
                    <th scope="col">
                        Category
                    </th>
                    <th 
                        scope="col"
                        v-for="(row, row_index) in rows"
                    >
                        {{ row.display_name }}
                    </th>
                    <th 
                        scope="col"
                        v-for="(details_name, details_index) in details_columns"
                        :key="details_index"
                    >
                        {{ $store.getters['details_columns/getByName'](details_name).display_name }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(leaderboard_type, leaderboard_type_index) in leaderboard_types"
                    v-if="leaderboard_type.name != 'daily'"
                    :key="leaderboard_type_index"
                >
                    <th scope="row">
                        {{ leaderboard_type.display_name }}
                    </th>
                    <td 
                        v-for="(row, row_index) in rows"
                    >
                        <template v-if="row['rounded'] != null && row.rounded">
                            <rounded-decimal :original_number="getCategoryField(leaderboard_type.name, row.name)"></rounded-decimal>
                        </template>
                        <template v-else>
                            {{ getCategoryField(leaderboard_type.name, row.name) }}
                        </template>
                    </td>
                    <td
                        v-for="details_column_name in details_columns"
                    >
                        <template
                            v-for="(details_value, details_name) in getCategoryField(leaderboard_type.name, 'details')"
                        >
                            <details-column
                                v-if="details_name == details_column_name"
                                :details_name="details_name" 
                                :details_value="details_value"
                            >
                            </details-column>
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import DetailsColumn from '../formatting/DetailsColumn.vue';

const RankingSummaryDetailsTable = {
    name: 'ranking-summary-details-table',
    components: {
        'rounded-decimal': RoundedDecimal,
        'details-column': DetailsColumn
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
        }
    },
    data() {
        return {
            details_columns: []
        };
    },
    methods: {
        getCategoryField(category_name, field_name) {
            let field_value = '';
            
            if(
                this.record['categories'] != null && 
                this.record['categories'][category_name] != null && 
                this.record['categories'][category_name][field_name] != null
            ) {
                field_value = this.record.categories[category_name][field_name];
            }
            
            return field_value;
        }
    },
    created() {
        this.leaderboard_types.forEach((leaderboard_type) => {
            leaderboard_type.details_columns.forEach((details_name) => {
                if(this.details_columns.indexOf(details_name) == -1) {
                    this.details_columns.push(details_name);
                }
            });
        });
    }
};

export default RankingSummaryDetailsTable;
</script>
