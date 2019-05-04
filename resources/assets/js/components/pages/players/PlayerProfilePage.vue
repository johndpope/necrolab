<template>
    <with-nav-body
        :loaded="loaded"
    >
        <div class="container-fluid">
            <div class="row">
                <div class="col-3 d-sm-none d-lg-block">
                    <h4 class="border pt-3 pb-3 pl-3 bg-primary text-white">
                        General
                    </h4>
                    <b-nav vertical pills>
                        <b-nav-item 
                            :active="active_link == 'info'"
                            :href="profile_url"
                            @click="setActiveLink('info')"
                        >
                            Info
                        </b-nav-item>
                        <b-nav-item 
                            :active="active_link == 'general_connections'"
                            :href="profile_url + '/connections'"
                            @click="setActiveLink('general_connections')"
                        >
                            Connections
                        </b-nav-item>
                        <b-nav-item 
                            :active="active_link == 'general_support'"
                            :href="profile_url + '/support'"
                            @click="setActiveLink('general_support')"
                        >
                            Support
                        </b-nav-item>
                    </b-nav>
                    <h4 class="border mt-3 pt-3 pb-3 pl-3 bg-primary text-white">
                        PBs
                    </h4>
                    <b-nav vertical pills>
                        <b-nav-item 
                            v-for="leaderboard_type in leaderboard_types" 
                            :key="leaderboard_type.name"
                            v-if="leaderboard_type.name != 'daily'"
                            :href="getPbUrl(leaderboard_type)"
                            :active="active_link == 'pbs_' + leaderboard_type.name"
                            @click="setActiveLink('pbs_' + leaderboard_type.name)"
                        >
                            {{ leaderboard_type.display_name }}
                        </b-nav-item>
                    </b-nav>
                    <h4 class="border mt-3 pt-3 pb-3 pl-3 bg-primary text-white">
                        Leaderboards
                    </h4>
                    <b-nav vertical pills>
                        <b-nav-item 
                            v-for="leaderboard_type in leaderboard_types" 
                            :key="leaderboard_type.name"
                            :href="getLeaderboardUrl(leaderboard_type)"
                            :active="active_link == 'leaderboards_' + leaderboard_type.name"
                            @click="setActiveLink('leaderboards_' + leaderboard_type.name)"
                        >
                            {{ leaderboard_type.display_name }}
                        </b-nav-item>
                    </b-nav>
                    <h4 class="border mt-3 pt-3 pb-3 pl-3 bg-primary text-white">
                        Rankings
                    </h4>
                    <b-nav vertical pills>
                        <b-nav-item
                            :href="getRankingUrl('power')"
                            :active="active_link == 'rankings_power'"
                            @click="setActiveLink('rankings_power')"
                        >
                            Power
                        </b-nav-item>
                        <b-nav-item
                            :href="getRankingUrl('character')"
                            :active="active_link == 'rankings_character'"
                            @click="setActiveLink('rankings_character')"
                        >
                            Character
                        </b-nav-item>
                        <b-nav-item 
                            v-for="leaderboard_type in leaderboard_types" 
                            :key="'rankings_' + leaderboard_type.name"
                            :href="getRankingUrl(leaderboard_type.name)"
                            :active="active_link == 'rankings_' + leaderboard_type.name"
                            @click="setActiveLink('rankings_' + leaderboard_type.name)"
                        >
                            {{ leaderboard_type.display_name }}
                        </b-nav-item>
                    </b-nav>
                </div>
                <div class="col-12 col-lg-9">
                    <div class="d-flex pb-3">
                        <div>
                            <h1>
                                <leaderboard-source-icon-display :name="leaderboard_source.name">
                                </leaderboard-source-icon-display>
                            </h1>
                        </div>
                        <div class="pl-3">
                            <h1>
                                {{ player.player.username }}
                            </h1>
                        </div>
                    </div>
                    <router-view></router-view>
                </div>
            </div>
        </div>
    </with-nav-body>
</template>

<script>
import BasePage from '../BasePage.vue';
import WithNavBody from '../../layouts/WithNavBody.vue';
import bNav from 'bootstrap-vue/es/components/nav/nav';
import bNavbar from 'bootstrap-vue/es/components/navbar/navbar';
import bNavItem from 'bootstrap-vue/es/components/nav/nav-item';
import bNavItemDropdown from 'bootstrap-vue/es/components/nav/nav-item-dropdown';
import bNavbarToggle from 'bootstrap-vue/es/components/navbar/navbar-toggle';
import bNavbarBrand from 'bootstrap-vue/es/components/navbar/navbar-brand';
import bCollapse from 'bootstrap-vue/es/components/collapse/collapse';
import LeaderboardSourceIconDisplay from '../../leaderboards/LeaderboardSourceIconDisplay.vue';

export default {
    extends: BasePage,
    name: 'player-profile-page',
    components: {
        'with-nav-body': WithNavBody,
        'b-nav': bNav,
        'b-navbar': bNavbar,
        'b-nav-item': bNavItem,
        'b-nav-item-dropdown': bNavItemDropdown,
        'b-navbar-toggle': bNavbarToggle,
        'b-navbar-brand': bNavbarBrand,
        'b-collapse': bCollapse,
        'leaderboard-source-icon-display': LeaderboardSourceIconDisplay
    },
    props: {
        visible_section: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            player_id: '',
            player: {},
            profile_url: '',
            leaderboard_source: {},
            leaderboard_types: [],
            active_link: ''
        };
    },
    methods: {
        loadState(route_params) {
            this.leaderboard_source = this.$store.getters['leaderboard_sources/getSelected'];
            this.leaderboard_types = this.$store.getters['leaderboard_types/getAll'];
            this.player_id = route_params.player_id;
            
            this.generateProfileUrl();
            
            const promise = this.$store.dispatch('players/load', {
                leaderboard_source: this.leaderboard_source.name,
                player_id: this.player_id
            });

            promise.then(() => {
                this.player = this.$store.getters['players/get'](this.leaderboard_source.name, this.player_id);
                
                if(this.player['player'] != null) {
                    this.loaded = true;
                }
            });
        },
        generateProfileUrl() {
            this.profile_url = '#/players/' + this.leaderboard_source.name + '/' + this.player_id;
        },
        setActiveLink(active_link) {
            this.active_link = active_link;
        },
        getPbUrl(leaderboard_type) {
            return this.profile_url + '/pbs/' + leaderboard_type.name;
        },
        getLeaderboardUrl(leaderboard_type) {
             return this.profile_url + '/leaderboards/' + leaderboard_type.name;
        },
        getRankingUrl(ranking_type) {
             return this.profile_url + '/rankings/' + ranking_type;
        }
    }
};
</script>
