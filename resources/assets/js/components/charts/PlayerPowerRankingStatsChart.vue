<script>
    import StatsChart from './StatsChart.vue';
    import parse from 'date-fns/parse';

    const PlayerPowerRankingStatsChart = {
        name: 'player-power-ranking-stats-chart',
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

                y_axis.push(this.getSingleYAxis('Rank'));

                y_axis.push(this.getSingleYAxis('Points'));

                this.leaderboard_types.forEach((leaderboard_type) => {
                    if(leaderboard_type.name == 'daily') {
                        return;
                    }

                    y_axis.push(this.getSingleYAxis(`${leaderboard_type.display_name} (Rank)`));

                    y_axis.push(this.getSingleYAxis(`${leaderboard_type.display_name} (Points)`));

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
                let yaxis_index = 0;
                const series = [];

                series.push(self.getSingleSeries('Rank', yaxis_index));

                yaxis_index += 1;

                series.push(self.getSingleSeries('Points', yaxis_index));

                yaxis_index += 1;

                this.leaderboard_types.forEach((leaderboard_type) => {
                    if(leaderboard_type.name == 'daily') {
                        return;
                    }

                    series.push(self.getSingleSeries(`${leaderboard_type.display_name} (Rank)`, yaxis_index, {
                        visible: false
                    }));

                    yaxis_index += 1;

                    series.push(self.getSingleSeries(`${leaderboard_type.display_name} (Points)`, yaxis_index, {
                        visible: false
                    }));

                    yaxis_index += 1;

                    leaderboard_type.details_columns.forEach((details_column_name) => {
                        const details_column = this.details_columns_by_name[details_column_name];

                        series.push(self.getSingleSeries(`${leaderboard_type.display_name} (${details_column.display_name})`, yaxis_index, {
                            visible: false,
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

                this.addToSeriesData(series_data, 'rank', [
                    utc_date,
                    row.rank
                ]);

                this.addToSeriesData(series_data, 'points', [
                    utc_date,
                    row.points
                ]);

                this.leaderboard_types.forEach((leaderboard_type) => {
                    if(leaderboard_type.name == 'daily' || row.categories[leaderboard_type.name] == null) {
                        return;
                    }

                    this.addToSeriesData(series_data, `${leaderboard_type.name}_rank`, [
                        utc_date,
                        row.categories[leaderboard_type.name].rank
                    ]);

                    this.addToSeriesData(series_data, `${leaderboard_type.name}_points`, [
                        utc_date,
                        row.categories[leaderboard_type.name].points
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

    export default PlayerPowerRankingStatsChart;
</script>