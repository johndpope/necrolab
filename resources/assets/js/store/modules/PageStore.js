const PageStore = {
    namespaced: true,
    state: {
        filters: []
    },
    mutations: {
        setFilter(state, filter) {
            if(state.filters.indexOf(filter) != -1) {
                state.filters.push(filter);
            }
        }
    },
    getters: {
        getFilters: (state) => {
            return state.filters;
        }
    },
    actions: {}
};

export default PageStore;
