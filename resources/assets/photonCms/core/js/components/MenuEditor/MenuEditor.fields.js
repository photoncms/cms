import _ from 'lodash';

import i18n from '_/i18n';

/**
 * Returns the entry fields configuration object
 *
 * @param   {object}  menuEditor
 * @return  {array}
 */
export const getEntryFields = (vueComponent) => {
    let configuration = [{
        disabled: false,
        hidden: false,
        id: 1,
        isSystem: false,
        label: 'Menu Name',
        lazyLoading: false,
        multiple: false,
        mutation: 'menuEditor/UPDATE_MENU_FIELD',
        name: 'title',
        relatedEntry: null,
        relatedTableName: false,
        required: false,
        tooltip: i18n.t('menuEditor.menuTitleTootltip'),
        value: _.has(vueComponent.editedEntry, 'title') ? vueComponent.editedEntry.title : '',
        vueComponent: 'InputText',
    }, {
        disabled: vueComponent.editorMode === 'edit',
        hidden: false,
        id: 2,
        isSystem: false,
        label: 'System Menu Name',
        lazyLoading: false,
        multiple: false,
        mutation: 'menuEditor/UPDATE_MENU_FIELD',
        name: 'name',
        relatedEntry: null,
        relatedTableName: false,
        required: false,
        tooltip: i18n.t('menuEditor.nameTooltip'),
        value: _.has(vueComponent.editedEntry, 'name') ? vueComponent.editedEntry.name : '',
        vueComponent: 'InputText',
    }, {
        disabled: false,
        hidden: false,
        id: 3,
        isSystem: false,
        label: 'Description',
        lazyLoading: false,
        multiple: false,
        mutation: 'menuEditor/UPDATE_MENU_FIELD',
        name: 'description',
        relatedEntry: null,
        relatedTableName: false,
        required: false,
        tooltip: i18n.t('menuEditor.descriptionTooltip'),
        value: _.has(vueComponent.editedEntry, 'description') ? vueComponent.editedEntry.description : '',
        vueComponent: 'InputText',
    }, {
        disabled: false,
        hidden: false,
        id: 4,
        isSystem: false,
        label: 'Maximum Menu Depth',
        lazyLoading: false,
        multiple: false,
        mutation: 'menuEditor/UPDATE_MENU_FIELD',
        name: 'max_depth',
        relatedEntry: null,
        relatedTableName: false,
        required: false,
        tooltip: i18n.t('menuEditor.maxDepthTooltip'),
        value: _.has(vueComponent.editedEntry, 'max_depth') ? vueComponent.editedEntry.max_depth : '',
        vueComponent: 'InputText',
    }, {
        disabled: false,
        hidden: false,
        id: 5,
        isSystem: false,
        label: 'Menu Link Types',
        lazyLoading: false,
        multiple: true,
        mutation: 'menuEditor/UPDATE_MENU_FIELD',
        name: 'menu_link_types',
        preloadDataConfig: {
            method: 'get',
            resultsObjectPath: 'body.body.menu_link_types',
            url: 'menus/link_types',
            valuesOfInterest: {
                id: 'id',
                text: 'title',
            },
        },
        relatedEntry: null,
        relatedTableName: false,
        required: false,
        tooltip: i18n.t('menuEditor.menuLinkTypesTooltip'),
        value: _.has(vueComponent.editedEntry, 'menu_link_types') ? vueComponent.editedEntry.menu_link_types : '',
        vueComponent: 'ManyToMany',
    }
    ];

    return configuration;
};
