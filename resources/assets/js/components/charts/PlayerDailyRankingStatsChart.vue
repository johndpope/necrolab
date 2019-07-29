<script>
    import StatsChart from './StatsChart.vue';
    import parse from 'date-fns/parse';

    const PlayerDailyRankingStatsChart = {
        name: 'player-daily-ranking-stats-chart',
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

                y_axis.push(this.getSingleYAxis('Rank'));

                y_axis.push(this.getSingleYAxis('Attempts'));

                y_axis.push(this.getSingleYAxis('Wins'));

                y_axis.push(this.getSingleYAxis('1st Place Ranks'));

                y_axis.push(this.getSingleYAxis('Top 5 Ranks'));

                y_axis.push(this.getSingleYAxis('Top 10 Ranks'));

                y_axis.push(this.getSingleYAxis('Top 20 Ranks'));

                y_axis.push(this.getSingleYAxis('Top 50 Ranks'));

                y_axis.push(this.getSingleYAxis('Top 100 Ranks'));

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

                series.push(self.getSingleSeries('Rank', 0));

                series.push(self.getSingleSeries('Attempts', 1));

                series.push(self.getSingleSeries('Wins', 2));

                series.push(self.getSingleSeries('1st Place Ranks', 3));

                series.push(self.getSingleSeries('Top 5 Ranks', 4));

                series.push(self.getSingleSeries('Top 10 Ranks', 5));

                series.push(self.getSingleSeries('Top 20 Ranks', 6));

                series.push(self.getSingleSeries('Top 50 Ranks', 7));

                series.push(self.getSingleSeries('Top 100 Ranks', 8));

                self.leaderboard_type.details_columns.forEach((details_column_name, details_column_index) => {
                    const details_column = self.details_columns_by_name[details_column_name];

                    series.push(self.getSingleSeries(details_column.display_name, details_column_index + 9, {
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

                return Object.values(series);
            },
            processSeriesDataRow(series_data, row) {
                const date = parse(row.date, 'YYYY-MM-DD');
                const utc_date = Date.UTC(date.getFullYear(), date.getMonth(), date.getDate());

                this.addToSeriesData(series_data, 'rank', [
                    utc_date,
                    row.rank
                ]);

                this.addToSeriesData(series_data, 'attempts', [
                    utc_date,
                    row.dailies
                ]);

                this.addToSeriesData(series_data, 'wins', [
                    utc_date,
                    row.wins
                ]);

                this.addToSeriesData(series_data, 'first_place_ranks', [
                    utc_date,
                    row.first_place_ranks
                ]);

                this.addToSeriesData(series_data, 'top_5_ranks', [
                    utc_date,
                    row.top_5_ranks
                ]);

                this.addToSeriesData(series_data, 'top_10_ranks', [
                    utc_date,
                    row.top_10_ranks
                ]);

                this.addToSeriesData(series_data, 'top_20_ranks', [
                    utc_date,
                    row.top_20_ranks
                ]);

                this.addToSeriesData(series_data, 'top_50_ranks', [
                    utc_date,
                    row.top_50_ranks
                ]);

                this.addToSeriesData(series_data, 'top_100_ranks', [
                    utc_date,
                    row.top_100_ranks
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

    export default PlayerDailyRankingStatsChart;
</script>