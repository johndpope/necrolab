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
                    <router-view></router-view>
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
