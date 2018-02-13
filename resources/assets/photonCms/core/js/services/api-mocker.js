import { pWarn } from '_/helpers/logger';

/**
 * Outputs a console warning about an object being mocked
 * @param  {object} obj
 * @param  {string} message
 */
const _logMocked = (obj, message) => {
    pWarn(`MOCKED ${message}: `, JSON.parse(JSON.stringify(obj)));
};

/**
 * Module Mock helper function
 * @param  {string} module
 * @param  {string} message
 * @param  {object} permissions
 */
const _moduleMock = (module, message, permissions) => {
    module.permission_control = permissions;

    if (message) {
        _logMocked(module, message);
    }

    return module;
};

/**
 * Entry Mock helper function
 * @param  {string} entry
 * @param  {string} message
 * @param  {object} permissions
 */
const _entryMock = (entry, message, permissions) => {
    entry.permission_control = permissions;

    if (message) {
        _logMocked(entry, message);
    }

    return entry;
};

/**
 * Response mocker
 * @param  {object} response
 * @return {object}
 */
export const mockResponse = (response) => {
    const message = response.data.message;

    const body = response.data.body;

    if (message === 'GET_MODULE_INFORMATION_SUCCESS') {

        if (response.request.url.indexOf('modules/invitations') === 0) {
            response.data.body.module = _moduleMock(body.module, message, {
                crud: {
                    create: true,
                    update: true,
                    delete: true
                },
                edit_restrictions: []
            });
        } else {
            response.data.body.module = _moduleMock(body.module, message, {
                crud: {
                    create: true,
                    update: true,
                    delete: true
                },
                edit_restrictions: []
            });
        }

    }

    if (message === 'LOAD_DYNAMIC_MODULE_ENTRY_SUCCESS') {

        if (response.request.url.indexOf('invitations/') === 0) {
            response.data.body.entry = _entryMock(body.entry, message, {
                crud: {
                    create: true,
                    update: true,
                    delete: true
                },
                edit_restrictions: []
            });
        } else {
            response.data.body.entry = _entryMock(body.entry, message, {
                crud: {
                    create: true,
                    update: true,
                    delete: true
                },
                edit_restrictions: []
            });
        }

    }

    return response;
};
