<template>
    <div v-if="properties_loaded">
        <b-navbar toggleable="md" type="dark" variant="primary">
            <b-navbar-brand href="#/">
                <img src="/images/banners/banner_no_background.png" class="img-fluid" />
            </b-navbar-brand>
            <b-navbar-toggle target="nav_collapse"></b-navbar-toggle>
            <b-collapse is-nav id="nav_collapse">
                <b-navbar-nav>
                    <b-nav-item href="#/">Home</b-nav-item>
                    <b-nav-item-dropdown text="Rankings" right>
                        <template
                            v-for="(leaderboard_source, leaderboard_source_index) in leaderboard_sources"
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
                                v-for="leaderboard_type in leaderboard_types" 
                                class="dropdown-item"
                                :href="'#/rankings/' + leaderboard_type.name + '/' + leaderboard_source.name"
                            >
                                {{ leaderboard_type.display_name }}
                            </a>
                        </template>
                    </b-nav-item-dropdown>
                    <b-nav-item-dropdown text="Leaderboards" right>
                        <template
                            v-for="(leaderboard_source, leaderboard_source_index) in leaderboard_sources"
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
                                v-for="leaderboard_type in leaderboard_types" 
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
                            v-for="leaderboard_source in leaderboard_sources" 
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

                <b-navbar-nav class="ml-auto">
                    <!-- @auth
                        <b-nav-item href="#">Welcome [user]!</b-nav-item> -->
                    <!-- @else -->
                        <b-button href="#/login" class="my-2 my-sm-0">Log In</b-button>
                    <!-- @endauth -->
                </b-navbar-nav>
            </b-collapse>
        </b-navbar>
        <div v-if="show_body" class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <b-breadcrumb :items="breadcrumbItems"></b-breadcrumb>
                </div>
            </div>
            <div v-if="title != ''" class="row">
                <div class="col-12 pb-3">
                    <h1>{{ title }}</h1>
                </div>
            </div>
            <div v-if="sub_title != ''" class="row">
                <div class="col-12 pb-3">
                    <h3>{{ sub_title }}</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <slot></slot>
                </div>
            </div>
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
import bBreadcrumb from 'bootstrap-vue/es/components/breadcrumb/breadcrumb';

import LeaderboardSourceIconDisplay from '../leaderboards/sources/LeaderboardSourceIconDisplay.vue';

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
        'b-breadcrumb': bBreadcrumb,
        'leaderboard-source-icon-display': LeaderboardSourceIconDisplay
    },
    props: {
        breadcrumbs: {
            type: Array,
            default: () => {
                return [];
            }
        },
        title: {
            type: String,
            default: ''
        },
        sub_title: {
            type: String,
            default: ''
        },
        show_body: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            properties_loaded: false,
            leaderboard_sources: [],
            leaderboard_types: []
        };
    },
    computed: {
        breadcrumbItems() {
            let breadcrumbs = this.breadcrumbs;
            
            if(breadcrumbs.length == 0) {
                breadcrumbs = this.$store.getters['breadcrumbs/getAll'];
            }
            
            breadcrumbs.unshift({
                text: 'Home',
                href: '#/'
            });
            
            return breadcrumbs;
        }
    },
    created() {        
        let promises = [
            this.$store.dispatch('leaderboard_sources/loadAll'),
            this.$store.dispatch('leaderboard_types/loadAll')
        ];
        
        Promise.all(promises)
            .then(() => {
                this.leaderboard_sources = this.$store.getters['leaderboard_sources/getAll'];
                this.leaderboard_types = this.$store.getters['leaderboard_types/getAll'];
                
                this.properties_loaded = true;
            });
    }
};

export default WithNavLayout;
</script>
