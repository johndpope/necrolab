<script>
    import StatsChart from './StatsChart.vue';
    import parse from 'date-fns/parse';

    const PowerRankingStatsChart = {
        name: 'power-ranking-stats-chart',
        extends: StatsChart,
        props: {
            leaderboard_types: {
                type: Array,
                default: () => []
            },
            details_columns_by_name: {
                type: Object,
                default: () => {}
            }
        },
        methods: {
            getYAxis() {
                this.resetColorIndex();

                const y_axis = [];

                y_axis.push(this.getSingleYAxis('Players'));

                this.leaderboard_types.forEach((leaderboard_type) => {
                    if(leaderboard_type.name == 'daily') {
                        return;
                    }

                    y_axis.push(this.getSingleYAxis(`${leaderboard_type.display_name} Category (Players)`));

                    leaderboard_type.details_columns.forEach((details_column_name) => {
                        const details_column = this.details_columns_by_name[details_column_name];

                        y_axis.push(this.getSingleYAxis(`${leaderboard_type.display_name} (${details_column.display_name})`));
                    });
                });

                return y_axis;
            },
            getSeries() {
                this.resetDashStyleIndex();
                this.resetColorIndex();

                const self = this;
                const series = [];
                let yaxis_index = 0;

                series.push(self.getSingleSeries('Players', yaxis_index));

                yaxis_index += 1;

                this.leaderboard_types.forEach((leaderboard_type) => {
                    if(leaderboard_type.name == 'daily') {
                        return;
                    }

                    series.push(self.getSingleSeries(`${leaderboard_type.display_name} (Players)`, yaxis_index));

                    yaxis_index += 1;

                    leaderboard_type.details_columns.forEach((details_column_name) => {
                        const details_column = this.details_columns_by_name[details_column_name];

                        series.push(self.getSingleSeries(`${leaderboard_type.display_name} (${details_column.display_name})`, yaxis_index, {
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

                        yaxis_index += 1;
                    });
                });

                return Object.values(series);
            },
            processSeriesDataRow(series_data, row) {
                const date = parse(row.date, 'YYYY-MM-DD');
                const utc_date = Date.UTC(date.getFullYear(), date.getMonth(), date.getDate());

                this.addToSeriesData(series_data, 'players', [
                    utc_date,
                    row.players
                ]);

                this.leaderboard_types.forEach((leaderboard_type) => {
                    if(leaderboard_type.name == 'daily') {
                        return;
                    }

                    this.addToSeriesData(series_data, leaderboard_type.name, [
                        utc_date,
                        row.categories[leaderboard_type.name].players
                    ]);

                    leaderboard_type.details_columns.forEach((details_column_name) => {

                        this.addToSeriesData(series_data, `${leaderboard_type.name}_${details_column_name}`, [
                            utc_date,
                            row.categories[leaderboard_type.name].details[details_column_name]
                        ]);
                    });
                });
            }
        }
    };

    export default PowerRankingStatsChart;
</script>