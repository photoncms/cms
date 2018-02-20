import Vue from 'vue';

import _ from 'lodash';

import { getFields } from '_/components/Dashboard/Widgets/LatestEntriesWidget/LatestEntriesWidget.fields.js';

import {
    mapGetters,
    mapActions,
} from 'vuex';

import { userHasRole } from '_/vuex/actions/userActions';

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        widget: {
            required: true,
            type: Object,
        },
    },

    /**
     * Set the component data
     *
     * @return  {object}
     */
    data () {
        return {
            /**
             * Stores widget setup fields
             *
             * @type  {Array}
             */
            fields: [],

            /**
             * Is polling active?
             *
             * @type  {boolean}
             */
            polling: false,

            /**
             * Toggles the widget setup mode
             *
             * @type  {boolean}
             */
            setupMode: _.isEmpty(this.widget.module),

            /**
             * Source module data
             *
             * @type  {Object}
             */
            sourceModule: {
                /**
                 * Souce module fields
                 *
                 * @type  {Array}
                 */
                fields: [],

                /**
                 * Source module entries
                 *
                 * @type  {Array}
                 */
                items: [],
            },

            /**
             * An object sent to child components; used to signal that the fields need to be reset, with the ability
             * to limit the reset to only certain fields
             *
             * @type  {Object}
             */
            formFieldsReset: {
                /**
                 * A list of field names to include in a reset action
                 *
                 * @type  {Array}
                 */
                includeFields: [],

                /**
                 * A parameter that is listened by the child components. Changed via moment().valueOf() method to always
                 * set the fresh value.
                 *
                 * @type  {integer}
                 */
                resetData: null,
            },
        };
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            photonModule: 'photonModule/photonModule',
        }),

        /**
         * Sets the widget theme CSS class
         *
         * @return  {string}
         */
        getWidgetTheme () {
            return this.widget.theme ? this.widget.theme : 'panel-primary';
        },

        /**
         * prepares the module dropdown data
         *
         * @return  {array}
         */
        moduleOptions () {
            return this.photonModule.modules.map(module => {
                return ({
                    id: module.table_name,
                    text: module.name,
                });
            });
        },

        /**
         * Preapares the Image Field dropdown data
         *
         * @return  {array}
         */
        imageFields () {
            let output = [];

            this.sourceModule.fields
                .map(field => {
                    if (field.type == 15) {
                        output.push({
                            id: field.relation_name,
                            text: field.name,
                        });
                    }
                });

            return output;
        },

        /**
         * Prepares the widget list items
         *
         * @return  {array}
         */
        items () {
            let items = [];

            const $list = $(this.$el).find('.front .list-group');

            const length = 12;

            if (this.sourceModule.items) {
                let offsetCount = null;

                let offset = null;

                for (var index = 0; index < length; index++) {
                    items[index] = this.sourceModule.items[index] || {
                        id: 'blank.' + index
                    };

                    if (items[index] && items[index].created_at) {
                        items[index].timeAgo = moment(items[index].created_at).fromNow();
                    }

                    offset = -40;

                    if (this.lastAddedId === items[index].id) {
                        offsetCount = index;
                    }
                }

                if (offsetCount === null && items[0].id > this.lastAddedId) {
                    offsetCount = 5;
                } else if (offsetCount === null) {
                    offsetCount = 0;
                }

                if (!this.setupMode) {
                    $list.css('top', offsetCount * offset);

                    setTimeout(() => {
                        $list.addClass('scrolling');
                        $list.css('top', 0);
                    });

                    this.animationTimeout = setTimeout(() => {
                        $list.find('li:first-child').addClass('animated flash');
                        $list.removeClass('scrolling');
                    }, 200);
                }

                this.lastAddedId = items[0].id;
            }

            return items;
        },

        /**
         * Unpacks the meta_data JSON to object
         *
         * @return  {object}
         */
        metaData () {
            return JSON.parse(this.widget.meta_data);
        }
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('widget', [
            'deleteEntry',
            'getLatestEntries',
            'getSelectedModuleFields',
            'submitEntry',
            'updateWidget',
        ]),

        addThumbSuffix(imageName) {
            let imageNameArray = imageName.split('.');

            imageNameArray[imageNameArray.length-2] += '_120x90';

            return imageNameArray.join('.');
        },

        /**
         * Toggles the setup mode
         *
         * @return  {void}
         */
        toggleSetup () {
            if (this.polling) {
                clearTimeout(this.polling);
            }

            if (this.animationTimeout) {
                clearTimeout(this.animationTimeout);
            }

            if (this.setupMode) {
                let payload = {
                    ...this.widget,
                };

                if (!_.has(this.widget, 'image_field')) {
                    payload['image_field'] = this.metaData.image_field;
                }

                this.submitEntry({ payload })
                    .then((response) => {
                        this.updateWidget({ widget: response.entry })
                            .then(() => {
                                this.setupMode = false;

                                setTimeout(() => {
                                    this.getModuleData(true);
                                }, 300);
                            });
                    });
            } else {
                $(this.$el).find('.animated.flash').removeClass('animated flash');

                this.setupMode = true;

                setTimeout(() => {
                    $(this.$el).find('.scrolling').removeClass('scrolling');
                }, 200);
            }
        },

        /**
         * Removes the widget
         *
         * @return  {void}
         */
        removeWidget () {
            this.deleteEntry({ entryId: this.widget.id })
                .then(() => {
                    if (this.polling) {
                        clearTimeout(this.polling);
                    }
                });
        },

        /**
         * Gets the latest module entries
         *
         * @param   {boolean}  longpoll
         * @return  {void}
         */
        getModuleData (longpoll) {
            if (!this.widget.module) {
                this.sourceModule = {};

                return;
            }

            this.getLatestEntries({
                itemsPerPage: 5,
                moduleTableName: this.widget.module,
            })
                .then((items) => {
                    Vue.set(this.sourceModule, 'items', items);

                    if (this.polling) {
                        clearTimeout(this.polling);
                    }

                    const refreshInterval = parseInt(this.widget.refresh_interval, 10);

                    if (refreshInterval && longpoll) {
                        this.polling = setTimeout(() => {
                            this.getModuleData(longpoll);
                        }, refreshInterval);
                    }
                });
        },

        /**
         * Checks if a user has given role
         *
         * @param   {string}  role
         * @return  {bool}
         */
        userHasRole,
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(() => {
            this.getSelectedModuleFields({ moduleTableName: this.widget.module })
                .then((fields) => {
                    this.sourceModule.fields = fields;

                    this.fields = getFields(this);

                    const longpoll = !this.widget.setupMode;

                    this.getModuleData(longpoll);
                });
        });
    },

    /**
     * Define watched properties
     *
     * @type  {Object}
     */
    watch: {
        'widget': {
            deep: true,
            handler (newEntry) {
                this.getSelectedModuleFields({ moduleTableName: newEntry.module })
                    .then((fields) => {
                        Vue.set(this.sourceModule, 'fields', fields);

                        this.fields = getFields(this);

                        this.formFieldsReset.resetData = moment().valueOf();
                    });
            },
        },
    },

    /**
     * Set the beforeDestroy hook
     *
     * @return  {void}
     */
    beforeDestroy () {
        if (this.polling) {
            clearTimeout(this.polling);
        }

        if (this.animationTimeout) {
            clearTimeout(this.animationTimeout);
        }
    }
};
