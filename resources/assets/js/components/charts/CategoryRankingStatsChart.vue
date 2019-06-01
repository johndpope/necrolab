<script>
    import StatsChart from './StatsChart.vue';
    import parse from 'date-fns/parse';

    const CategoryRankingStatsChart = {
        name: 'category-ranking-stats-chart',
        extends: StatsChart,
        props: {
            leaderboard_type: {
                type: Object,
                default: () => []
            },
            characters: {
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

                this.leaderboard_type.details_columns.forEach((details_column_name) => {
                    const details_column = this.details_columns_by_name[details_column_name];
                    const leaderboard_type_details_column_color = this.getColor();

                    y_axis.push({
                        labels: {
                            enabled: false,
                            style: {
                                color: leaderboard_type_details_column_color
                            }
                        },
                        title: {
                            text: details_column.display_name,
                            style: {
                                color: leaderboard_type_details_column_color
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

                this.leaderboard_type.details_columns.forEach((details_column_name, details_column_index) => {
                    const details_column = this.details_columns_by_name[details_column_name];
                    const leaderboard_type_details_column_color = this.getColor();

                    series.push({
                        name: details_column.display_name,
                        type: 'spline',
                        data: [],
                        yAxis: details_column_index + 1,
                        dashStyle: self.getDashStyle(),
                        color: leaderboard_type_details_column_color,
                        tooltip: {
                            // NOTE: "function()" is needed here otherwise the context of "this" is in Vue and not Highcharts.
                            pointFormatter: function() {
                                let value = this.y;

                                if(details_column.name == 'time') {
                                    value = self.getFormattedTime(value);
                                }

                                return `<span style="color:${leaderboard_type_details_column_color}">●</span> ${this.series.name}: <b>${value}</b><br/>`;
                            }
                        }
                    });
                });

                this.characters.forEach((character) => {
                    const character_color = this.getColor();

                    series.push({
                        name: `${character.display_name} (Players)`,
                        type: 'spline',
                        visible: false,
                        data: [],
                        yAxis: 0,
                        dashStyle: self.getDashStyle(),
                        color: character_color
                    });

                    this.leaderboard_type.details_columns.forEach((details_column_name, details_column_index) => {
                        const details_column = this.details_columns_by_name[details_column_name];
                        const details_column_color = this.getColor();

                        series.push({
                            name: `${character.display_name} (${details_column.display_name})`,
                            type: 'spline',
                            visible: false,
                            data: [],
                            yAxis: details_column_index + 1,
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
                });

                return Object.values(series);
            },
            processSeriesDataRow(series_data, row) {
                const date = parse(row.date, 'YYYY-MM-DD');
                const utc_date = Date.UTC(date.getFullYear(), date.getMonth(), date.getDate());

                if(row.categories[this.leaderboard_type.name] == null) {
                    return;
                }

                if(series_data['players'] == null) {
                    series_data['players'] = [];
                }

                series_data['players'].push([
                    utc_date,
                    row.categories[this.leaderboard_type.name].players
                ]);

                this.leaderboard_type.details_columns.forEach((details_column_name) => {
                    if(series_data[details_column_name] == null) {
                        series_data[details_column_name] = [];
                    }

                    let details_value = 0;

                    if(row.categories[this.leaderboard_type.name].details[details_column_name] != null) {
                        details_value = row.categories[this.leaderboard_type.name].details[details_column_name];
                    }

                    series_data[details_column_name].push([
                        utc_date,
                        details_value
                    ]);
                });

                this.characters.forEach((character) => {
                    if(series_data[character.name] == null) {
                        series_data[character.name] = [];
                    }

                    let character_players = 0;

                    if(row.characters[character.name] != null && row.characters[character.name].categories[this.leaderboard_type.name] != null) {
                        character_players = row.characters[character.name].categories[this.leaderboard_type.name].players;
                    }

                    series_data[character.name].push([
                        utc_date,
                        character_players
                    ]);

                    this.leaderboard_type.details_columns.forEach((details_column_name) => {
                        const series_name = `${character.name}_${details_column_name}`;

                        if(series_data[series_name] == null) {
                            series_data[series_name] = [];
                        }

                        let character_details_value = 0;

                        if(row.characters[character.name] != null && row.characters[character.name].categories[this.leaderboard_type.name] != null) {
                            character_details_value = row.characters[character.name].categories[this.leaderboard_type.name].details[details_column_name];
                        }

                        series_data[series_name].push([
                            utc_date,
                            character_details_value
                        ]);
                    });
                });
            }
        }
    };

    export default CategoryRankingStatsChart;
</script>