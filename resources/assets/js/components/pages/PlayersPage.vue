<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <b-breadcrumb :items="breadcrumbs"></b-breadcrumb>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <necrotable :api_endpoint_url="api_endpoint_url" :columns="columns" has_search="has_search" :filters="filters"></necrotable>
            </div>
        </div>
    </div>
</template>

<script>
import NecroTable from '../table/NecroTable.vue';
import SiteDropdownFilter from '../table/filters/SiteDropdownFilter.vue';
import PlayerProfileField from '../table/fields/PlayerProfileField.vue';

export default {
    name: 'PlayersPageTable',
    components: {
        'necrotable': NecroTable
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
            columns: [
                {
                    name: 'username',
                    label: 'Player',
                    field: 'username',
                    component: PlayerProfileField
                },
                {
                    name: 'steam_profile',
                    label: 'Steam',
                    field: 'steam'
                },
                {
                    name: 'discord_profile',
                    label: 'Discord',
                    field: 'discord'
                },
                {
                    name: 'mixer_profile',
                    label: 'Mixer',
                    field: 'mixer'
                },
                {
                    name: 'reddit_profile',
                    label: 'Reddit',
                    field: 'reddit'
                },
                {
                    name: 'twitch_profile',
                    label: 'Twitch',
                    field: 'twitch'
                },
                {
                    name: 'twitter_profile',
                    label: 'Twitter',
                    field: 'twitter'
                },
                {
                    name: 'youtube_profile',
                    label: 'YouTube',
                    field: 'youtube'
                }
            ],
            has_search: false
        }
    }
};
</script>
