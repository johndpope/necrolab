<template>
    <div class="h-100 d-flex align-items-center site-icon-display">
        <span class="h3 mr-4 mb-0">
            <discord-icon v-if="name == 'discord'"></discord-icon>
            <mixer-icon v-else-if="name == 'mixer'"></mixer-icon>
            <patreon-icon v-else-if="name == 'patreon'"></patreon-icon>
            <reddit-icon v-else-if="name == 'reddit'"></reddit-icon>
            <twitch-icon v-else-if="name == 'twitch'"></twitch-icon>
            <twitter-icon v-else-if="name == 'twitter'"></twitter-icon>
            <youtube-icon v-else-if="name == 'youtube'"></youtube-icon>
        </span>
        <span>
            <template v-if="display_name != ''">
                {{ display_name }}
            </template>
            <template v-else>
                All
            </template>
        </span>
    </div>
</template>

<script>
import DiscordIcon from './DiscordIcon.vue';
import MixerIcon from './MixerIcon.vue';
import PatreonIcon from './PatreonIcon.vue';
import RedditIcon from './RedditIcon.vue';
import SteamIcon from './SteamIcon.vue';
import TwitchIcon from './TwitchIcon.vue';
import TwitterIcon from './TwitterIcon.vue';
import YoutubeIcon from './YoutubeIcon.vue';

const SiteIconDisplay = {
    name: 'site-icon-display',
    components: {
        'discord-icon': DiscordIcon,
        'mixer-icon': MixerIcon,
        'patreon-icon': PatreonIcon,
        'steam-icon': SteamIcon,
        'reddit-icon': RedditIcon,
        'twitch-icon': TwitchIcon,
        'twitter-icon': TwitterIcon,
        'youtube-icon': YoutubeIcon
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

export default SiteIconDisplay;
</script>
