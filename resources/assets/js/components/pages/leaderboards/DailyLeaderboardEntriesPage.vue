<template>
    <with-nav-body 
        :loaded="loaded"
        :breadcrumbs="breadcrumbs"
        title="Daily Leaderboard Entries"
        :sub_title="subTitle"
    >
        <necrotable 
            :api_endpoint_url="api_endpoint_url"
            :header_columns="headerColumns" 
            :has_search="true" 
            :has_action_column="true" 
            :filters="filters"
            :default_request_parameters="apiRequestParameters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td>
                    {{ row.rank }}
                </td>
                <td>
                    <player-profile-modal 
                        :leaderboard_source="leaderboard_source"
                        :player="row.player"
                    >
                    </player-profile-modal>
                </td>
                <td v-for="details_column in details_columns">
                    <details-column
                        :details_name="details_column.name"
                        :details_value="row.pb.details[details_column.name] != null ? row.pb.details[details_column.name] : ''"
                    >
                    </details-column>
                </td>
                <td v-if="leaderboard_type.show_seed === 1">
                    <seed :record="row"></seed>
                </td>
            </template>
            <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
            </template>
            <template slot="row-details" slot-scope="{ row }">
                <leaderboard-entry-details-table :record="row">
                </leaderboard-entry-details-table>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
import BasePage from '../BasePage.vue';
import WithNavBody from '../../layouts/WithNavBody.vue';
import NecroTable from '../../table/NecroTable.vue';
import SiteDropdownFilter from '../../table/filters/SiteDropdownFilter.vue';
import PlayerProfileModal from '../../player/PlayerProfileModal.vue';
import Seed from '../../leaderboards/Seed.vue';
import DetailsColumn from '../../formatting/DetailsColumn.vue';
import ToggleDetails from '../../table/action_columns/ToggleDetails.vue';
import LeaderboardEntryDetailsTable from '../../table/LeaderboardEntryDetailsTable.vue';

export default {
    extends: BasePage,
    name: 'daily-leaderboard-entries-page',
    components: {
        'with-nav-body': WithNavBody,
        'necrotable': NecroTable,
        'player-profile-modal': PlayerProfileModal,
        'seed': Seed,
        'details-column': DetailsColumn,
        'toggle-details': ToggleDetails,
        'leaderboard-entry-details-table': LeaderboardEntryDetailsTable
    },
    data() {
        return {
            date: '',
            leaderboard_source: {},
            character: {},
            release: {},
            mode: {},
            multiplayer_type: {},
            soundtrack: {},
            leaderboard_type: {},
            details_columns: [],
            api_endpoint_url: '/api/1/leaderboards/daily/entries',
            filters: [
                SiteDropdownFilter
            ]
        }
    },
    computed: {
        breadcrumbs() {
            return [
                {
                    text: 'Leaderboards'
                },
                {
                    text: 'Daily',
                    href: '#/leaderboards/daily'
                },
                {
                    text: 'Entries - ' + this.subTitle
                }
            ];
        },
        apiRequestParameters() {
            return {
                leaderboard_source: this.leaderboard_source.name,
                character: this.character.name,
                release: this.release.name,
                mode: this.mode.name,
                multiplayer_type: this.multiplayer_type.name,
                soundtrack: this.soundtrack.name,
                date: this.date
            };
        },
        subTitle() {
            return this.leaderboard_source.display_name + ' ' + 
                this.character.display_name + ' ' + 
                this.release.display_name + ' ' + 
                this.mode.display_name + ' ' + 
                this.multiplayer_type.display_name + ' ' + 
                this.soundtrack.display_name + ' ' + 
                this.date;
        },
        headerColumns() {
            const header_columns = [
                'Rank',
                'Player'
            ];
            
            this.details_columns.forEach((details_column) => {
                header_columns.push(details_column.display_name);
            });
            
            if(this.leaderboard_type.show_seed === 1) {
                header_columns.push('Seed');
            }
            
            return header_columns;
        }
    },
    methods: {
        loadState(route_params) {
            this.$store.commit('characters/setFilterStores', [
                'leaderboard_sources',
                'leaderboard_types',
                'releases',
                'modes'
            ]);
            
            this.$store.commit('releases/setFilterStores', [
                'leaderboard_sources'
            ]);
            
            this.$store.commit('modes/setFilterStores', [
                'releases',
                'leaderboard_types'
            ]);
            
            this.$store.commit('multiplayer_types/setFilterStores', [
                'leaderboard_sources'
            ]);

            this.$store.commit('leaderboard_types/setSelected', 'daily');
            
            this.leaderboard_source = this.$store.getters['leaderboard_sources/getSelected'];
            this.leaderboard_type = this.$store.getters['leaderboard_types/getSelected'];
            this.character = this.$store.getters['characters/getSelected'];
            this.release = this.$store.getters['releases/getSelected'];
            this.mode = this.$store.getters['modes/getSelected'];
            this.multiplayer_type = this.$store.getters['multiplayer_types/getSelected'];
            this.soundtrack = this.$store.getters['soundtracks/getSelected'];
            this.details_columns = this.$store.getters['details_columns/getAllByNames'](this.leaderboard_type.details_columns);
            
            this.date = route_params.date;
            
            this.loaded = true;
        }
    }
};
</script>
