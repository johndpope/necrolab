<template>
    <div class="container-fluid pl-0 pr-0 ml-0 mr-0">
        <div class="row">
            <div v-if="player['steam'] != null" class="col-12">
                <steam-profile-link :profile_data="player.steam"></steam-profile-link>
            </div>
            <div v-if="player['google_play'] != null" class="col-12">
                <google-play-profile-link :profile_data="player.google_play"></google-play-profile-link>
            </div>
            <div v-if="player['discord'] != null" class="col-12 mt-2">
                <discord-profile-link :profile_data="player.discord"></discord-profile-link>
            </div>
            <div v-if="player['mixer'] != null" class="col-12 mt-2">
                <mixer-profile-link :profile_data="player.mixer"></mixer-profile-link>
            </div>
            <div v-if="player['reddit'] != null" class="col-12 mt-2">
                <reddit-profile-link :profile_data="player.reddit"></reddit-profile-link>
            </div>
            <div v-if="player['twitch'] != null" class="col-12 mt-2">
                <twitch-profile-link :profile_data="player.twitch"></twitch-profile-link>
            </div>
            <div v-if="player['twitter'] != null" class="col-12 mt-2">
                <twitter-profile-link :profile_data="player.twitter"></twitter-profile-link>
            </div>
            <div v-if="player['youtube'] != null" class="col-12 mt-2">
                <youtube-profile-link :profile_data="player.youtube"></youtube-profile-link>
            </div>
        </div>
    </div>
</template>

<script>
import SteamProfileLink from '../sites/SteamProfileLink.vue';
import GooglePlayProfileLink from '../sites/GooglePlayProfileLink.vue';
import DiscordProfileLink from '../sites/DiscordProfileLink.vue';
import MixerProfileLink from '../sites/MixerProfileLink.vue';
import RedditProfileLink from '../sites/RedditProfileLink.vue';
import TwitchProfileLink from '../sites/TwitchProfileLink.vue';
import TwitterProfileLink from '../sites/TwitterProfileLink.vue';
import YoutubeProfileLink from '../sites/YoutubeProfileLink.vue';

const PlayerLinkedSites = {
    name: 'player-linked-sites',
    components: {
        'steam-profile-link': SteamProfileLink,
        'google-play-profile-link': GooglePlayProfileLink,
        'discord-profile-link': DiscordProfileLink,
        'mixer-profile-link': MixerProfileLink,
        'reddit-profile-link': RedditProfileLink,
        'twitch-profile-link': TwitchProfileLink,
        'twitter-profile-link': TwitterProfileLink,
        'youtube-profile-link': YoutubeProfileLink
    },
    props: {
        player: {
            type: Object,
            default: () => {
                return {};
            }
        }
    }
};

export default PlayerLinkedSites;
</script>
