import ModuleGenerator from './ModuleGenerator.js';

const ModesStore = ModuleGenerator.getNew(
    '/api/1/modes',
    'modes'
);

export default ModesStore;
