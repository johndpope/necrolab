<script>
    import PlayerPowerRankingStatsChart from './PlayerPowerRankingStatsChart.vue';

    const PlayerCharacterRankingStatsChart = {
        name: 'player-character-ranking-stats-chart',
        extends: PlayerPowerRankingStatsChart,
        props: {
            character: {
                type: Object,
                default: () => {}
            }
        },
        methods: {
            getSeriesData() {
                const series_data = {};

                if(this.dataset.total_records > 0) {
                    this.dataset.data.slice().reverse().forEach((row) => {
                        let character_row = {};

                        if(row.characters[this.character.name] != null) {
                            character_row = row.characters[this.character.name];

                            character_row.date = row.date;

                            this.processSeriesDataRow(series_data, character_row);
                        }
                    });
                }

                return Object.values(series_data);
            }
        }
    };

    export default PlayerCharacterRankingStatsChart;
</script>