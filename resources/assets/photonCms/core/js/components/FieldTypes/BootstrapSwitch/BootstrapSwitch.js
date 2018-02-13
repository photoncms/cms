import { pWarn } from '_/helpers/logger';

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        defaultValue: {
            default: true,
            type: Boolean,
        },
        disabled: {
            type: Boolean,
        },
        id: {
            required: true,
            type: [
                Number,
                String,
            ],
        },
        name: {
            required: true,
            type: String,
        },
        refreshFields: {
            type: Number,
        },
        type: {
            default: 'checkbox',
            type: String,
        },
        value: {
            type: Boolean,
            default: null
        },
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        /**
         * Initializes the bootstrapSwitch plug-in
         *
         * @return  {void}
         */
        initializeBootstrapSwitch () {
            const $booleanSwitch = $(this.$el).find('input');

            const state = this.value === null ? this.defaultValue : this.value;

            $booleanSwitch.bootstrapSwitch({
                state
            });

            $booleanSwitch.off('switchChange.bootstrapSwitch');

            $booleanSwitch.on('switchChange.bootstrapSwitch', function(event, state) {
                this.$emit('change', {
                    event,
                    id: this.id,
                    name: this.name,
                    value: state,
                });
            }.bind(this));
        },
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(() => {
            this.initializeBootstrapSwitch();
        });
    },

    /**
     * Set the beforeDestroy hook
     *
     * @return  {void}
     */
    beforeDestroy () {
        $(this.$el).find('input').bootstrapSwitch('destroy');
    },

    /**
     * Set the watched properties
     *
     * @type  {Object}
     */
    watch: {
        'refreshFields' (newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                pWarn(
                    'Watched property fired.',
                    `BootstrapSwitch.${this.name}`,
                    'refreshFields',
                    newEntry,
                    oldEntry,
                    this
                );

                this.$nextTick(() => {
                    this.$forceUpdate();

                    $(this.$el).find('input').bootstrapSwitch('destroy');

                    this.initializeBootstrapSwitch();
                });
            }
        },
    },
};
