<template>
    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
            <thead>
                <tr>
                    <th scope="col">1st Place</th>
                    <th scope="col">Top 5</th>
                    <th scope="col">Top 10</th>
                    <th scope="col">Top 20</th>
                    <th scope="col">Top 50</th>
                    <th scope="col">Top 100</th>
                    <th scope="col">Points Per Day</th>
                    <th scope="col">Attempts</th>
                    <th scope="col">Wins</th>
                    <th scope="col">Average Rank</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ record.first_place_ranks }}
                    </td>
                    <td>
                        {{ record.top_5_ranks }}
                    </td>
                    <td>
                        {{ record.top_10_ranks }}
                    </td>
                    <td>
                        {{ record.top_20_ranks }}
                    </td>
                    <td>
                        {{ record.top_50_ranks }}
                    </td>
                    <td>
                        {{ record.top_100_ranks }}
                    </td>
                    <td>
                        <rounded-decimal :original_number="record.points_per_day"></rounded-decimal>
                    </td>
                    <td>
                        {{ record.total_dailies }}
                    </td>
                    <td>
                        {{ record.total_wins }}
                    </td>
                    <td>
                        <rounded-decimal :original_number="record.average_rank"></rounded-decimal>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import RoundedDecimal from '../formatting/RoundedDecimal.vue';

const DailyRankingDetailsTable = {
    name: 'daily-ranking-details-table',
    components: {
        'rounded-decimal': RoundedDecimal
    },
    props: {
        record: {
            type: Object,
            default: () => {}
        }
    }
};

export default DailyRankingDetailsTable;
</script>
