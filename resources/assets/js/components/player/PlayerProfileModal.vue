<template>
     <div>
        <template v-if="player['necrolab'] != null">
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
                                <a :href="'/players/' + player.necrolab.id">
                                    <profile-icon></profile-icon>
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
                <div slot="modal-footer" class="w-100">
                    <player-linked-sites :player="player"></player-linked-sites>
                </div>
            </b-modal>
        </template>
        <template v-else>
            {{ player.player.username }}
        </template>
    </div>
</template>

<script>
import bModal from 'bootstrap-vue/es/components/modal/modal';
import LinkIcon from  '../sites/LinkIcon.vue';
import ProfileIcon from './ProfileIcon.vue';
import PlayerLinkedSites from '../player/PlayerLinkedSites.vue';

/* Restore later
    <router-link :to="`/players/${leaderboard_source.name}/${player.player.id}`">
        {{ player.player.username }}
    </router-link>
 */

const PlayerProfileModal = {
    name: 'player-profile-modal',
    components: {
        'b-modal': bModal,
        'link-icon': LinkIcon,
        'profile-icon': ProfileIcon,
        'player-linked-sites': PlayerLinkedSites
    },
    props: {
        player: {
            type: Object,
            default: () => {
                return {};
            }
        },
        leaderboard_source: {
            type: Object
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
            
            if(
                this.player['necrolab'] != null && 
                this.player.necrolab['username'] != null &&
                this.player.necrolab.username.length > 0
            ) {
                username = this.player.necrolab.username;
            }
            else {
                username = this.player.player.username;
            }
            
            return username;
        }
    }
};

export default PlayerProfileModal;
</script>
