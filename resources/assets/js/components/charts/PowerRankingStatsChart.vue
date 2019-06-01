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

                this.leaderboard_types.forEach((leaderboard_type) => {
                    if(leaderboard_type.name == 'daily') {
                        return;
                    }

                    const leaderboard_type_color = this.getColor();

                    y_axis.push({
                        labels: {
                            enabled: false,
                            style: {
                                color: leaderboard_type_color
                            },
                            enabled: false
                        },
                        title: {
                            text: `${leaderboard_type.display_name} Category (Players)`,
                            style: {
                                color: leaderboard_type_color
                            }
                        }
                    });

                    leaderboard_type.details_columns.forEach((details_column_name) => {
                        const details_column = this.details_columns_by_name[details_column_name];
                        const leaderboard_type_details_column_color = this.getColor();

                        y_axis.push({
                            labels: {
                                enabled: false,
                                style: {
                                    color: leaderboard_type_details_column_color
                                },
                                enabled: false
                            },
                            title: {
                                text: `${leaderboard_type.display_name} (${details_column.display_name})`,
                                style: {
                                    color: leaderboard_type_details_column_color
                                }
                            }
                        });
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
                let yaxis_index = 0;

                series.push({
                    name: 'Total Players',
                    type: 'spline',
                    data: [],
                    yAxis: yaxis_index,
                    dashStyle: self.getDashStyle(),
                    color: players_color
                });

                yaxis_index += 1;

                this.leaderboard_types.forEach((leaderboard_type) => {
                    if(leaderboard_type.name == 'daily') {
                        return;
                    }

                    const leaderboard_type_color = this.getColor();

                    series.push({
                        name: `${leaderboard_type.display_name} (Players)`,
                        type: 'spline',
                        data: [],
                        yAxis: yaxis_index,
                        dashStyle: self.getDashStyle(),
                        color: leaderboard_type_color
                    });

                    yaxis_index += 1;

                    leaderboard_type.details_columns.forEach((details_column_name) => {
                        const details_column = this.details_columns_by_name[details_column_name];
                        const details_column_color = this.getColor();

                        series.push({
                            name: `${leaderboard_type.display_name} (${details_column.display_name})`,
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
                        });

                        yaxis_index += 1;
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

                this.leaderboard_types.forEach((leaderboard_type) => {
                    if(leaderboard_type.name == 'daily') {
                        return;
                    }

                    if(series_data[leaderboard_type.name] == null) {
                        series_data[leaderboard_type.name] = [];
                    }

                    series_data[leaderboard_type.name].push([
                        utc_date,
                        row.categories[leaderboard_type.name].players
                    ]);

                    leaderboard_type.details_columns.forEach((details_column_name) => {
                        const series_name = `${leaderboard_type.name}_${details_column_name}`;

                        if(series_data[series_name] == null) {
                            series_data[series_name] = [];
                        }

                        series_data[series_name].push([
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