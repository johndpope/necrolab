<template>
    <div class="h-100 d-flex text-center align-items-center">
        <div class="container text-center">
            <div class="row justify-content-center mb-4">
                <div class="col-12">
                    <a href="#/">
                        <img class="img-fluid" src="/images/banners/banner_background.png" alt="The NecroLab">
                    </a>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-sm-7 col-md-5 col-lg-4 col-xl-4">
                    <h5 class="mb-3 font-weight-normal">Welcome to The NecroLab.</h5>
                    <h5 class="mb-3 font-weight-normal">Please login below.</h5>
                        <div v-if="$route.query['error'] != null" class="alert alert-danger">
                            <div v-if="$route.query.error == 'steam_not_exists'">
                                Your Steam account doesn't exist on this site. If you've recently submitted to the Steam leaderboards you may need to wait a few minutes before logging in.
                            </div>
                        </div>
                    
                    <a href="/login/steam" class="btn btn-lg btn-primary btn-block" role="button"><i class="fab fa-steam-square"></i> Log in with Steam</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'login-page'
};
</script>
