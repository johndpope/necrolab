<script>
    import StatsChart from './StatsChart.vue';
    import parse from 'date-fns/parse';

    const PlayerCategoryRankingStatsChart = {
        name: 'player-category-ranking-stats-chart',
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

                y_axis.push(this.getSingleYAxis('Rank'));

                y_axis.push(this.getSingleYAxis('Points'));

                this.leaderboard_type.details_columns.forEach((details_column_name) => {
                    const details_column = this.details_columns_by_name[details_column_name];

                    y_axis.push(this.getSingleYAxis(details_column.display_name));
                });

                this.characters.forEach((character) => {
                    y_axis.push(this.getSingleYAxis(`${character.display_name} (Rank)`));

                    y_axis.push(this.getSingleYAxis(`${character.display_name} (Points)`));

                    this.leaderboard_type.details_columns.forEach((details_column_name) => {
                        const details_column = this.details_columns_by_name[details_column_name];

                        y_axis.push(this.getSingleYAxis(`${character.display_name} (${details_column.display_name})`));
                    });
                });

                return y_axis;
            },
            getSeries() {
                this.resetDashStyleIndex();
                this.resetColorIndex();

                const self = this;
                let y_axis_index = 0;
                const series = [];

                series.push(self.getSingleSeries('Rank', y_axis_index));

                y_axis_index += 1;

                series.push(self.getSingleSeries('Points', y_axis_index));

                y_axis_index += 1;

                this.leaderboard_type.details_columns.forEach((details_column_name) => {
                    const details_column = self.details_columns_by_name[details_column_name];

                    series.push(self.getSingleSeries(details_column.display_name, y_axis_index, {
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

                    y_axis_index += 1;
                });

                this.characters.forEach((character) => {
                    series.push(self.getSingleSeries(`${character.display_name} (Rank)`, y_axis_index, {
                        visible: false
                    }));

                    y_axis_index += 1;

                    series.push(self.getSingleSeries(`${character.display_name} (Points)`, y_axis_index, {
                        visible: false
                    }));

                    y_axis_index += 1;

                    this.leaderboard_type.details_columns.forEach((details_column_name) => {
                        const details_column = self.details_columns_by_name[details_column_name];

                        series.push(self.getSingleSeries(`${character.display_name} (${details_column.display_name})`, y_axis_index, {
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

                        y_axis_index += 1;
                    });
                });

                return series;
            },
            processSeriesDataRow(series_data, row) {
                const date = parse(row.date, 'YYYY-MM-DD');
                const utc_date = Date.UTC(date.getFullYear(), date.getMonth(), date.getDate());

                if(row.categories[this.leaderboard_type.name] == null) {
                    return;
                }

                this.addToSeriesData(series_data, 'rank', [
                    utc_date,
                    row.categories[this.leaderboard_type.name].rank
                ]);

                this.addToSeriesData(series_data, 'points', [
                    utc_date,
                    row.categories[this.leaderboard_type.name].points
                ]);

                this.leaderboard_type.details_columns.forEach((details_column_name) => {
                    let details_value = 0;

                    if(row.categories[this.leaderboard_type.name].details[details_column_name] != null) {
                        details_value = row.categories[this.leaderboard_type.name].details[details_column_name];
                    }

                    this.addToSeriesData(series_data, details_column_name, [
                        utc_date,
                        details_value
                    ]);
                });

                this.characters.forEach((character) => {
                    let character_rank = 0;

                    if(row.characters[character.name] != null && row.characters[character.name].categories[this.leaderboard_type.name] != null) {
                        character_rank = row.characters[character.name].categories[this.leaderboard_type.name].rank;
                    }

                    this.addToSeriesData(series_data, `${character.name}_rank`, [
                        utc_date,
                        character_rank
                    ]);

                    let character_points = 0;

                    if(row.characters[character.name] != null && row.characters[character.name].categories[this.leaderboard_type.name] != null) {
                        character_points = row.characters[character.name].categories[this.leaderboard_type.name].points;
                    }

                    this.addToSeriesData(series_data, `${character.name}_points`, [
                        utc_date,
                        character_points
                    ]);

                    this.leaderboard_type.details_columns.forEach((details_column_name) => {
                        let character_details_value = 0;

                        if(row.characters[character.name] != null && row.characters[character.name].categories[this.leaderboard_type.name] != null) {
                            character_details_value = row.characters[character.name].categories[this.leaderboard_type.name].details[details_column_name];
                        }

                        this.addToSeriesData(series_data, `${character.name}_${details_column_name}`, [
                            utc_date,
                            character_details_value
                        ]);
                    });
                });
            }
        }
    };

    export default PlayerCategoryRankingStatsChart;
</script>