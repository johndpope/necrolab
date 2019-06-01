<template>
    <with-nav-body
        :loaded="loaded"
    >
        <div class="jumbotron">
            <h1 class="display-4">Welcome to The NecroLab!</h1>
            <p class="lead">Here you can find leaderboards and rankings for Crypt of the NecroDancer.</p>
            <p class="lead">You can also log in via  Steam, view your player profile, and link your other social media so others can find you more easily.</p>
            <hr class="my-4" />
            <p></p>
            <a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a>
        </div>
    </with-nav-body>
</template>

<script>
import BasePage from './BasePage.vue';
import WithNavBody from '../layouts/WithNavBody.vue';

export default {
    extends: BasePage,
    name: 'home-page',
    components: {
        'with-nav-body': WithNavBody,
    },
    methods: {
        loadState(route_params) {
            this.loaded = true;
        }
    }
};
</script>
