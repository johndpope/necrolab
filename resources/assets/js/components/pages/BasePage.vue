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
    
        this.loadState(this.$route.params);
    },
    beforeRouteUpdate(to, from, next) {
        this.loaded = false;
    
        this.loadState(to.params);
        
        next();
    }
};

export default BasePage;
</script>
