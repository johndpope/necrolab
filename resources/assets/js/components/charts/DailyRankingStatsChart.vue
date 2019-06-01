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

                const players_color = this.getColor();

                y_axis.push({
                    labels: {
                        enabled: false,
                        style: {
                            color: players_color
                        },
                    },
                    title: {
                        text: 'Players',
                        style: {
                            color: players_color
                        }
                    }
                });


                const attempts_color = this.getColor();

                y_axis.push({
                    labels: {
                        enabled: false,
                        style: {
                            color: attempts_color
                        },
                    },
                    title: {
                        text: 'Attempts',
                        style: {
                            color: attempts_color
                        }
                    }
                });


                const wins_color = this.getColor();

                y_axis.push({
                    labels: {
                        enabled: false,
                        style: {
                            color: wins_color
                        },
                    },
                    title: {
                        text: 'Wins',
                        style: {
                            color: wins_color
                        }
                    }
                });

                this.leaderboard_type.details_columns.forEach((details_column_name) => {
                    const details_column = this.details_columns_by_name[details_column_name];
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
                this.resetDashStyleIndex();
                this.resetColorIndex();

                const self = this;
                const series = [];
                const players_color = this.getColor();

                series.push({
                    name: 'Players',
                    type: 'spline',
                    data: [],
                    yAxis: 0,
                    dashStyle: self.getDashStyle(),
                    color: players_color
                });


                const attempts_color = this.getColor();

                series.push({
                    name: 'Attempts',
                    type: 'spline',
                    data: [],
                    yAxis: 1,
                    dashStyle: self.getDashStyle(),
                    color: attempts_color
                });


                const wins_color = this.getColor();

                series.push({
                    name: 'Wins',
                    type: 'spline',
                    data: [],
                    yAxis: 2,
                    dashStyle: self.getDashStyle(),
                    color: wins_color
                });

                this.leaderboard_type.details_columns.forEach((details_column_name, details_column_index) => {
                    const details_column = this.details_columns_by_name[details_column_name];
                    const details_column_color = this.getColor();

                    series.push({
                        name: details_column.display_name,
                        type: 'spline',
                        data: [],
                        yAxis: details_column_index + 3,
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
                    });
                });

                return Object.values(series);
            },
            processSeriesDataRow(series_data, row) {
                const date = parse(row.date, 'YYYY-MM-DD');
                const utc_date = Date.UTC(date.getFullYear(), date.getMonth(), date.getDate());

                if(series_data['players'] == null) {
                    series_data['players'] = [];
                }

                series_data['players'].push([
                    utc_date,
                    row.players
                ]);


                if(series_data['attempts'] == null) {
                    series_data['attempts'] = [];
                }

                series_data['attempts'].push([
                    utc_date,
                    row.dailies
                ]);


                if(series_data['wins'] == null) {
                    series_data['wins'] = [];
                }

                series_data['wins'].push([
                    utc_date,
                    row.wins
                ]);

                this.leaderboard_type.details_columns.forEach((details_column_name) => {
                    if(series_data[details_column_name] == null) {
                        series_data[details_column_name] = [];
                    }

                    let details_column_value = 0;

                    if(row.details[details_column_name] != null) {
                        details_column_value = row.details[details_column_name];
                    }

                    series_data[details_column_name].push([
                        utc_date,
                        details_column_value
                    ]);
                });
            }
        }
    };

    export default DailyRankingStatsChart;
</script>