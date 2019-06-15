<script>
import StatsChart from './StatsChart.vue';
import parse from 'date-fns/parse';

const LeaderboardSnapshotStatsChart = {
    name: 'leaderboard-snapshots-stats-chart',
    extends: StatsChart,
    props: {
        details_columns: {
            type: Array,
            default: () => []
        }
    },
    methods: {
        getYAxis() {
            this.resetColorIndex();

            const y_axis = [];

            y_axis.push(this.getSingleYAxis('Players'));

            this.details_columns.forEach((details_column) => {
                y_axis.push(this.getSingleYAxis(details_column.display_name));
            });

            return y_axis;
        },
        getSeries() {
            this.resetColorIndex();

            const self = this;

            const series = [];

            series.push(self.getSingleSeries('Players', 0));

            self.details_columns.forEach((details_column, index) => {
                series.push(self.getSingleSeries(details_column.display_name, index + 1, {
                    tooltip: {
                        // NOTE: "function()" is needed here otherwise the context of "this" is in Vue and not Highcharts.
                        pointFormatter: function() {
                            let value = this.y;

                            if(details_column.name == 'time') {
                                value = self.getFormattedTime(value);
                            }

                            return `<span style="color:${this.color}">●</span> ${this.series.name}: <b>${value}</b><br/>`;
                        }
                    }
                }));
            });

            return series;
        },
        processSeriesDataRow(series_data, row) {
            const date = parse(row.date, 'YYYY-MM-DD');
            const utc_date = Date.UTC(date.getFullYear(), date.getMonth(), date.getDate());

            this.addToSeriesData(series_data, 'players', [
                utc_date,
                row.players
            ]);

            this.details_columns.forEach((details_column) => {
                let details_value = 0;

                if(row.details[details_column.name] != null) {
                    details_value = row.details[details_column.name]
                }

                this.addToSeriesData(series_data, details_column.name, [
                    utc_date,
                    details_value
                ]);
            });
        }
    }
};

export default LeaderboardSnapshotStatsChart;
</script>