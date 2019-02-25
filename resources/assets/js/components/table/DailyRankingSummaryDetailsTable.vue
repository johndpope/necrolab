<template>
    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
            <thead>
                <tr>
                    <th scope="col">Attempts</th>
                    <th scope="col">Wins</th>
                    <th
                        v-for="details_column in details_columns"
                    >
                        {{ details_column.display_name }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ record['dailies'] != null ? record.dailies : ''}}
                    </td>
                    <td>
                        {{ record['wins'] != null ? record.wins : ''}}
                    </td>
                    <td
                        v-for="details_column in details_columns"
                    >
                        <details-column
                            :details_name="details_column.name" 
                            :details_value="getDetailsValue(record, details_column.name)"
                        >
                        </details-column>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import DetailsColumn from '../formatting/DetailsColumn.vue';

const DailyRankingSummaryDetailsTable = {
    name: 'daily-ranking-details-table',
    components: {
        'details-column': DetailsColumn
    },
    props: {
        record: {
            type: Object,
            default: () => {}
        },
        details_columns: {
            type: Array,
            default: () => []
        }
    },
    methods: {
        getDetailsValue(row, details_name) {
            let details_value = '';
            
            if(
                row['details'] != null &&
                row['details'][details_name] != null
            ) {
                details_value = row['details'][details_name];
            }
            
            return details_value;
        }
    }
};

export default DailyRankingSummaryDetailsTable;
</script>
