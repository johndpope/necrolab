<template>
    <with-nav-body
        :loaded="loaded"
    >
        <div class="container-fluid">
            <div class="row">
                <div class="col-3 d-none d-lg-block">
                    <template
                        v-for="(navigation_group, navigation_group_key) in navigation"
                    >
                        <h4
                            :key="`group_${navigation_group_key}`"
                            class="border pt-3 pb-3 pl-3 bg-primary text-white"
                        >
                            {{ navigation_group.display_name }}
                        </h4>
                        <b-nav vertical pills>
                            <b-nav-item
                                v-for="(navigation_item, navigation_item_key) in navigation_group.options"
                                :key="`item_${navigation_item_key}`"
                                :active="active_link == navigation_item.name"
                                :href="`#${navigation_item.name}`"
                                @click="setActiveLink(navigation_item.name)"
                                active-class="bg-secondary text-white"
                            >
                                {{ navigation_item.display_name }}
                            </b-nav-item>
                        </b-nav>
                        <br />
                    </template>
                </div>
                <div class="col-12 col-lg-9">
                    <div class="d-flex pb-3">
                        <div>
                            <h2>
                                <leaderboard-source-icon-display :name="leaderboard_source.name">
                                </leaderboard-source-icon-display>
                            </h2>
                        </div>
                        <div class="pl-3 flex-grow-1">
                            <h2 v-if="player['player'] != null">
                                {{ player.player.username }}
                            </h2>
                        </div>
                        <div v-if="player['player'] != null && player.player['profile_url'] != null" class="pl-3">
                            <h3>
                                <site-link :url="player.player.profile_url">
                                </site-link>
                            </h3>
                        </div>
                    </div>
                    <div class="d-lg-none">
                        <dropdown-field
                            :default_options="navigation"
                            :label="'Jump To'"
                            :default_selected_option="navigation_by_name[active_link]"
                            @selectedValueChanged="navigateToSection"
                        >
                            <template slot="selected-option" slot-scope="{ selected, option_groups }">
                                {{ option_groups[selected.group_index] }} - {{ selected['display_name'] }}
                            </template>
                        </dropdown-field>
                    </div>
                    <hr />
                    <br />
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
import SiteLink from '../../sites/SiteLink.vue';
import LeaderboardSourceIconDisplay from '../../leaderboards/LeaderboardSourceIconDisplay.vue';
import DropdownField from '../../fields/dropdown/DropdownField.vue';

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
        'site-link': SiteLink,
        'leaderboard-source-icon-display': LeaderboardSourceIconDisplay,
        'dropdown-field': DropdownField
    },
    props: {
        visible_section: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            initial_load: true,
            player_id: '',
            player: {},
            profile_url: '',
            leaderboard_source: {},
            leaderboard_types: [],
            navigation: [],
            navigation_by_name: {},
            active_link: ''
        };
    },
    methods: {
        loadState(route_params) {
            this.leaderboard_source = this.$store.getters['leaderboard_sources/getSelected'];
            this.leaderboard_types = this.$store.getters['leaderboard_types/getAll'];
            this.player_id = route_params.player_id;


            if(this.initial_load) {
                this.profile_url = '/players/' + this.leaderboard_source.name + '/' + this.player_id;
                this.setActiveLink(this.$route.fullPath);
                this.generateNavigation();

                this.initial_load = false;
            }

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
        setActiveLink(active_link) {
            this.active_link = active_link;
        },
        getStatsUrl(page) {
            return this.profile_url + '/stats/' + page;
        },
        getPbUrl(leaderboard_type) {
            return this.profile_url + '/pbs/' + leaderboard_type.name;
        },
        getLeaderboardUrl(leaderboard_type) {
             return this.profile_url + '/leaderboards/' + leaderboard_type.name;
        },
        getRankingUrl(ranking_type) {
             return this.profile_url + '/rankings/' + ranking_type;
        },
        generateNavigation() {
            this.navigation = [];
            this.navigation_by_name = {};

            const overall_stats_link ={
                name: this.getStatsUrl('overall'),
                display_name: 'Overall'
            };

            this.navigation_by_name[overall_stats_link.name] = overall_stats_link;

            const by_release_stats_link = {
                name: this.getStatsUrl('by_release'),
                display_name: 'By Release'
            };

            this.navigation_by_name[by_release_stats_link.name] = by_release_stats_link;

            const stats_nav = [
                overall_stats_link,
                by_release_stats_link
            ];

            const pbs_nav = [];

            const leaderboards_nav = [];

            const rankings_nav = [];

            const power_rankings_link = {
                name: this.getRankingUrl('power'),
                display_name: 'Power'
            };

            rankings_nav.push(power_rankings_link);
            this.navigation_by_name[power_rankings_link.name] = power_rankings_link;

            const daily_rankings_link = {
                name: this.getRankingUrl('character'),
                display_name: 'Character'
            };

            rankings_nav.push(daily_rankings_link);
            this.navigation_by_name[daily_rankings_link.name] = daily_rankings_link;

            this.leaderboard_types.forEach((leaderboard_type) => {
                const category_rankings_link = {
                    name: this.getRankingUrl(leaderboard_type.name),
                    display_name: leaderboard_type.display_name
                };

                rankings_nav.push(category_rankings_link);
                this.navigation_by_name[category_rankings_link.name] = category_rankings_link;

                const leaderboards_link = {
                    name: this.getLeaderboardUrl(leaderboard_type),
                    display_name: leaderboard_type.display_name
                };

                leaderboards_nav.push(leaderboards_link);
                this.navigation_by_name[leaderboards_link.name] = leaderboards_link;

                if(leaderboard_type.name != 'daily') {
                    const pbs_link = {
                        name: this.getPbUrl(leaderboard_type),
                        display_name: leaderboard_type.display_name
                    };

                    pbs_nav.push(pbs_link);
                    this.navigation_by_name[pbs_link.name] = pbs_link;
                }
            });

            const info_link = {
                name: this.profile_url,
                display_name: 'Info'
            };

            this.navigation_by_name[info_link.name] = info_link;

            this.navigation = [
                {
                    display_name: 'General',
                    options: [
                        info_link
                    ]
                },
                {
                    display_name: "Stats",
                    options: stats_nav
                },
                {
                    display_name: 'PBs',
                    options: pbs_nav
                },
                {
                    display_name: 'Leaderboards',
                    options: leaderboards_nav
                },
                {
                    display_name: 'Rankings',
                    options: rankings_nav
                }
            ];
        },
        navigateToSection(name, value) {
            if(value != this.active_link) {
                this.setActiveLink(value);
                this.$router.push(value);
            }
        }
    }
};
</script>
