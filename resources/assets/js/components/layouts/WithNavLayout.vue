<template>
    <div v-if="properties_loaded">
        <b-navbar toggleable="md" type="dark" variant="primary">
            <b-navbar-brand href="#/">
                <img src="/images/banners/banner_no_background_small.png" class="img-fluid" />
            </b-navbar-brand>
            <b-navbar-toggle target="nav_collapse"></b-navbar-toggle>
            <b-collapse is-nav id="nav_collapse">
                <b-navbar-nav>
                    <b-nav-item href="#/">Home</b-nav-item>
                    <b-nav-item-dropdown text="Rankings" right>
                        <template
                            v-for="(leaderboard_source, leaderboard_source_index) in leaderboardSources"
                        >
                            <div
                                class="h5 pl-3 pb-0"
                                :class="{ 'pt-3': leaderboard_source_index > 0 }"
                            >
                                <leaderboard-source-icon-display
                                    :name="leaderboard_source.name"
                                    :display_name="leaderboard_source.display_name"
                                >
                                </leaderboard-source-icon-display>
                            </div>

                            <a class="dropdown-item" :href="'#/rankings/power/' + leaderboard_source.name">Power</a>
                            <a class="dropdown-item" :href="'#/rankings/character/' + leaderboard_source.name">Characters</a>

                            <a
                                v-for="leaderboard_type in leaderboardTypes"
                                class="dropdown-item"
                                :href="'#/rankings/' + leaderboard_type.name + '/' + leaderboard_source.name"
                            >
                                {{ leaderboard_type.display_name }}
                            </a>
                        </template>
                    </b-nav-item-dropdown>
                    <b-nav-item-dropdown text="Leaderboards" right>
                        <template
                            v-for="(leaderboard_source, leaderboard_source_index) in leaderboardSources"
                        >
                            <div
                                class="h5 pl-3 pb-0"
                                :class="{ 'pt-3': leaderboard_source_index > 0 }"
                            >
                                <leaderboard-source-icon-display
                                    :name="leaderboard_source.name"
                                    :display_name="leaderboard_source.display_name"
                                >
                                </leaderboard-source-icon-display>
                            </div>
                            <a
                                v-for="leaderboard_type in leaderboardTypes"
                                class="dropdown-item"
                                :href="'#/leaderboards/' + leaderboard_type.name + '/' + leaderboard_source.name"
                            >
                                {{ leaderboard_type.display_name }}
                            </a>
                        </template>
                    </b-nav-item-dropdown>
                    <b-nav-item-dropdown text="Players" right>
                        <a class="dropdown-item" href="#/players">Necrolab</a>
                        <div class="dropdown-divider"></div>
                        <a
                            v-for="leaderboard_source in leaderboardSources"
                            :key="leaderboard_source.name"
                            class="dropdown-item"
                            :href="'#/players/' + leaderboard_source.name"
                        >
                            <leaderboard-source-icon-display
                                :name="leaderboard_source.name"
                                :display_name="leaderboard_source.display_name"
                            >
                            </leaderboard-source-icon-display>
                        </a>
                    </b-nav-item-dropdown>
                </b-navbar-nav>
            </b-collapse>
        </b-navbar>
        <div class="container-fluid">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
import bNavbar from 'bootstrap-vue/es/components/navbar/navbar';
import bNavItem from 'bootstrap-vue/es/components/nav/nav-item';
import bNavItemDropdown from 'bootstrap-vue/es/components/nav/nav-item-dropdown';
import bNavbarToggle from 'bootstrap-vue/es/components/navbar/navbar-toggle';
import bNavbarBrand from 'bootstrap-vue/es/components/navbar/navbar-brand';
import bNarbarNav from 'bootstrap-vue/es/components/navbar/navbar-nav';
import bCollapse from 'bootstrap-vue/es/components/collapse/collapse';
import LeaderboardSourceIconDisplay from '../leaderboards/LeaderboardSourceIconDisplay.vue';

/* Restore Later
<b-navbar-nav class="ml-auto">
<!-- @auth
    <b-nav-item href="#">Welcome [user]!</b-nav-item> -->
<!-- @else -->
    <b-button href="#/login" class="my-2 my-sm-0">Log In</b-button>
<!-- @endauth -->
</b-navbar-nav>
*/

const WithNavLayout = {
    name: 'with-nav-layout',
    components: {
        'b-navbar': bNavbar,
        'b-nav-item': bNavItem,
        'b-nav-item-dropdown': bNavItemDropdown,
        'b-navbar-toggle': bNavbarToggle,
        'b-navbar-brand': bNavbarBrand,
        'b-navbar-nav': bNarbarNav,
        'b-collapse': bCollapse,
        'leaderboard-source-icon-display': LeaderboardSourceIconDisplay
    },
    props: {
        title: {
            type: String,
            default: ''
        },
        sub_title: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            properties_loaded: false
        };
    },
    computed: {
        leaderboardSources() {
            return this.$store.getters['leaderboard_sources/getAll'];
        },
        leaderboardTypes() {
            return this.$store.getters['leaderboard_types/getAll'];
        }
    },
    created() {
        this.properties_loaded = true;
    }
};

export default WithNavLayout;
</script>
