import _ from 'lodash';

/**
 * Formats the category modules for use with select2 plugin
 *
 * @param   {array}  modules
 * @return  {object}
 */
const _mapCategoryModulesSelect2 = (modules) => {
    return [{
        id: 0,
        text: 'No Category'
    }]
        .concat(modules.map((module) => {
            return {
                id: module.id,
                text: module.name
            };
        }));
};

export const getModuleOptions = (ModuleConfigurator) => {
    const selectedModule = ModuleConfigurator.generator.selectedModule;

    /**
     * Field configuration (props passed to form components) Type of component is visible in the type property
     * (see comments in components to see what each of these properties is for)
     *
     * @type  {Array}
     */
    let configuration = [
        /**
         * Module type field
         */
        {
            disabled: !ModuleConfigurator.generator.newModule,
            id: 'type',
            label: 'Module Type',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_TYPE',
            name: 'module[type]',
            optionsData: [{
                id: 'single_entry',
                text: 'Single Entry Type Module'
            }, {
                id: 'non_sortable',
                text: 'Non Sortable Module'
            }, {
                id: 'sortable',
                text: 'Sortable Module'
            }, {
                id: 'multilevel_sortable',
                text: 'Multilevel Sortable Module'
            }],
            preselectFirst: true,
            required: true,
            relatedTableName: false,
            tooltip: 'Module type.',
            value: _.has(selectedModule, 'type') ? selectedModule.type : null,
            vueComponent: 'ManyToMany',
        },

        /**
         * Module name field
         */
        {
            id: 'name',
            label: 'Module Name',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_NAME',
            name: 'module[name]',
            placeholder: 'My Module Name',
            tooltip: 'Name of the module.',
            value: _.has(selectedModule, 'name') ? selectedModule.name : null,
            vueComponent: 'InputText',
        },

        /**
         * Table name field
         */
        {
            disabled: !ModuleConfigurator.generator.newModule,
            id: 'table_name',
            label: 'Table Name',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_TABLE_NAME',
            name: 'module[table_name]',
            placeholder: 'my_table_name',
            tooltip: 'Table name for the module in snake_case notation.',
            value: _.has(selectedModule, 'table_name') ? selectedModule.table_name : null,
            vueComponent: 'InputText',
        },

        /**
         * Anchor text field
         */
        {
            id: 'anchor_text',
            label: 'Anchor Text',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_ANCHOR_TEXT',
            name: 'module[anchor_text]',
            placeholder: '{{id}} - {{field_name}}',
            tooltip: 'Stub which will be used to generate human-readable text for each entry representation.',
            value: _.has(selectedModule, 'anchor_text') ? selectedModule.anchor_text : null,
            vueComponent: 'InputText',
        },

        /**
         * Anchor html field
         */
        {
            id: 'anchor_html',
            label: 'Anchor HTML',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_ANCHOR_HTML',
            name: 'module[anchor_html]',
            placeholder: '{{id}} - <strong>{{field_name}}</strong>',
            tooltip: 'Stub which will be used to generate HTML snippet for each entry representation.',
            value: _.has(selectedModule, 'anchor_html') ? selectedModule.anchor_html : null,
            vueComponent: 'InputText',
        },

        /**
         * Slug field
         */
        {
            id: 'slug',
            label: 'Slug',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_SLUG',
            name: 'module[slug]',
            placeholder: '{{id}}-{{field_name}}',
            tooltip: 'Stub which will be used to generate url-friendly text for each entry representation (similar to Anchor Text).',
            value: _.has(selectedModule, 'slug') ? selectedModule.slug : null,
            vueComponent: 'InputText',
        },

        /**
         * Module icon field
         */
        {
            id: 'icon',
            label: 'Icon',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_ICON',
            name: 'module[icon]',
            // optionsData: iconOptionsSelect2,
            // preselectFirst: true,
            tooltip: 'Generic icon name used in Photon CMS frontend.',
            value: _.has(selectedModule, 'icon') ? selectedModule.icon : null,
            vueComponent: 'BootstrapIconPicker',
        },

        /**
         * Parent module field
         */
        {
            disabled: !ModuleConfigurator.generator.newModule,
            id: 'category',
            label: 'Parent Module',
            mutation: 'generator/UPDATE_GENERATOR_SELECTED_MODULE_CATEGORY',
            name: 'module[category]',
            optionsData: _mapCategoryModulesSelect2(ModuleConfigurator.categoryModules),
            preselectFirst: true,
            tooltip: 'Module ID of a parent module. If this value is set then each entry of the current module can belong to an entry of the parent module (scope).',
            value: _.has(selectedModule, 'category') ? selectedModule.category : null,
            vueComponent: 'ManyToMany',
        },
    ];

    return configuration;
};
