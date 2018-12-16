import ModuleGenerator from './ModuleGenerator.js';

const LeaderboardTypesStore = ModuleGenerator.getNew(
    '/api/1/leaderboards/types',
    'leaderboard_types'
);

export default LeaderboardTypesStore;
