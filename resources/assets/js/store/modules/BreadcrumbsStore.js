const BreadcrumbsStore = {
    namespaced: true,
    state: {
        breadcrumbs: [],
    },
    mutations: {
        setAll(state, breadcrumbs) {
            state.breadcrumbs = breadcrumbs;
        }
    },
    getters: {
        getAll: state => {
            return state.breadcrumbs;
        }
    }
};

export default BreadcrumbsStore;
