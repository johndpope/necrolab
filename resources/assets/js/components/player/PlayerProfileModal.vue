<template>
     <div>
        <b-button @click="modal_show = !modal_show" variant="primary">
            {{ username }}
        </b-button>
        <b-modal v-model="modal_show" :centered="true">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-3">
                        Avatar
                    </div>
                    <div class="col-7 pl-0">
                        <span class="h4 align-middle font-weight-bold">
                            {{ username }}
                        </span>
                    </div>
                    <div class="col-2 text-right">
                        <span class="h3 align-middle">
                            <a :href="'/players/' + player.id">
                                <profile-icon></profile-icon>
                            </a>
                        </span>
                    </div>
                </div>
                <!-- <div class="row">
                    <div class="col-6">
                    </div>
                </div> -->
            </div>
            <div slot="modal-footer" class="w-100">
                <div class="container-fluid pl-0 pr-0 ml-0 mr-0">
                    <div class="row">
                        <div v-if="player['steam'] != null" class="col-12">
                            <steam-profile-link :profile_data="player.steam"></steam-profile-link>
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
            </div>
        </b-modal>
    </div>
</template>

<script>
import bModal from 'bootstrap-vue/es/components/modal/modal';
import LinkIcon from  '../sites/LinkIcon.vue';
import ProfileIcon from './ProfileIcon.vue';
import SteamProfileLink from '../sites/SteamProfileLink.vue';
import DiscordProfileLink from '../sites/DiscordProfileLink.vue';
import MixerProfileLink from '../sites/MixerProfileLink.vue';
import RedditProfileLink from '../sites/RedditProfileLink.vue';
import TwitchProfileLink from '../sites/TwitchProfileLink.vue';
import TwitterProfileLink from '../sites/TwitterProfileLink.vue';
import YoutubeProfileLink from '../sites/YoutubeProfileLink.vue';

const PlayerProfileModal = {
    name: 'player-profile-modal',
    components: {
        'b-modal': bModal,
        'link-icon': LinkIcon,
        'profile-icon': ProfileIcon,
        'steam-profile-link': SteamProfileLink,
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
            default: () => {}
        }
    },
    data() {
        return {
            modal_show: false
        }
    },
    computed: {
        username() {
            let username = '';
            
            if(this.player['necrolab'] != null && this.player.necrolab.username.length > 0) {
                username = this.player.necrolab.username;
            }
            else {
                username = this.player.steam.username;
            }
            
            return username;
        }
    }
};

export default PlayerProfileModal;
</script>
