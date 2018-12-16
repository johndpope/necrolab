const PageStore = {
    namespaced: true,
    state: {
    },
    mutations: {
    },
    getters: {
    },
    actions: {
        loadModules(context, store_modules) {
            return new Promise((resolve, reject) => {
                let promises = [];
                
                let store_modules_length = store_modules.length;
                
                for(let index = 0; index < store_modules_length; index++) {
                    let store_module = store_modules[index];
                    
                    promises.push(context.dispatch(store_module + '/loadAll', null, {root: true}));

                    promises.push(context.dispatch(store_module + '/loadDependencies', null, {root: true}));
                }
                
                Promise.all(promises).then(function() {
                    resolve();
                });
            });
        }
    }
};

export default PageStore;
