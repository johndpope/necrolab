import ModuleGenerator from './ModuleGenerator.js';

const SeededTypesStore = ModuleGenerator.getNew(
    '/api/1/seeded_types',
    'seeded_types'
);

export default SeededTypesStore;
