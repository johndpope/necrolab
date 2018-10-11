<template>
    <span>
        {{ rounded }}
    </span>
</template>

<script>
const RoundedDecimal = {
    name: 'rounded-decimal',
    props: {
        original_number: {
            type: Number
        },
        decimal_places: {
            type: Number,
            default: 2
        },
        format_to_locale: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        rounded() {
            let rounded_number = '';
                
            if(this.original_number != null) {
                rounded_number = Number(Math.round(this.original_number + 'e' + this.decimal_places) + 'e-' + this.decimal_places);
            
                if(this.format_to_locale && typeof Intl == 'object' && typeof Intl.NumberFormat == 'function') {
                    rounded_number = rounded_number.toLocale();
                }
            }
        
            return rounded_number;
        }
    }
};

export default RoundedDecimal;
</script>
