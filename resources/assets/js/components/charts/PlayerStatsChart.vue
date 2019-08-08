<script>
import StatsChart from './StatsChart.vue';
import parse from 'date-fns/parse';

const PlayerStatsChart = {
    name: 'player-stats-chart',
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

            y_axis.push(this.getSingleYAxis('PBs'));
            y_axis.push(this.getSingleYAxis('Leaderboards'));
            y_axis.push(this.getSingleYAxis('WRs'));
            y_axis.push(this.getSingleYAxis('Dailies'));
            y_axis.push(this.getSingleYAxis('Seeded PBs'));
            y_axis.push(this.getSingleYAxis('Unseeded PBs'));

            this.details_columns.forEach((details_column) => {
                y_axis.push(this.getSingleYAxis(details_column.display_name));
            });

            return y_axis;
        },
        getSeries() {
            this.resetColorIndex();

            const self = this;

            const series = [];

            series.push(self.getSingleSeries('PBs', 0));
            series.push(self.getSingleSeries('Leaderboards', 1));
            series.push(self.getSingleSeries('WRs', 2));
            series.push(self.getSingleSeries('Dailies', 3));
            series.push(self.getSingleSeries('Seeded PBs', 4));
            series.push(self.getSingleSeries('Unseeded PBs', 5));

            self.details_columns.forEach((details_column, index) => {
                series.push(self.getSingleSeries(details_column.display_name, index + 6, {
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

            this.addToSeriesData(series_data, 'pbs', [
                utc_date,
                row.pbs
            ]);

            this.addToSeriesData(series_data, 'leaderboards', [
                utc_date,
                row.leaderboards
            ]);

            this.addToSeriesData(series_data, 'first_place_ranks', [
                utc_date,
                row.first_place_ranks
            ]);

            this.addToSeriesData(series_data, 'dailies', [
                utc_date,
                row.dailies
            ]);

            this.addToSeriesData(series_data, 'seeded_pbs', [
                utc_date,
                row.seeded_pbs
            ]);

            this.addToSeriesData(series_data, 'unseeded_pbs', [
                utc_date,
                row.unseeded_pbs
            ]);

            this.details_columns.forEach((details_column) => {
                this.addToSeriesData(series_data, details_column.name, [
                    utc_date,
                    row.details[details_column.name] || 0
                ]);
            });
        }
    }
};

export default PlayerStatsChart;
</script>