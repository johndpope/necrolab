<template>
    <div class="table-responsive pb-0">
        <h5>Bests</h5>
        <table class="table table-sm table-bordered pb-0 mb-0">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Character</th>
                    <th v-if="record.bests.hasOwnProperty('release')">Release</th>
                    <th>Mode</th>
                    <th>Seeded Type</th>
                    <th>Multiplayer Type</th>
                    <th>Soundtrack</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="text-sm-right text-lg-left">
                            {{ $store.getters['leaderboard_types/getByName'](getBest('leaderboard_type')).display_name }}
                        </div>
                    </td>
                    <td>
                        <div class="text-sm-right text-lg-left">
                            {{ $store.getters['characters/getByName'](getBest('character')).display_name }}
                        </div>
                    </td>
                    <td v-if="record.bests.hasOwnProperty('release')">
                        <div class="text-sm-right text-lg-left">
                            {{ $store.getters['releases/getByName'](getBest('release')).display_name }}
                        </div>
                    </td>
                    <td>
                        <div class="text-sm-right text-lg-left">
                            {{ $store.getters['modes/getByName'](getBest('mode')).display_name }}
                        </div>
                    </td>
                    <td>
                        <div class="text-sm-right text-lg-left">
                            {{ $store.getters['seeded_types/getByName'](getBest('seeded_type')).display_name }}
                        </div>
                    </td>
                    <td>
                        <div class="text-sm-right text-lg-left">
                            {{ $store.getters['multiplayer_types/getByName'](getBest('multiplayer_type')).display_name }}
                        </div>
                    </td>
                    <td>
                        <div class="text-sm-right text-lg-left">
                            {{ $store.getters['soundtracks/getByName'](getBest('soundtrack')).display_name }}
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
const PlayerStatsDetailsTable = {
    name: 'player-stats-details-table',
    props: {
        record: {
            type: Object,
            default: () => {}
        }
    },
    methods: {
        getBest(best_name) {
            let value = null;

            if(this.record.bests[best_name] != null) {
                value = this.record.bests[best_name];
            }

            return value;
        }
    }
};

export default PlayerStatsDetailsTable;
</script>
