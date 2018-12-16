import ModuleGenerator from './ModuleGenerator.js';

const ReleasesStore = ModuleGenerator.getNew(
    '/api/1/releases',
    'releases'
);

export default ReleasesStore;
