import _ from 'lodash';

import adminFactory from '_/vuex/modules/admin';

import assetsManagerFactory from '_/vuex/modules/assetsManager';

import { store } from '_/vuex/store';

import { storage } from '_/services/storage';

/**
 * Lists extended fields that need to have their dynamic modules registered
 *
 * @type  {Array}
 */
const extendedFields = [
    'many_to_many_extended',
    'one_to_many_extended',
    'one_to_one_extended',
];

/**
 * Lists fields that need to have their assets manager dynamic modules registered
 *
 * @type  {Array}
 */
const assetManagerFields = [
    'asset',
    'assets',
    'gallery',
    'rich_text',
];

const multipleOptionsTypes = [
    'assets',
    'many_to_many',
    'many_to_many_extended',
    'one_to_many',
    'one_to_many_extended',
];

/**
 * Checks if field should be disabled
 *
 * @param   {object}  moduleField
 * @param   {object}  Entry
 * @return  {boolean}
 */
const checkIfFieldDisabled = ({ Entry, moduleField }) => {
    // If disabled is passed as field option, disable field
    if (moduleField.disabled) {
        return true;
    }

    if((moduleField.relation_name === 'roles' && moduleField.pivot_table === 'user_has_roles')
        || (moduleField.relation_name === 'permissions' && moduleField.pivot_table === 'user_has_permissions')) {
        const licenseStatus = storage.get('licenseStatus', true);

        if (!(licenseStatus.domainType === 1 || licenseStatus.licenseType === 4)) {
            return true;
        }
    }

    // If in create entry mode and current field is in the edit restricted array for this module, disable field
    if (Entry.admin.editorMode === 'create'
        && _.has(Entry.admin, 'selectedModule.permission_control.edit_restrictions')
        && Entry.admin.selectedModule.permission_control.edit_restrictions.indexOf(moduleField.unique_name) > -1) {
        return true;
    }

    // If in edit entry mode and current field is in the edit restricted array for this entry, disable field
    if (Entry.admin.editorMode === 'edit'
        && _.has(Entry.admin, 'editedEntry.permission_control.edit_restrictions')
        && Entry.admin.editedEntry.permission_control.edit_restrictions.indexOf(moduleField.unique_name) > -1) {
        return true;
    }

    // If in edit entry mode and entry's permissions contain update:false, disable field
    if (Entry.admin.editorMode === 'edit'
        && _.has(Entry.admin, 'editedEntry.permission_control.crud.update')
        && !Entry.admin.editedEntry.permission_control.crud.update) {
        return true;
    }

    // If in edit entry mode and this field is not an editable field as defined by module field setting, disable field
    if (Entry.admin.editorMode === 'edit' && !moduleField.editable) {
        return true;
    }

    return false;
};

/**
 * Generate field options
 *
 * @param   {object}  moduleField
 * @param   {object}  Entry
 * @param   {function}  customCallback
 * @return  {object}
 */
const makeField = (moduleField, Entry, customCallback) => {
    const fieldType = Object.assign({}, store.state.photonField.fieldTypes[moduleField.type]);

    const relatedTableName = getRelatedTableName({
        fieldType: fieldType.type,
        Entry,
        moduleField,
    });

    const relationFieldRequired = checkIfRelationFieldIsRequired({
        fieldType: fieldType.type,
        relatedTableName,
    });

    const fieldOptions = {
        canCreateSearchChoice: moduleField.can_create_search_choice,
        defaultValue: moduleField.default == 1 ? true : false,
        disabled: checkIfFieldDisabled({
            Entry,
            moduleField,
        }),
        flattenToOptgroups: moduleField.flatten_to_optgroups,
        hidden: moduleField.hidden,
        id: moduleField.id,
        isActiveEntryFilter: moduleField.is_active_entry_filter,
        isSystem: moduleField.is_system,
        label: moduleField.name,
        lazyLoading: moduleField.lazy_loading,
        multiple: (_.indexOf(multipleOptionsTypes, fieldType.type) > -1) ? true : false,
        mutation: `${Entry.admin.moduleName}/UPDATE_MODULE_ENTRY_FIELD`,
        name: moduleField.unique_name,
        relatedEntry: Entry.admin.editedEntry[moduleField.unique_name + '_related_entry'],
        relatedTableName: relatedTableName,
        required: relationFieldRequired,
        tooltip: moduleField.tooltip_text,
        value: Entry.admin.editedEntry[moduleField.unique_name],
        vueComponent: fieldType.component,
    };

    if (_.indexOf(extendedFields, fieldType.type) > -1) {
        const dynamicModuleName = `extended-${moduleField.unique_name}-${moduleField.id}`;

        if(!_.has(store.state, dynamicModuleName)) {
            store.registerModule(dynamicModuleName, adminFactory(dynamicModuleName));
        }
    }

    if (_.indexOf(assetManagerFields, fieldType.type) > -1) {
        const assetsManagerDynamicModuleName = `assetsManager-${moduleField.unique_name}-${moduleField.id}`;

        if(!_.has(store.state, assetsManagerDynamicModuleName)) {
            store.registerModule(assetsManagerDynamicModuleName, assetsManagerFactory(assetsManagerDynamicModuleName));
        }
    }

    // If a custom callback function was defined execute it here
    if (typeof customCallback === 'function') {
        customCallback(moduleField, fieldOptions, Entry);
    }

    return fieldOptions;
};

/**
 * Gets a related table name
 *
 * @param   {object}  options.Entry
 * @param   {string}  options.fieldTypeName
 * @param   {object}  options.moduleField
 * @return  {string}
 */
function getRelatedTableName({ Entry, fieldType, moduleField }) {
    if (_.isEmpty(moduleField.related_module)) {
        // Gets related table name for module related to the current field (just to shorten syntax in options)
        return Entry.photonModule.moduleIdToTableNameMap[moduleField.related_module];
    }

    if (fieldType === 'file' || fieldType === 'files') {
        // Related table is hard-coded to assets (the only module containing file assets)
        return 'assets';
    }

    return false;
}

/**
 * Check if field's validation_rules contain string 'required', if so force selection for this dropdown
 *
 * @param   {string}  options.relatedTableName
 * @param   {object}  options.moduleField
 * @return  {boolean}
 */
function checkIfRelationFieldIsRequired({ fieldType, validationRules }) {
    if (fieldType === 'many_to_one' || fieldType === 'many_to_many') {
        return validationRules && (validationRules.indexOf('required') > -1) ? true : false;
    }

    return false;
}

/**
 * Dynamically creates fields from Entry.admin.selectedModule.fields
 *
 * @param   {object}  Entry
 * @param   {function}  customCallback
 * @return  {object}
 */
export const getEntryFields = (Entry, customCallback) => {
    if (!Entry.admin.selectedModule || !Entry.admin.selectedModule.fields) {
        return [];
    }

    return Entry.admin.selectedModule.fields
        .map((field) => {
            return makeField(field, Entry, customCallback);
        });
};
