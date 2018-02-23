import * as fieldTypesDependencies from '~/services/fieldTypes';

// Manually defines field types used on API
// There is also an API route returning this, but mapping is still needed
// to connect API field types to Vue components, so it's done manually
export const inputText = {
    id: 1,
    name: 'Input Text',
    vueComponent: 'InputText',
    valueType: 'string',
    isSystem: false
};

export const richText = {
    id: 2,
    name: 'Rich Text',
    vueComponent: 'RichText',
    valueType: 'text',
    isSystem: false
};

export const image = {
    id: 3,
    name: 'Image',
    vueComponent: 'FileUpload',
    valueType: 'string',
    isSystem: false
};

export const booleanSwitch = {
    id: 4,
    name: 'Boolean',
    vueComponent: 'BooleanSwitch',
    valueType: 'boolean',
    isSystem: false
};

export const date = {
    id: 5,
    name: 'Date',
    vueComponent: 'Calendar',
    valueType: 'timestamp',
    isSystem: false
};

export const manyToOne = {
    id: 7,
    name: 'Many to One',
    vueComponent: 'SelectBasic',
    valueType: 'integer',
    isSystem: false
};

export const manyToMany = {
    id: 8,
    name: 'Many to Many',
    vueComponent: 'SelectTags',
    valueType: '',
    isSystem: false
};

export const password = {
    id: 9,
    name: 'Password',
    vueComponent: 'InputPassword',
    valueType: 'string',
    isSystem: false
};

export const integer = {
    id: 10,
    name: 'Integer',
    vueComponent: 'InputText',
    valueType: 'integer',
    isSystem: false
};

export const systemInteger = {
    id: 11,
    name: 'System Integer',
    vueComponent: 'InputText',
    valueType: 'integer',
    isSystem: true
};

export const systemDateTime = {
    id: 12,
    name: 'System Date-time',
    vueComponent: 'DateTime',
    valueType: 'timestamp',
    isSystem: true
};

export const systemString = {
    id: 13,
    name: 'System String',
    vueComponent: 'InputText',
    valueType: 'string',
    isSystem: true
};

export const oneToOne = {
    id: 14,
    name: 'One to One',
    vueComponent: 'SelectBasic',
    valueType: 'integer',
    isSystem: true
};

export const asset = {
    id: 15,
    name: 'Asset',
    vueComponent: 'FileInput',
    valueType: 'string',
    isSystem: false
};

export const assets = {
    id: 16,
    name: 'Assets',
    vueComponent: 'FileInput',
    valueType: 'string',
    multiple: true,
    isSystem: false
};

export const oneToMany = {
    id: 17,
    name: 'One to Many',
    vueComponent: 'SelectTags',
    valueType: 'string',
    isSystem: false
};

export const dateTime = {
    id: 18,
    name: 'Date-time',
    vueComponent: 'DateTime',
    valueType: 'timestamp',
    isSystem: false
};

export const gallery = {
    id: 19,
    name: 'Gallery',
    vueComponent: 'Gallery',
    valueType: 'integer',
    isSystem: false
};

export const manyToOneExtended = {
    id: 20,
    name: 'Many to One Extended',
    vueComponent: 'Extended',
    valueType: 'integer',
    isSystem: false
};

export const manyToManyExtended = {
    id: 21,
    name: 'Many to Many Extended',
    vueComponent: 'Extended',
    valueType: 'integer',
    isSystem: false
};

export const oneToOneExtended = {
    id: 22,
    name: 'One to One Extended',
    vueComponent: 'Extended',
    valueType: 'integer',
    isSystem: false
};

export const oneToManyExtended = {
    id: 23,
    name: 'One to Many Extended',
    vueComponent: 'Extended',
    valueType: 'integer',
    isSystem: false
};

export const permissions = {
    id: 24,
    name: 'Permissions',
    vueComponent: 'Permissions',
    valueType: 'integer',
    isSystem: false
};

// Field id to field object map
export const mapFromId = {
    ...fieldTypesDependencies.mapFromIdDependencies,
    1: inputText,
    2: richText,
    3: image,
    4: booleanSwitch,
    5: date,
    7: manyToOne,
    8: manyToMany,
    9: password,
    10: integer,
    11: systemInteger,
    12: systemDateTime,
    13: systemString,
    14: oneToOne,
    15: asset,
    16: assets,
    17: oneToMany,
    18: dateTime,
    19: gallery,
    20: manyToOneExtended,
    21: manyToManyExtended,
    22: oneToOneExtended,
    23: oneToManyExtended,
    24: permissions,
};

/**
 * Helper function to generate field type options formatted for select2
 *
 * @return  {array}
 */
export const getFieldTypeOptionsSelect2 = function() {
    const options = [];

    for (let fieldTypeId in mapFromId) {
        // Skip the Image field type
        if (fieldTypeId == 3) {
            continue;
        }

        if (mapFromId.hasOwnProperty(fieldTypeId)) {
            options.push({
                id: fieldTypeId,
                text: mapFromId[fieldTypeId].name,
            });
        }
    }

    return options;
};
