import ModuleGenerator from './ModuleGenerator.js';

const CharactersStore = ModuleGenerator.getNew(
    '/api/1/characters',
    'characters'
);

export default CharactersStore;
