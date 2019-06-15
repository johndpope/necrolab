<script>
    import StatsChart from './StatsChart.vue';
    import parse from 'date-fns/parse';

    const DailyRankingStatsChart = {
        name: 'daily-ranking-stats-chart',
        extends: StatsChart,
        props: {
            leaderboard_type: {
                type: Object,
                default: () => {}
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

                y_axis.push(this.getSingleYAxis('Attempts'));

                y_axis.push(this.getSingleYAxis('Wins'));

                this.leaderboard_type.details_columns.forEach((details_column_name) => {
                    const details_column = this.details_columns_by_name[details_column_name];

                    y_axis.push(this.getSingleYAxis(details_column.display_name));
                });

                return y_axis;
            },
            getSeries() {
                this.resetDashStyleIndex();
                this.resetColorIndex();

                const self = this;
                const series = [];

                series.push(self.getSingleSeries('Players', 0));

                series.push(self.getSingleSeries('Attempts', 1));

                series.push(self.getSingleSeries('Wins', 2));

                this.leaderboard_type.details_columns.forEach((details_column_name, details_column_index) => {
                    const details_column = this.details_columns_by_name[details_column_name];

                    series.push(self.getSingleSeries(details_column.display_name, details_column_index + 3, {
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

                this.addToSeriesData(series_data, 'attempts', [
                    utc_date,
                    row.dailies
                ]);

                this.addToSeriesData(series_data, 'wins', [
                    utc_date,
                    row.wins
                ]);

                this.leaderboard_type.details_columns.forEach((details_column_name) => {
                    let details_column_value = 0;

                    if(row.details[details_column_name] != null) {
                        details_column_value = row.details[details_column_name];
                    }

                    this.addToSeriesData(series_data, details_column_name, [
                        utc_date,
                        details_column_value
                    ]);
                });
            }
        }
    };

    export default DailyRankingStatsChart;
</script>