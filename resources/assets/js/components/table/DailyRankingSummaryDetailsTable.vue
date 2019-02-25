<template>
    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
            <thead>
                <tr>
                    <th scope="col">Attempts</th>
                    <th scope="col">Wins</th>
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
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
const DailyRankingSummaryDetailsTable = {
    name: 'daily-ranking-details-table',
    props: {
        record: {
            type: Object,
            default: () => {}
        }
    }
};

export default DailyRankingSummaryDetailsTable;
</script>
