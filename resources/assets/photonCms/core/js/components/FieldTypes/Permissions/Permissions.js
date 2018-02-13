import _ from 'lodash';

import Vue from 'vue';

import { api } from '_/services/api';

import { pError } from '_/helpers/logger';

import {
    bindValidatorToForm,
    destroyValidator,
    processErrors,
    resetValidator
} from '_/services/formValidator';

export const PermissionNameGenerator = Vue.component(
        'PermissionNameGenerator',
        require('_/components/UserInterface/PermissionNameGenerator/PermissionNameGenerator.vue')
    );

export const BooleanField = Vue.component(
        'BooleanField',
        require('_/components/FieldTypes/BootstrapSwitch/BootstrapSwitch.vue')
    );

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
        value: {
            type: Array,
            default: null
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
             * A list of available permissions
             *
             * @type  {Array}
             */
            availablePermissions: [],

            /**
             * Contains API errors if permission creation fails
             *
             * @type  {string}
             */
            error: {
                fields: null,
                message: null,
            },

            /**
             * Permission name
             *
             * @type  {string}
             */
            permissionName: null,

            /**
             * Permission title
             *
             * @type  {string}
             */
            permissionTitle: null,

            /**
             * Selected permissions
             *
             * @type  {array}
             */
            selectedPermissions: this.value ? this.value.map(item => item.id) : [],

            /**
             * Contains a general API error
             *
             * @type  {string}
             */
            serverError: null,

            /**
             * Stores the visibility state of permission generator
             *
             * @type  {boolean}
             */
            showPermissionGenerator: false,

            /**
             * The validator object
             *
             * @type  {object}
             */
            validator: {},
        };
    },

    /**
     * Define components
     *
     * @type  {Object}
     */
    components: {
        PermissionNameGenerator,
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        permissionsAvailable() {
            return (!_.isEmpty(this.availablePermissions));
        }
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        /**
         * Clears the errors from the UI
         *
         * @return  {void}
         */
        clearErrors () {
            this.serverError = null;
        },

        /**
         * Creates a new permission entry
         *
         * @return  {Promise}
         */
        createPermission () {
            const payload = {
                name: this.permissionName,
                title: this.permissionTitle,
            };

            return api.post('permissions', payload)
                .then(() => {
                    this.getAllPermissions()
                        .then(() => {
                            $(this.$el).find('input[type="checkbox"]').uniform();

                            this.setPermissionGeneratorVisibility({ visible: false });
                        });
                })
                .catch((response) => {
                    resetValidator(this.validator);

                    this.error.fields = response.body.body.error_fields;

                    this.error.message = response.body.message;

                    this.serverError = processErrors(this.validator, this.error);

                    pError('Failed to create new permission.', response);
                });
        },

        /**
         * Define a method that runs on change event
         *
         * @param   {integer}  options.id
         * @param   {string}  options.value
         * @return  {void}
         */
        onChange ({ permissionName, permissionTitle }) {
            this.permissionName = permissionName;

            this.permissionTitle = permissionTitle;
        },

        /**
         * Gets all available perissions from the API
         *
         * @return  {Promise}
         */
        getAllPermissions () {
            const payload = {
                sorting: {
                    title: 'asc',
                }
            };

            return api.post('filter/permissions', payload)
                .then((response) => {
                    const entries = response.body.body.entries;

                    this.availablePermissions = entries.map(entry => {
                        return {
                            id: entry.id,
                            name: entry.name,
                            title: entry.title,
                        };
                    });
                })
                .catch((response) => {
                    pError('Failed to load available permissions.', response);
                });
        },

        /**
         * Sets the permissions generator visibility
         *
         * @param  {boolean}  options.value
         */
        setPermissionGeneratorVisibility ({ visible }) {
            this.showPermissionGenerator = visible;
        },

        updatePermissionFieldValue ({ name, value }) {
            if (name === 'name') {
                this.permissionName = value;
            }

            if (name === 'title') {
                this.permissionTitle = value;
            }
        },

        /**
         * Define a method that runs on PermissionNameGenerator change event
         *
         * @param   {integer}  options.id
         * @param   {string}  options.value
         * @return  {void}
         */
        updateGeneratedPermissionData ({ permissionName, permissionTitle }) {
            this.permissionName = permissionName;

            this.permissionTitle = permissionTitle;
        },
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(() => {
            this.getAllPermissions()
                .then(() => {
                    $(this.$el).find('input[type="checkbox"]').uniform();
                });
        });
    },

    /**
     * Call a beforeDestroy hook
     *
     * @type  {function}
     * @return  void
     */
    beforeDestroy: function() {
        this.clearErrors();

        destroyValidator(this.validator);
    },

    /**
     * Define watched properties
     *
     * @type  {Object}
     */
    watch: {
        'showPermissionGenerator' () {
            if (this.showPermissionGenerator) {
                this.validator = bindValidatorToForm({
                    selector: '#permissions-generator-form',

                    onSubmit: () => {
                        this.clearErrors();

                        this.createPermission();
                    }
                }, this);
            } else {
                this.clearErrors();

                destroyValidator(this.validator);
            }
        },

        'selectedPermissions': {
            deep: true,
            handler (value, oldValue) {
                if (_.isEqual(value, oldValue)) {
                    return;
                }

                const payload = {
                    id: this.id,
                    name: this.name,
                    value,
                };

                this.$emit('change', payload);
            },
        },

        'refreshFields' (newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                this.$forceUpdate();

                const selectedPermissions = this.value ? this.value.map(item => item.id) : [];

                Vue.set(this, 'selectedPermissions', selectedPermissions);

                this.getAllPermissions()
                    .then(() => {
                        $(this.$el).find('input[type="checkbox"]').uniform();
                    });
            }
        },
    },
};
