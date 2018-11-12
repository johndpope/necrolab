<template>
    <div>
        <b-navbar toggleable="md" type="dark" variant="primary">
            <b-navbar-brand href="/">
                <img src="/images/banners/banner_no_background.png" class="img-fluid" />
            </b-navbar-brand>
            <b-navbar-toggle target="nav_collapse"></b-navbar-toggle>
            <b-collapse is-nav id="nav_collapse">
                <b-navbar-nav>
                    <b-nav-item href="#/">Home</b-nav-item>
                    <b-nav-item-dropdown text="Rankings" right>
                        <b-dropdown-item href="#/rankings/power">Power</b-dropdown-item>
                        <b-dropdown-item href="#/rankings/score">Score</b-dropdown-item>
                        <b-dropdown-item href="#/rankings/speed">Speed</b-dropdown-item>
                        <b-dropdown-item href="#/rankings/deathless">Deathless</b-dropdown-item>
                        <b-dropdown-item href="#/rankings/character">Character</b-dropdown-item>
                        <b-dropdown-item href="#/rankings/daily">Daily</b-dropdown-item>
                    </b-nav-item-dropdown>
                    <b-nav-item-dropdown text="Leaderboards" right>
                        <b-dropdown-item href="#/leaderboards/score">Score</b-dropdown-item>
                        <b-dropdown-item href="#/leaderboards/speed">Speed</b-dropdown-item>
                        <b-dropdown-item href="#/leaderboards/deathless">Deathless</b-dropdown-item>
                        <b-dropdown-item href="#/leaderboards/daily">Daily</b-dropdown-item>
                    </b-nav-item-dropdown>
                    <b-nav-item href="#/players">Players</b-nav-item>
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
import bDropdownItem from 'bootstrap-vue/es/components/dropdown/dropdown-item';
import bCollapse from 'bootstrap-vue/es/components/collapse/collapse';
import bBreadcrumb from 'bootstrap-vue/es/components/breadcrumb/breadcrumb';

const WithNavLayout = {
    name: 'with-nav-layout',
    components: {
        'b-navbar': bNavbar,
        'b-nav-item': bNavItem,
        'b-nav-item-dropdown': bNavItemDropdown,
        'b-navbar-toggle': bNavbarToggle,
        'b-navbar-brand': bNavbarBrand,
        'b-navbar-nav': bNarbarNav,
        'b-dropdown-item': bDropdownItem,
        'b-collapse': bCollapse,
        'b-breadcrumb': bBreadcrumb
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
        show_body: {
            type: Boolean,
            default: true
        }
    },
    computed: {
        breadcrumbItems() {
            let breadcrumbs = this.breadcrumbs;
            
            breadcrumbs.unshift({
                text: 'Home',
                href: '#/'
            });
            
            return breadcrumbs;
        }
    }
};

export default WithNavLayout;
</script>
