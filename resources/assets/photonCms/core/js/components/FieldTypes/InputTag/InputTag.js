import { pWarn } from '_/helpers/logger';

import { eventBus } from '_/helpers/eventBus';

import { mapGetters } from 'vuex';

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        accept: {
            type: String,
        },
        accesskey: {
            type: String,
        },
        align: {
            type: String,
        },
        alt: {
            type: String,
        },
        autocomplete: {
            type: Boolean,
        },
        autofocus: {
            type: Boolean,
        },
        checked: {
            type: Boolean,
        },
        dirname: {
            type: String,
        },
        disabled: {
            type: Boolean,
        },
        form: {
            type: String,
        },
        formaction: {
            type: String,
        },
        formenctype: {
            type: String,
        },
        formmethod: {
            type: String,
        },
        formnovalidate: {
            type: Boolean,
        },
        formtarget: {
            type: String,
        },
        id: {
            required: true,
            type: [
                Number,
                String,
            ],
        },
        height: {
            type: String,
        },
        list: {
            type: String,
        },
        max: {
            type: Number,
        },
        maxlength: {
            type: Number,
        },
        min: {
            type: Number,
        },
        multiple: {
            type: Boolean,
        },
        name: {
            required: true,
            type: String,
        },
        pattern: {
            type: String,
        },
        placeholder: {
            type: String,
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
        size: {
            type: Number,
        },
        src: {
            type: String,
        },
        step: {
            type: Number,
        },
        tabindex: {
            type: Number,
        },
        type: {
            default: 'text',
            type: String,
        },
        value: {
            type: [
                Boolean,
                Number,
                String,
            ],
        },
        width: {
            type: Number,
        },
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            ui: 'ui/ui',
        }),

        spellcheck () {
            return this.ui.photonConfig.spellcheck;
        },
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        /**
         * Creates the event payload
         *
         * @param   {object}  event
         * @return  {object}
         */
        createEventPayload (event) {
            return {
                event,
                id: event.target.id,
                name: event.target.name,
                value: event.target.value,
            };
        },

        /**
         * Emmits a change event with a payload consting of input value and the event object
         *
         * @param   {event}  event
         * @return  {void}
         */
        onChange: function(event) {
            this.$emit('change', this.createEventPayload(event));

            eventBus.$emit('fieldTypeChange', this.createEventPayload(event));
        },

        /**
         * Emmits a global change event with a payload consting of input value and the event object
         *
         * @param   {event}  event
         * @return  {void}
         */
        onBlur: function(event) {
            eventBus.$emit('fieldTypeBlur', this.createEventPayload(event));
        },
    },

    watch: {
        'refreshFields' (newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                pWarn(
                    'Watched property fired.',
                    `InputTag.${this.name}`,
                    'refreshFields',
                    newEntry,
                    oldEntry,
                    this
                );

                this.$forceUpdate();

                $(this.$el).val(this.value);
            }
        },
    },
};
