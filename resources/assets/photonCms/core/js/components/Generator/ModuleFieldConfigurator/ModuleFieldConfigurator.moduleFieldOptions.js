// Gets list of possible field types and field types list formatted for select2 plugin
import {
    mapFromId,
    getFieldTypeOptionsSelect2
} from '_/services/fieldTypes';

/**
 * List of field types that feature relations
 *
 * @type  {Array}
 */
const fieldTypesWithRelations = [
    7, // manyToOne
    8, // manyToMany
    14, // oneToOne
    15, // file
    16, // files
    17, // oneToMany
    19, // gallery
    20, // manyToOneExtended
    21, // manyToManyExtended
    22, // oneToOneExtended
    23, // oneToManyExtended
    24 // permissions
];

/**
 * Formats the related modules for use with select2 plugin
 *
 * @param   {rray}  modules
 * @return  {object}
 */
const _mapRelatedModulesSelect2 = (modules) => {
    // Set initial select2 value
    return [{
        id: 0,
        text: 'No Relation'
    }]
        .concat(modules.map((module) => {
            // Format that select2 expects for it's option
            return {
                id: module.id,
                text: module.name
            };
        }));
};

/**
 * Gets module field options
 *
 * @param   {object}  ModuleFieldsConfigurator
 * @return  {object}
 */
export const getModuleFieldOptions = (ModuleFieldsConfigurator) => {
    /**
     * Field configuration (props passed to form components)
     * Type of component is visible in the type property (see comments in components
     * to see what each of these properties is for)
     *
     * type {array}
     */
    const fieldOptions = [
        /**
         * Module form field type field
         */
        {
            disabled: !ModuleFieldsConfigurator.moduleField.newField,
            hidden: false,
            id: `${ModuleFieldsConfigurator.moduleField.id}|type`,
            label: 'Field Type',
            lazyLoading: false,
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_TYPE',
            name: `fields[${ModuleFieldsConfigurator.moduleField.order}][type]`,
            optionsData: getFieldTypeOptionsSelect2(),
            optionGroup: 0,
            preselectFirst: true,
            tooltip: 'ID of the field type.',
            value: ModuleFieldsConfigurator.moduleField.type,
            vueComponent: 'ManyToMany',
        },

        /**
         * Field name field
         */
        {
            id: `${ModuleFieldsConfigurator.moduleField.id}|name`,
            label: 'Field Name',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_NAME',
            name: `fields[${ModuleFieldsConfigurator.moduleField.order}][name]`,
            optionGroup: 0,
            placeholder: 'My Field Name',
            tooltip: 'Human readable field name.',
            value: ModuleFieldsConfigurator.moduleField.name,
            vueComponent: 'InputText',
        },

        /**
         * Tooltip text field
         */
        {
            id: ModuleFieldsConfigurator.moduleField.id + '|tooltip_text',
            label: 'Tooltip Text',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_TOOLTIP_TEXT',
            name: `fields[${ModuleFieldsConfigurator.moduleField.order}][tooltip_text]`,
            optionGroup: 1,
            placeholder: 'Tooltip Text',
            tooltip: 'Text which should be used in front end to implement a popup over a field with field explanation.',
            value: ModuleFieldsConfigurator.moduleField.tooltip_text,
            vueComponent: 'InputText',
        },

        /**
         * Validation rules field
         */
        {
            id: `${ModuleFieldsConfigurator.moduleField.id}|validation_rules`,
            label: 'Validation Rules',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_VALIDATION_RULES',
            name: `fields[${ModuleFieldsConfigurator.moduleField.order}][validation_rules]`,
            optionGroup: 1,
            placeholder: 'required|max:255',
            tooltip: 'Validation rules written using Laravel validation notation.',
            value: ModuleFieldsConfigurator.moduleField.validation_rules,
            vueComponent: 'InputText',
        },

        /**
         * Default field
         */
        {
            id: `${ModuleFieldsConfigurator.moduleField.id}|default`,
            label: 'Default Value',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_DEFAULT',
            name: `fields[${ModuleFieldsConfigurator.moduleField.order}][default]`,
            optionGroup: 1,
            placeholder: 'Default Value',
            tooltip: 'Sets the default parameter in the DB.',
            value: ModuleFieldsConfigurator.moduleField.default,
            vueComponent: 'InputText',
        },

        /**
         * Editable field
         */
        {
            defaultValue: true,
            id: `${ModuleFieldsConfigurator.moduleField.id}|editable`,
            label: 'Editable',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_EDITABLE',
            name: `fields[${ModuleFieldsConfigurator.moduleField.order}][editable]`,
            optionGroup: 1,
            tooltip: 'Use this to determine if the field in the form should be editable.',
            value: ModuleFieldsConfigurator.moduleField.editable,
            vueComponent: 'Boolean',
        },

        /**
         * Disabled field
         */
        {
            defaultValue: false,
            id: `${ModuleFieldsConfigurator.moduleField.id}|disabled`,
            label: 'Disabled',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_DISABLED',
            name: `fields[${ModuleFieldsConfigurator.moduleField.order}][disabled]`,
            optionGroup: 1,
            tooltip: 'Use this to determine if the field in the form should be disabled.',
            value: ModuleFieldsConfigurator.moduleField.disabled,
            vueComponent: 'Boolean',
        },

        /**
         * Hidden field
         */
        {
            defaultValue: false,
            id: `${ModuleFieldsConfigurator.moduleField.id}|hidden`,
            label: 'Hidden',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_HIDDEN',
            name: `fields[${ModuleFieldsConfigurator.moduleField.order}][hidden]`,
            optionGroup: 1,
            tooltip: 'Use this to determine if the field in the form should be hidden.',
            value: ModuleFieldsConfigurator.moduleField.hidden,
            vueComponent: 'Boolean',
        },

        /**
         * Is system field
         */
        {
            defaultValue: false,
            id: `${ModuleFieldsConfigurator.moduleField.id}|is_system`,
            label: 'Is System',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_IS_SYSTEM',
            name: `fields[${ModuleFieldsConfigurator.moduleField.order}][is_system]`,
            optionGroup: 1,
            tooltip: 'Use this to determine if the field in the form should be only read and assigned by the system.',
            value: ModuleFieldsConfigurator.moduleField.is_system,
            vueComponent: 'Boolean',
        },

        /**
         * Nullable field
         */
        {
            defaultValue: true,
            id: `${ModuleFieldsConfigurator.moduleField.id}|nullable`,
            label: 'Nullable',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_NULLABLE',
            name: `fields[${ModuleFieldsConfigurator.moduleField.order}][nullable]`,
            optionGroup: 1,
            tooltip: 'Sets the nullable parameter in the database.',
            value: ModuleFieldsConfigurator.moduleField.nullable,
            vueComponent: 'Boolean',
        },

        /**
         * Indexed field
         */
        {
            defaultValue: false,
            id: `${ModuleFieldsConfigurator.moduleField.id}|indexed`,
            label: 'Indexed',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_INDEXED',
            name: `fields[${ModuleFieldsConfigurator.moduleField.order}][indexed]`,
            optionGroup: 1,
            tooltip: 'Adds the field indexing in the database.',
            value: ModuleFieldsConfigurator.moduleField.indexed,
            vueComponent: 'Boolean',
        },

        /**
         * Is default search choice field
         */
        {
            defaultValue: false,
            id: `${ModuleFieldsConfigurator.moduleField.id}|is_default_search_choice`,
            label: 'Is Default Search Choice',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_IS_DEFAULT_SEARCH_CHOICE',
            name: `fields[${ModuleFieldsConfigurator.moduleField.order}][is_default_search_choice]`,
            optionGroup: 1,
            tooltip: 'Use to determine which field should receive the data created via select2.js plugin.',
            value: ModuleFieldsConfigurator.moduleField.is_default_search_choice,
            vueComponent: 'Boolean',
        },
    ];

    // Gets name of the fields edited
    const fieldType = mapFromId[
        parseInt(ModuleFieldsConfigurator.moduleField.type, 10)
    ];

    // Special case for many-to-*, adds more field options
    if (fieldTypesWithRelations.includes(fieldType.id)) {
        fieldOptions.push(
            /**
             * Relation name field
             */
            {
                id: ModuleFieldsConfigurator.moduleField.id + '|relation_name',
                label: 'Relation Name',
                mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_RELATION_NAME',
                name: `fields[${ModuleFieldsConfigurator.moduleField.order}][relation_name]`,
                optionGroup: 0,
                placeholder: 'my_relation_name',
                tooltip: 'Name of the relation in snake_case notation (if the field represents a relation).',
                value: ModuleFieldsConfigurator.moduleField.relation_name,
                vueComponent: 'InputText',
            },

            /**
             * Related module field
             */
            {
                disabled: !ModuleFieldsConfigurator.moduleField.newField,
                id: ModuleFieldsConfigurator.moduleField.id + '|related_module',
                label: 'Related Module',
                mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_RELATED_MODULE',
                name: `fields[${ModuleFieldsConfigurator.moduleField.order}][related_module]`,
                optionGroup: 0,
                optionsData: _mapRelatedModulesSelect2(ModuleFieldsConfigurator.photonModules),
                preselectFirst: true,
                tooltip: 'ID of a related module (if the field represents a relation).',
                value: ModuleFieldsConfigurator.moduleField.related_module || 0,
                vueComponent: 'ManyToMany',
            },

            /**
             * Pivot table field
             */
            {
                id: ModuleFieldsConfigurator.moduleField.id + '|pivot_table',
                label: 'Pivot Table Name',
                mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_PIVOT_TABLE',
                name: `fields[${ModuleFieldsConfigurator.moduleField.order}][pivot_table]`,
                optionGroup: 1,
                placeholder: 'my_pivot_table_name',
                tooltip: 'Name of the pivot table in snake_case notation (if the field represents a many-to-many relation). If the value is not provided, it will be auto-generated.',
                value: ModuleFieldsConfigurator.moduleField.pivot_table,
                vueComponent: 'InputText',
            },

            /**
             * Local key field
             */
            {
                id: ModuleFieldsConfigurator.moduleField.id + '|local_key',
                label: 'Local Key',
                mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_LOCAL_KEY',
                name: `fields[${ModuleFieldsConfigurator.moduleField.order}][local_key]`,
                optionGroup: 1,
                placeholder: 'my_local_key_name',
                tooltip: 'Custom local key in snake_case notation that will be used for relation. If the value is not provided, it will be auto-generated.',
                value: ModuleFieldsConfigurator.moduleField.local_key,
                vueComponent: 'InputText',
            },

            /**
             * Foreign key field
             */
            {
                id: ModuleFieldsConfigurator.moduleField.id + '|foreign_key',
                label: 'Foreign Key',
                mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_FOREIGN_KEY',
                name: `fields[${ModuleFieldsConfigurator.moduleField.order}][foreign_key]`,
                optionGroup: 1,
                placeholder: 'my_foreign_key_name',
                tooltip: 'Custom foreign key in snake_case notation that will be used for relation. If the value is not provided, it will be auto-generated. In case of One to many field user must provide custom foreign_key in order for relation within module to be properly generated.',
                value: ModuleFieldsConfigurator.moduleField.foreign_key,
                vueComponent: 'InputText',
            },

            /**
             * Lazy loading field
             */
            {
                defaultValue: true,
                id: ModuleFieldsConfigurator.moduleField.id + '|lazy_loading',
                label: 'Lazy Loading',
                mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_LAZY_LOADING',
                name: `fields[${ModuleFieldsConfigurator.moduleField.order}][lazy_loading]`,
                optionGroup: 1,
                tooltip: 'Use this to determine if values should be lazy-loaded.',
                value: ModuleFieldsConfigurator.moduleField.lazy_loading,
                vueComponent: 'Boolean',
            },

            /**
             * Can create search choice field
             */
            {
                defaultValue: false,
                id: ModuleFieldsConfigurator.moduleField.id + '|can_create_search_choice',
                label: 'Can Create Search Choice',
                mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_CAN_CREATE_SEARCH_CHOICE',
                name: `fields[${ModuleFieldsConfigurator.moduleField.order}][can_create_search_choice]`,
                optionGroup: 1,
                tooltip: 'Use to determine if this field can trigger the creation of search choice.',
                value: ModuleFieldsConfigurator.moduleField.can_create_search_choice,
                vueComponent: 'Boolean',
            },

            /**
             * Flatten to optgroups field
             */
            {
                defaultValue: false,
                id: ModuleFieldsConfigurator.moduleField.id + '|flatten_to_optgroups',
                label: 'Flatten to optgroups',
                mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_FLATTEN_TO_OPTGROUPS',
                name: `fields[${ModuleFieldsConfigurator.moduleField.order}][flatten_to_optgroups]`,
                optionGroup: 1,
                tooltip: 'If set to true, select2.js plugin will flatten all entries fetched from multilevel sortable module, and use the root level as optgorups, and everything below as options.',
                value: ModuleFieldsConfigurator.moduleField.flatten_to_optgroups,
                vueComponent: 'Boolean',
            },

            /**
             * Is active entry filter field
             */
            {
                defaultValue: false,
                id: ModuleFieldsConfigurator.moduleField.id + '|is_active_entry_filter',
                label: 'Is active entry filter',
                mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_IS_ACTIVE_ENTRY_FILTER',
                name: `fields[${ModuleFieldsConfigurator.moduleField.order}][is_active_entry_filter]`,
                optionGroup: 1,
                tooltip: 'Name of the field from related module that is used for filtering search choices for select2.js plugin.',
                value: ModuleFieldsConfigurator.moduleField.is_active_entry_filter,
                vueComponent: 'InputText',
            }
        );

        return fieldOptions;
    }

    /**
     * Column name field
     */
    fieldOptions.push({
        disabled: !ModuleFieldsConfigurator.moduleField.newField,
        id: `${ModuleFieldsConfigurator.moduleField.id}|column_name`,
        label: 'Column Name',
        mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_FIELD_COLUMN_NAME',
        name: `fields[${ModuleFieldsConfigurator.moduleField.order}][column_name]`,
        optionGroup: 0,
        placeholder: 'my_column_name',
        tooltip: 'Name of the DB column for the field in snake_case notation.',
        value: ModuleFieldsConfigurator.moduleField.column_name,
        vueComponent: 'InputText',
    });

    return fieldOptions;
};
