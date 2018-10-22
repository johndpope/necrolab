<template>
    <div>
        <span v-if="long_name">
            {{ longName }}
        </span>
        <span v-else>
            {{ shortName }}
        </span>
    </div>
</template>

<script>
const LeaderboardName = {
    name: 'leaderboard-name',
    props: {
        record: {
            type: Object,
            default: () => {}
        },
        long_name: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        longName() {
            let name = Formatting.getCharacterDisplay(leaderboard_record.character);
    
            if(leaderboard_record['release'] != null) {
                name += ' ' + leaderboard_record.release.display_name;
            }
            
            if(leaderboard_record['mode'] != null) {
                name += ' ' + leaderboard_record.mode.display_name + ' Mode ';
            }
            
            name += ' ';
            
            switch(this.record.type) {
                case 'score':
                    name += 'Score';
                    break;
                case 'speed':
                    name += 'Speedrun';
                    break;
                case 'deathless':
                    name += 'Deathless';
                    break;
            }
            
            name += ' ' + this.shortName();
            
            return name;
        },
        shortName() {
            let name = '';
    
            if(this.record.co_op == 0) {
                if(this.record.seeded == 0) {
                    if(this.record.custom == 0) {
                        name += 'All Zones';
                    }
                    else {
                        name += 'Custom Music';
                    }
                }
                else {
                    if(this.record.custom == 0) {
                        name += 'Seeded';
                    }
                    else {
                        name += 'Seeded Custom Music';
                    }
                }
            }
            else {
                if(this.record.seeded == 0) {
                    if(this.record.custom == 0) {
                        name += 'Co-Op';
                    }
                    else {
                        name += 'Co-Op Custom Music';
                    }
                }
                else {
                    if(this.record.custom == 0) {
                        name += 'Seeded Co-Op';
                    }
                    else {
                        name += 'Seeded Co-Op Custom Music';
                    }
                }
            }
            
            return name;
        }
    }
};

export default LeaderboardName;
</script>
