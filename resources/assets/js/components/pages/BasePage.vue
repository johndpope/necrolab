<script>
const BasePage = {
    name: 'base-page',
    data() {
        return {
            loaded: false
        }
    },
    methods: {
        loadState() {}
    },
    created() {
        this.loaded = false;
        
        let promise = this.$store.dispatch('attributes/load');

        promise.then(() => {
            this.loadState(this.$route.params);
        });
    },
    beforeRouteUpdate(to, from, next) {
        this.loaded = false;

        let promise = this.$store.dispatch('attributes/load');
        
        promise.then(() => {
            this.loadState(to.params);
        });
        
        next();
    }
};

export default BasePage;
</script>
