import Vue from 'vue';

import _ from 'lodash';

import {
    mapGetters,
    mapActions,
} from 'vuex';

const PermissionTemplatePicker = Vue.component(
        'PermissionTemplatePicker',
        require('_/components/FieldTypes/Select2/Select2.vue')
    );

const InputTextField = Vue.component(
        'InputTextField',
        require('_/components/FieldTypes/InputTag/InputTag.vue')
    );

import { getPermissionNameGeneratorFields } from './PermissionNameGenerator.fields.js';

import { eventBus } from '_/helpers/eventBus';

import { pError } from '_/helpers/logger';

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
            type: [
                Number,
                String,
            ],
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
             * Used to provide name and title lodash templates based names for actions
             *
             * @type  {Array}
             */
            actionsNameMap: [
                {
                    create: {
                        name: 'create',
                        title: 'Create',
                    }
                },
                {
                    delete: {
                        name: 'delete',
                        title: 'Delete',
                    }
                },
                {
                    retrieve: {
                        name: 'retrieve',
                        title: 'Retrieve',
                    }
                },
                {
                    update: {
                        name: 'update',
                        title: 'Update',
                    }
                },
            ],

            /**
             * Default field values (not returned by the get module fields API call)
             *
             * @type  {Array}
             */
            defaultFieldValues: [
                {
                    id: 'id',
                    text: 'id',
                },
                {
                    id: 'created_by',
                    text: 'Created By',
                },
                {
                    id: 'updated_by',
                    text: 'Updated By',
                },
            ],

            /**
             * Used to feed the data to the fields dropdown
             *
             * @type  {Array}
             */
            fieldsOptions: [],

            /**
             * Holds the fields used to generate permission names
             *
             * @type  {Array}
             */
            permissionNameGeneratorFields: [],

            /**
             * Used to provide name and title lodash templates based on permission template dropdown selection
             *
             * @type  {Object}
             */
            permissionNameMap: {
                'assign_role:[role name]':
                {
                    name: 'assign_role:{{role}}',
                    title: 'Can Assign \'{{role}}\' Role',
                    visibleControls: [
                        'permissionRoles',
                    ],
                },
                'revoke_role:[role name]':
                {
                    name: 'revoke_role:{{role}}',
                    title: 'Can Revoke \'{{role}}\' Role',
                    visibleControls: [
                        'permissionRoles',
                    ],
                },
                'retrieve_role:[role name]':
                {
                    name: 'retrieve_role:{{role}}',
                    title: 'Can Retrieve \'{{role}}\' Role',
                    visibleControls: [
                        'permissionRoles',
                    ],
                },
                'modify_module:[table name]':
                {
                    name: 'modify_module:{{module}}',
                    title: 'Can Modify \'{{module}}\' Module',
                    visibleControls: [
                        'permissionModules',
                    ],
                },
                'retrieve_all_entries:[table name]':
                {
                    name: 'retrieve_all_entries:{{module}}',
                    title: 'Can Retrieve All Entries From \'{{module}}\' Module',
                    visibleControls: [
                        'permissionModules',
                    ],
                },
                '[action]_entry:[table name]':
                {
                    name: '{{action}}_entry:{{module}}',
                    title: 'Can \'{{action}}\' Entry From \'{{module}}\' Module',
                    visibleControls: [
                        'permissionActions',
                        'permissionModules',
                    ],
                },
                '[action]_module:[table name]_match:[users column name]_to:[column name]':
                {
                    name: '{{action}}_module:{{module}}_match:{{usersField}}_to:{{field}}',
                    title: 'Cannot \'{{action}}\' \'{{module}}\' Module, Match \'{{usersField}}\' To \'{{field}}\'',
                    visibleControls: [
                        'permissionActions',
                        'permissionFields',
                        'permissionModules',
                        'permissionUsersFields',
                    ],
                },
                '[action]_module:[table name]_match:[users column name]_in:[relation field]_field:[related module column name]':
                {
                    name: '{{action}}_module:{{module}}_match:{{usersField}}_in:{{relationField}}_field:{{relatedModuleColumnName}}',
                    title: 'Cannot \'{{action}}\' \'{{module}}\' Module, Match \'{{usersField}}\' In \'{{relationField}}\' Relation (\'{{relatedModuleColumnName}}\' Field)',
                    visibleControls: [
                        'permissionActions',
                        'permissionModules',
                        'permissionRelationFields',
                        'permissionRelatedModuleFields',
                        'permissionUsersFields',
                    ],
                },
                'cannot_edit_field:[table name]:[column name]':
                {
                    name: 'cannot_edit_field:{{module}}:{{field}}',
                    title: 'Cannot Edit \'{{module}}\' Module \'{{field}}\' Field',
                    visibleControls: [
                        'permissionFields',
                        'permissionModules',
                    ],
                }
            },

            /**
             * Used to feed data to permission template options dropdown
             *
             * @type  {Array}
             */
            permissionTemplateOptions: [
                {
                    text: 'Role Permissions',
                    children: [
                        {
                            id: 'assign_role:[role name]',
                            text: 'Assign Role [role name]',
                        },
                        {
                            id: 'revoke_role:[role name]',
                            text: 'Revoke Role [role name]',
                        },
                        {
                            id: 'retrieve_role:[role name]',
                            text: 'Retrieve Role [role name]',
                        },
                    ],
                },
                {
                    text: 'Module Permissions',
                    children: [
                        {
                            id: 'modify_module:[table name]',
                            text: 'Modify Module [table name]',
                        },
                        {
                            id: 'retrieve_all_entries:[table name]',
                            text: 'Retrieve All Entries [table name]',
                        },
                        {
                            id: '[action]_entry:[table name]',
                            text: '[action] Entry [table name]',
                        },
                    ],
                },
                {
                    text: 'Module Restricions',
                    children: [
                        {
                            id: '[action]_module:[table name]_match:[users column name]_to:[column name]',
                            text: '[action] Module [table name] Match [users column name] To [column name]',
                        },
                        {
                            id: '[action]_module:[table name]_match:[users column name]_in:[relation field]_field:[related module column name]',
                            text: '[action] Module[table name] Match[users column name] In [relation field][related module column name]',
                        },
                        {
                            id: 'cannot_edit_field:[table name]:[column name]',
                            text: 'Cannot Edit Field [table name][column name]',
                        },
                    ],
                },
            ],

            /**
             * Selected modules non-filtered, non-altared, raw fields data, as returned by the API
             *
             * @type  {Array}
             */
            rawFieldsOptions: [],

            /**
             * Used to feed the data to the related module fields dropdown
             *
             * @type  {Array}
             */
            relatedModuleFieldsOptions: [],

            /**
             * Used to feed the data to the related fields dropdown
             *
             * @type  {Array}
             */
            relationFieldsOptions: [],

            selections: {
                /**
                 * Action name selected in the actions dropdown
                 *
                 * @type  {string}
                 */
                action: null,

                /**
                 * Field name selected in the fields dropdown
                 *
                 * @type  {string}
                 */
                field: null,

                /**
                 * Module name selected in the modules dropdown
                 *
                 * @type  {string}
                 */
                module: null,

                /**
                 * Related module name selected in the related modules dropdown
                 *
                 * @type  {string}
                 */
                relatedModule: null,

                /**
                 * Related module column name selected in the related module column name dropdown
                 *
                 * @type  {string}
                 */
                relatedModuleColumnName: null,

                /**
                 * Related field name selected in the related fields dropdown
                 *
                 * @type  {string}
                 */
                relationField: null,

                /**
                 * Template name selected in permission template dropdown
                 *
                 * @type  {[type]}
                 */
                template: 'assign_role:[role name]',

                /**
                 * Users field name selected in the related fields dropdown
                 *
                 * @type  {string}
                 */
                usersField: null,
            },

            /**
             * Used to feed the data to the users fields dropdown
             *
             * @type  {Array}
             */
            usersFieldsOptions: [],
        };
    },

    /**
     * Define components
     *
     * @type  {Object}
     */
    components: {
        InputTextField,
        PermissionTemplatePicker,
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
         * Used to feed the data to the actions dropdown
         *
         * @type  {Array}
         */
        actionsOptions () {
            let options = [
                {
                    id: 'create',
                    text: 'Create',
                },
                {
                    id: 'update',
                    text: 'Update',
                },
                {
                    id: 'delete',
                    text: 'Delete',
                },
            ];

            if (this.selections.template !== '[action]_entry:[table name]') {
                options.splice(1, 0,
                    {
                        id: 'retrieve',
                        text: 'Retrieve',
                    });
            }

            return options;
        },
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('photonModule', [
            'getPhotonModuleInformation',
        ]),

        /**
         * Bind evenBus listeners
         *
         * @return  {void}
         */
        initEventBusListener () {
            eventBus.$on('fieldTypeChange', payload => {
                switch(payload.name) {
                case 'permissionActions':
                    Vue.set(this.selections, 'action', payload.value);

                    break;
                case 'permissionRoles':
                    Vue.set(this.selections, 'role', payload.value);

                    break;
                case 'permissionModules':
                    Vue.set(this.selections, 'module', payload.value);

                    break;
                case 'permissionFields':
                    Vue.set(this.selections, 'field', payload.value);

                    break;
                case 'permissionRelationFields':
                    Vue.set(this.selections, 'relationField', payload.value);

                    break;
                case 'permissionRelatedModuleFields':
                    Vue.set(this.selections, 'relatedModuleColumnName', payload.value);

                    break;
                case 'permissionTemplatePicker':
                    Vue.set(this.selections, 'template', payload.value);

                    break;
                case 'permissionUsersFields':
                    Vue.set(this.selections, 'usersField', payload.value);

                    break;
                }
            });
        },

        /**
         * Handles visibility of name builder dropdowns
         *
         * @param   {string}  id
         * @return  {Boolean}
         */
        isComponentVisible(id) {
            return this.permissionNameMap[this.selections.template].visibleControls.includes(id);
        },

        /**
         * Creates the slug version of the string template
         *
         * @param   {object}  templateVariables
         * @return  {string}
         */
        slugify (slugTemplate, templateVariables) {
            _.templateSettings.interpolate = /{{([\s\S]+?)}}/g;

            slugTemplate = slugTemplate.replace(/{{/g, '{{data.');

            let compiled = _.template(slugTemplate);

            /**
             * Instead of directly passing the templateVariables object, we're wrapping it in a data object to
             * avoid the issue of lodash _.template complaining about the template variable not being set
             */
            let slugCandidate = compiled({ data: { ...templateVariables }});

            return slugCandidate;
        },
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(function () {
            this.initEventBusListener();

            this.getPhotonModuleInformation({
                moduleTableName: 'users',
                refreshModule: true,
            })
                .then((response) => {
                    let options = response.body.body.module.fields;

                    options = options.map(option => {
                        return {
                            id: option.relation_name ? option.relation_name : option.column_name,
                            text: option.relation_name ? `${option.name} (relation field)` : option.name,
                        };
                    });

                    Vue.set(this, 'usersFieldsOptions', this.defaultFieldValues.concat(options));

                    if (!_.isEmpty(options)) {
                        Vue.set(this.selections, 'usersField', this.usersFieldsOptions[0]['id']);
                    }

                    this.permissionNameGeneratorFields = getPermissionNameGeneratorFields(this);
                })
                .catch((response) => {
                    pError('Failed to load module fields.', response);
                });
        });
    },

    /**
     * Set the beforeDestroy hook
     *
     * @return  {void}
     */
    beforeDestroy() {
        eventBus.$off('fieldTypeChange');
    },

    /**
     * Define watched properties
     *
     * @type  {Object}
     */
    watch: {
        'actionsOptions' () {
            this.permissionNameGeneratorFields = getPermissionNameGeneratorFields(this);
        },

        'selections.module' (newValue) {
            this.getPhotonModuleInformation({
                moduleTableName: newValue,
                refreshModule: true,
            })
                .then((response) => {
                    let options = response.body.body.module.fields;

                    const fieldOptions = options.map(option => {
                        return {
                            id: option.relation_name ? option.relation_name : option.column_name,
                            text: option.relation_name ? `${option.name} (relation field)` : option.name,
                        };
                    });

                    const filteredOptions = options.filter(option => option.relation_name);

                    const relationFieldsOptions = filteredOptions.map(option => {
                        return {
                            id: option.relation_name ? option.relation_name : option.column_name,
                            text: option.relation_name ? `${option.name} (relation field)` : option.name,
                        };
                    });

                    Vue.set(this, 'rawFieldsOptions', options);

                    Vue.set(this, 'fieldsOptions', this.defaultFieldValues.concat(fieldOptions));

                    Vue.set(this, 'relationFieldsOptions', relationFieldsOptions);

                    if (!_.isEmpty(fieldOptions)) {
                        Vue.set(this.selections, 'field', this.fieldsOptions[0]['id']);
                    }

                    if (!_.isEmpty(relationFieldsOptions)) {
                        Vue.set(this.selections, 'relationField', this.relationFieldsOptions[0]['id']);
                    }

                    this.permissionNameGeneratorFields = getPermissionNameGeneratorFields(this);
                })
                .catch((response) => {
                    pError('Failed to load module fields.', response);
                });
        },
        'selections.relationField' (newValue) {
            const fieldOfInterest = _.find(this.rawFieldsOptions, { relation_name: newValue });

            const moduleTableName = this.photonModule.moduleIdToTableNameMap[fieldOfInterest.related_module];

            this.getPhotonModuleInformation({
                refreshModule: true,
                moduleTableName,
            })
                .then((response) => {
                    let options = response.body.body.module.fields;

                    const fieldOptions = options.map(option => {
                        return {
                            id: option.relation_name ? option.relation_name : option.column_name,
                            text: option.relation_name ? `${option.name} (relation field)` : option.name,
                        };
                    });

                    Vue.set(this, 'relatedModuleFieldsOptions', this.defaultFieldValues.concat(fieldOptions));

                    setTimeout(() => {
                        if (!_.isEmpty(fieldOptions)) {
                            Vue.set(this.selections, 'relatedModuleColumnName', this.relatedModuleFieldsOptions[0]['id']);
                        }

                        this.permissionNameGeneratorFields = getPermissionNameGeneratorFields(this);
                    }, 50);

                })
                .catch((response) => {
                    pError('Failed to load module fields.', response);
                });
        },

        'selections': {
            deep: true,
            handler () {
                let generatedValues = {
                    permissionName: this.slugify(this.permissionNameMap[this.selections.template].name, this.selections),

                    permissionTitle: this.slugify(this.permissionNameMap[this.selections.template].title, this.selections),
                };

                this.$emit('change', generatedValues);

                $(this.$el).find('[data-toggle="tooltip"]').tooltip('destroy');

                $(this.$el).find('[data-toggle="tooltip"]').tooltip({
                    'container': 'body'
                });
            },
        },
    }
};
