export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        autocomplete: {
            type: Boolean,
        },
        autofocus: {
            type: Boolean,
        },
        disabled: {
            type: Boolean,
        },
        form: {
            type: String,
        },
        id: {
            type: Number,
            required: true,
        },
        maxlength: {
            type: Number,
        },
        name: {
            type: String,
            required: true,
        },
        readonly: {
            type: Boolean,
        },
        refreshFields: {
            type: Number,
        },
        required: {
            type: Boolean,
        },
        tabindex: {
            type: Number,
        },
        value: {
            type: null,
        }
    },

    /**
     * Set the data property
     *
     * @return  {object}
     */
    data() {
        return {
            // Set the initial localValue to the value of 'value' prop
            localValue: this.value,
            // Set the initial field type
            type: 'password',
        };
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        /**
         * Emmits a change event with a payload consting of input value and the event object
         *
         * @param   {event}  event
         * @return  {void}
         */
        onChange: function(event) {
            this.localValue = event.target.value;

            this.$emit('change', {
                value: event.target.value,
                id: event.target.id,
                name: event.target.name,
                event
            });
        },

        /**
         * Reveals the password
         *
         * @return  {void}
         */
        revealPassword: function() {
            this.type = this.type === 'password' ? 'text' : 'password';
        }
    },

    watch: {
        'refreshFields'(newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                this.$forceUpdate();

                this.localValue = this.value;

                this.type = 'password';
            }
        },
    },
};
