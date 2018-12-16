<template>
    <div class="h-100 leaderboard-source-icon-display">
        <span class="mr-1 mb-0">
            <steam-icon v-if="name == 'steam'"></steam-icon>
            <google-play-icon v-else-if="name == 'google_play'"></google-play-icon>
        </span>
        <span>
            <template v-if="display_name != ''">
                {{ display_name }}
            </template>
        </span>
    </div>
</template>

<script>
import SteamIcon from './SteamIcon.vue';
import GooglePlayIcon from './GooglePlayIcon.vue';

const LeaderboardSourceIconDisplay = {
    name: 'site-icon-display',
    components: {
        'steam-icon': SteamIcon,
        'google-play-icon': GooglePlayIcon
    },
    props: {
        name: {
            type: String,
            default: ''
        },
        display_name: {
            type: String,
            default: ''
        }
    }
};

export default LeaderboardSourceIconDisplay;
</script>
