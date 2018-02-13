/**
 * Returns field options
 *
 * @param   {object}  VueObject
 * @return  {array}
 */
export const getPermissionNameGeneratorFields = (VueObject) => {
    return [
        /**
         * Roles
         */
        {
            disabled: false,
            id: 'permissionRoles',
            label: 'Select Role',
            name: 'permissionRoles',
            preloadDataConfig: {
                method: 'post',
                payload: {
                    include_relations: false,
                },
                resultsObjectPath: 'body.body.entries',
                valuesOfInterest: {
                    id: 'name',
                    text: 'anchor_text',
                },
                url: false,
            },
            preselectFirst: true,
            relatedTableName: 'roles',
            required: true,
            tooltip: 'Roles are used to dynamically populate the permission name.',
            value: null,
            vueComponent: 'ManyToMany',
        },

        /**
         * Permission Actions
         */
        {
            disabled: false,
            id: 'permissionActions',
            label: 'Select Action',
            name: 'permissionActions',
            optionsData: VueObject.actionsOptions,
            preselectFirst: true,
            refreshFields: moment().valueOf(),
            required: true,
            tooltip: 'Action types are used to dynamically populate the permission name.',
            value: VueObject.selections.action,
            vueComponent: 'ManyToMany',
        },

        /**
         * Modules
         */
        {
            disabled: false,
            id: 'permissionModules',
            label: 'Select Affected Module',
            name: 'permissionModules',
            preloadDataConfig: {
                method: 'get',
                payload: {
                    include_relations: false,
                },
                resultsObjectPath: 'body.body.modules',
                valuesOfInterest: {
                    id: 'table_name',
                    text: 'name',
                },
                url: 'modules',
            },
            preselectFirst: true,
            required: true,
            tooltip: 'Modules are used to dynamically populate the permission name.',
            value: null,
            vueComponent: 'ManyToMany',
        },

        /**
         * Users Fields
         */
        {
            disabled: !VueObject.usersFieldsOptions.length > 0,
            id: 'permissionUsersFields',
            label: 'Select Users Module Field',
            name: 'permissionUsersFields',
            optionsData: VueObject.usersFieldsOptions,
            preselectFirst: true,
            refreshFields: moment().valueOf(),
            required: true,
            tooltip: 'Users fields are used to dynamically populate the permission name.',
            value: VueObject.selections.usersField,
            vueComponent: 'ManyToMany',
        },

        /**
         * Fields
         */
        {
            disabled: !VueObject.fieldsOptions.length > 0,
            id: 'permissionFields',
            label: 'Select Affected Module Field',
            name: 'permissionFields',
            optionsData: VueObject.fieldsOptions,
            preselectFirst: true,
            refreshFields: moment().valueOf(),
            required: true,
            tooltip: 'Fields are used to dynamically populate the permission name.',
            value: VueObject.selections.field,
            vueComponent: 'ManyToMany',
        },

        /**
         * Relation Fields
         */
        {
            disabled: !VueObject.relationFieldsOptions.length > 0,
            id: 'permissionRelationFields',
            label: 'Select Affected Module Relation Field',
            name: 'permissionRelationFields',
            optionsData: VueObject.relationFieldsOptions,
            preselectFirst: true,
            refreshFields: moment().valueOf(),
            required: true,
            tooltip: 'Relation fields are used to dynamically populate the permission name.',
            value: VueObject.selections.relationField,
            vueComponent: 'ManyToMany',
        },

        /**
         * Related Module Fields
         */
        {
            disabled: !VueObject.relatedModuleFieldsOptions.length > 0,
            id: 'permissionRelatedModuleFields',
            label: 'Select Related Module Field',
            name: 'permissionRelatedModuleFields',
            optionsData: VueObject.relatedModuleFieldsOptions,
            preselectFirst: true,
            refreshFields: moment().valueOf(),
            required: true,
            tooltip: 'Related module fields are used to dynamically populate the permission name.',
            value: VueObject.selections.relatedModuleColumnName,
            vueComponent: 'ManyToMany',
        },
    ];
};
