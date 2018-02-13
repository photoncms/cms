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
            default: false,
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
            type: [
                Number,
                String,
            ],
            required: true,
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
            type: String,
            required: true,
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
            type: Number    ,
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
            type: String,
            default: 'text'
        },
        value: {
            type: [String, Number],
        },
        width: {
            type: Number,
        },
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
            $(this.$el).find('button').iconpicker('setIcon', event.target.value);

            this.$emit('change', {
                event,
                id: event.target.id,
                name: event.target.name,
                value: event.target.value,
            });
        },

        /**
         * Initializes the iconpicker plugin
         *
         * @return  {void}
         */
        initializeIconPicker () {
            const options = {
                footer: false,
                iconset: 'fontawesome',
                arrowNextIconClass: 'fa fa-arrow-circle-right',
                arrowPrevIconClass: 'fa fa-arrow-circle-left',
                arrowClass: 'btn-primary-outline',
                selectedClass: 'btn-success',
                unselectedClass: 'btn-primary-outline',
                icon: this.trimmedIconName,
            };

            const $button = $(this.$el).find('button');

            $button.iconpicker(options);

            const self = this;

            $button.off('change');

            $button.on('change', function(event) {
                if (event.icon === 'empty') {
                    return false;
                }

                $(self.$el).find('input').val(`fa ${event.icon}`);

                self.$emit('change', {
                    event,
                    id: self.id,
                    value: `fa ${event.icon}`,
                });
            });
        },

        /**
         * Sets the new icon
         *
         * @return  {void}
         */
        setIcon () {
            const value = this.value ? this.trimmedIconName : 'empty';

            $(this.$el).find('button').iconpicker('setIcon', value);
        },
    },

    /**
     * Sets the computed properties
     *
     * @type  {object}
     */
    computed: {
        /**
         * Trims the first three characters of the class name (presumably 'fa ')
         *
         * @return  {string}
         */
        trimmedIconName () {
            if (!this.value) {
                return 'empty';
            }

            return this.value.slice(3);
        },
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(function() {
            this.initializeIconPicker();
        });
    },

    /**
     * Sets watched properties
     *
     * @type  {Object}
     */
    watch: {
        'value' () {
            this.$nextTick(function() {
                $(this.$el).find('button').iconpicker('reset');

                this.setIcon();
            });
        },

        'refreshFields' () {
            this.$nextTick(function() {
                $(this.$el).find('button').iconpicker('reset');

                this.setIcon();
            });
        },
    },
};
