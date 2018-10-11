<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <b-breadcrumb :items="breadcrumbs"></b-breadcrumb>
            </div>
        </div>
        <div class="row">
            <div class="col-12 pb-3">
                <h1>Players</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <necrotable :api_endpoint_url="api_endpoint_url" :header_columns="header_columns" :has_search="true" :filters="filters">
                    <template slot="table-row" slot-scope="{ row_index, row }">
                        <td>
                            <player-profile-link :id="row.id" :username="row.username"></player-profile-link>
                        </td>
                        <td>
                            {{ row.steam }}
                        </td>
                        <td>
                            {{ row.discord }}
                        </td>
                        <td>
                            {{ row.mixer }}
                        </td>
                        <td>
                            {{ row.reddit }}
                        </td>
                        <td>
                            {{ row.twitch }}
                        </td>
                        <td>
                            {{ row.twitter }}
                        </td>
                        <td>
                            {{ row.YouTube }}
                        </td>
                    </template>
                </necrotable>
            </div>
        </div>
    </div>
</template>

<script>
import NecroTable from '../table/NecroTable.vue';
import SiteDropdownFilter from '../table/filters/SiteDropdownFilter.vue';
import PlayerProfileLink from '../formatting/PlayerProfileLink.vue';

export default {
    name: 'PlayersPageTable',
    components: {
        'necrotable': NecroTable,
        'player-profile-link': PlayerProfileLink
    },
    data() {
        return {
            breadcrumbs: [
                {
                    text: 'Players',
                    href: '/players'
                }
            ],
            api_endpoint_url: '/api/1/players/steam',
            filters: [
                SiteDropdownFilter
            ],
            header_columns: [
                'Player',
                'Steam',
                'Discord',
                'Mixer',
                'Reddit',
                'Twitch',
                'Twitter',
                'YouTube'
            ]
        }
    }
};
</script>
