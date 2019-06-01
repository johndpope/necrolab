<template>
    <with-nav-body
        :loaded="loaded"
        :breadcrumbs="breadcrumbs"
        title="Leaderboard Snapshots"
        :sub_title="subTitle"
    >
        <leaderboard-snapshot-stats-chart
            :dataset="dataset"
            :details_columns="details_columns"
        >
        </leaderboard-snapshot-stats-chart>
        <br />
        <necrotable
            :dataset="dataset"
            :header_columns="headerColumns"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td>
                    <router-link :to="getEntriesUrl(row.date)">
                        {{ row.date }}
                    </router-link>
                </td>
                <td>
                    {{ row.players }}
                </td>
                <td v-for="details_column in details_columns">
                    <details-column
                        :details_name="details_column.name"
                        :details_value="row.details[details_column.name] != null ? row.details[details_column.name] : ''"
                    >
                    </details-column>
                </td>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
import LeaderboardBasePage from './LeaderboardBasePage.vue';
import WithNavBody from '../../layouts/WithNavBody.vue';
import Dataset from '../../../datasets/Dataset.js';
import LeaderboardSnapshotStatsChart from '../../charts/LeaderboardSnapshotStatsChart.vue';
import NecroTable from '../../table/NecroTable.vue';
import DetailsColumn from '../../formatting/DetailsColumn.vue';

const LeaderboardSnapshotsPage = {
    extends: LeaderboardBasePage,
    name: 'leaderboard-snapshots-page',
    components: {
        'with-nav-body': WithNavBody,
        'leaderboard-snapshot-stats-chart': LeaderboardSnapshotStatsChart,
        'necrotable': NecroTable,
        'details-column': DetailsColumn
    },
    computed: {
        breadcrumbs() {
            return [
                {
                    text: 'Leaderboards'
                },
                {
                    text: 'Snapshots'
                },
                {
                    text: this.subTitle
                }
            ]
        },
        headerColumns() {
            const leaderboard_columns = [
                'Date',
                'Players'
            ];

            this.details_columns.forEach((details_column) => {
                leaderboard_columns.push(details_column.display_name);
            });

            return leaderboard_columns;
        }
    },
    methods: {
        loadState(route_params) {
            this.loadRecords(route_params).then(() => {
                this.dataset = new Dataset('leaderboard_snapshots', '/api/1/leaderboard/snapshots');

                this.dataset.disablePagination();
                this.dataset.setRequestParameter('leaderboard_source', this.leaderboard_source.name);
                this.dataset.setRequestParameter('leaderboard_id', this.leaderboard.id);

                this.loaded = true;
            });
        },
        getEntriesUrl(date) {
            return this.$route.path + '/' + date;
        }
    }
};

export default LeaderboardSnapshotsPage;
</script>
