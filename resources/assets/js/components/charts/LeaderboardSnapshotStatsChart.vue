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

            const players_color = this.getColor();

            y_axis.push({
                labels: {
                    enabled: false,
                    style: {
                        color: players_color
                    }
                },
                title: {
                    text: 'Players',
                    style: {
                        color: players_color
                    }
                }
            });

            this.details_columns.forEach((details_column) => {
                const details_column_color = this.getColor();

                y_axis.push({
                    labels: {
                        enabled: false,
                        style: {
                            color: details_column_color
                        }
                    },
                    title: {
                        text: details_column.display_name,
                        style: {
                            color: details_column_color
                        }
                    }
                });
            });

            return y_axis;
        },
        getSeries() {
            this.resetColorIndex();

            const self = this;

            const series = {};

            const players_color = this.getColor();

            series['players'] = {
                name: 'Players',
                type: 'spline',
                data: [],
                yAxis: 0,
                dashStyle: self.getDashStyle(),
                color: players_color
            };

            self.details_columns.forEach((details_column, index) => {
                const yaxis_index = index + 1;
                const details_column_color = this.getColor();

                series[details_column.name] = {
                    name: details_column.display_name,
                    type: 'spline',
                    data: [],
                    yAxis: yaxis_index,
                    dashStyle: self.getDashStyle(),
                    color: details_column_color,
                    tooltip: {
                        // NOTE: "function()" is needed here otherwise the context of "this" is in Vue and not Highcharts.
                        pointFormatter: function() {
                            let value = this.y;

                            if(details_column.name == 'time') {
                                value = self.getFormattedTime(value);
                            }

                            return `<span style="color:${details_column_color}">●</span> ${this.series.name}: <b>${value}</b><br/>`;
                        }
                    }
                };
            });

            return Object.values(series);
        },
        processSeriesDataRow(series_data, row) {
            if(series_data['players'] == null) {
                series_data['players'] = [];
            }

            const date = parse(row.date, 'YYYY-MM-DD');
            const utc_date = Date.UTC(date.getFullYear(), date.getMonth(), date.getDate());

            series_data['players'].push([
                utc_date,
                row.players
            ]);

            this.details_columns.forEach((details_column) => {
                if(series_data[details_column.name] == null) {
                    series_data[details_column.name] = [];
                }

                if(row.details[details_column.name] != null) {
                    series_data[details_column.name].push([
                        utc_date,
                        row.details[details_column.name]
                    ]);
                }
            });
        }
    }
};

export default LeaderboardSnapshotStatsChart;
</script>