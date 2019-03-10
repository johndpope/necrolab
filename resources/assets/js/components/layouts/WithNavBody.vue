<template>
    <div>
        <div v-if="loaded" class="container-fluid">
            <div v-if="show_breadcrumbs" class="row">
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
        <div v-else>
            <h2>
                Loading...
            </h2>
        </div>
    </div>
</template>

<script>
import bBreadcrumb from 'bootstrap-vue/es/components/breadcrumb/breadcrumb';

const WithNavBody = {
    name: 'with-nav-body',
    components: {
        'b-breadcrumb': bBreadcrumb
    },
    props: {
        loaded: {
            type: Boolean,
            default: false
        },
        show_breadcrumbs: {
            type: Boolean,
            default: true,
        },
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
        }
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
    }
};

export default WithNavBody;
</script>
