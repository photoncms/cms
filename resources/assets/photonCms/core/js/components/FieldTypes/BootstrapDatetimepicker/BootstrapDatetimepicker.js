import { eventBus } from '_/helpers/eventBus';

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        disabled: {
            type: Boolean,
        },
        fieldType: {
            type: String,
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
        placeholder: {
            type: String,
        },
        refreshFields: {
            type: Number,
        },
        value: {
            type: [String, Number],
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
            const timestamp = event.date ? event.date.valueOf() : null;

            let value = '';

            if (timestamp) {
                if(this.fieldType === 'DateTime') {
                    value = moment(timestamp).format();
                } else {
                    value = moment(timestamp).format('YYYY-MM-DD');
                }
            }

            return {
                event,
                id: this.id,
                name: this.name,
                value,
            };
        },

        /**
         * Computes the date format using full formatting (including hours, minutes and seconds etc.)
         *
         * @param   {object}  date
         * @return  {string}
         */
        dateWithHoursMinutesSeconds (humanReadable = false) {
            if (this.value) {
                return humanReadable
                    ? moment(this.value).format('DD MMMM YYYY - HH:mm')
                    : moment(this.value).format('YYYY-MM-DD HH-mm-ssZ');
            }

            return null;
        },

        /**
         * Computes the date format using only year, month and day
         *
         * @return  {string}
         */
        dateWithoutHoursMinutesSeconds (humanReadable = false) {
            if (this.value) {
                return humanReadable
                    ? moment(this.value).format('DD MMMM YYYY')
                    : moment(this.value).format('YYYY-MM-DD');
            }

            return null;
        },

        /**
         * Initializes the datetimepicker plug-in
         *
         * @return  {void}
         */
        initializeDatetimepicker () {
            const calendarField = $(this.$el).find(`#${this.id}-container`);

            let dateTimePickerOptions = {
                autoclose: true,
                fontAwesome: true,
                maxView: 'year',
                todayBtn: true,
            };

            if(this.fieldType === 'DateTime' || this.fieldType === 'SystemDateTime') {
                dateTimePickerOptions.format = 'dd MM yyyy - hh:ii';

                dateTimePickerOptions.linkFormat = 'yyyy-mm-ddThh:ii:ssZ';

                dateTimePickerOptions.minView = 'hour';
            } else {
                dateTimePickerOptions.format = 'dd MM yyyy';

                dateTimePickerOptions.linkFormat = 'yyyy-mm-dd';

                dateTimePickerOptions.minView = 'month';
            }

            calendarField.datetimepicker(dateTimePickerOptions);

            calendarField.off('changeDate');

            calendarField.on('changeDate', (event) => {
                this.onChange(event);
            });
        },

        /**
         * Emmits a change event with a payload consting of input value and the event object
         *
         * @param   {event}  event
         * @return  {void}
         */
        onChange (event) {
            this.$emit('change', this.createEventPayload(event));

            eventBus.$emit('fieldTypeChange', this.createEventPayload(event));
        },
    },

    computed: {
        /**
         * Computes the date format according to the field type
         *
         * @return  {string}
         */
        date () {
            if(this.fieldType === 'DateTime') {
                return this.dateWithHoursMinutesSeconds();
            }

            return this.dateWithoutHoursMinutesSeconds();
        },

        /**
         * Computes the human readable date format according to the field type
         *
         * @return  {string}
         */
        humanReadableDate () {
            if(this.fieldType === 'DateTime') {
                return this.dateWithHoursMinutesSeconds(true);
            }

            return this.dateWithoutHoursMinutesSeconds(true);
        },
    },

    mounted () {
        this.$nextTick(() => {
            this.initializeDatetimepicker();
        });
    },

    beforeDestroy () {
        $(this.$el).find(`#${this.id}-container`).datetimepicker('remove');
    },

    watch: {
        'refreshFields'(newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                this.$forceUpdate();

                $(this.$el).find(`#${this.id}-container`).datetimepicker('remove');

                this.initializeDatetimepicker();
            }
        },
    },
};
