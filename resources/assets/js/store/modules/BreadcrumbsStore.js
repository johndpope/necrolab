const BreadcrumbsStore = {
    namespaced: true,
    state: {
        breadcrumbs: [],
    },
    mutations: {
        setAll(state, breadcrumbs) {
            state.breadcrumbs = breadcrumbs;
        },
        clear(state) {
            state.breadcrumbs = [];
        }
    },
    getters: {
        getAll: state => {
            return state.breadcrumbs;
        }
    }
};

export default BreadcrumbsStore;
