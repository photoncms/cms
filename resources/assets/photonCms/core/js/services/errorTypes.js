import i18n from '_/i18n';

import { customErrorTypes } from '~/services/errorTypes';

export const errorTypes = {
    ...customErrorTypes,

    // API message translations
    ADMIN_CANNOT_DELETE_ENTRY_HAS_RELATIONS: i18n.t('errorTypes.adminCannotDeleteEntryHasRelations'),
    ADMIN_CANNOT_DELETE_ENTRY_HAS_CHILDREN: i18n.t('errorTypes.adminCannotDeleteEntryHasChildren'),
    ADMIN_INSUFICIENT_PERMISSIONS: i18n.t('errorTypes.adminInsuficientPermissions'),
    ADMIN_INVALID_CSV_FILE: i18n.t('errorTypes.adminInvalidCsvFile'),
    ADMIN_PASSWORD_ALREADY_USED: i18n.t('errorTypes.adminPasswordAlreadyUsed'),
    ADMIN_PHP_NATIVE_EXCEPTION: i18n.t('errorTypes.adminPhpNativeException'),
    ADMIN_TRANSACTION_FAILURE_ROLLED_BACK: i18n.t('errorTypes.adminTransactionFailureRolledBack'),
    ADMIN_VALIDATION_ERROR: i18n.t('errorTypes.adminValidationError'),
    AUTH_INVALID_USER_INVITATION_CODE: i18n.t('errorTypes.authInvalidUserInvitationCode'),
    AUTH_PASSWORD_EXPIRED: i18n.t('errorTypes.authPasswordExpired'),
    AUTH_PHP_NATIVE_EXCEPTION: i18n.t('errorTypes.authPhpNativeException'),
    AUTH_TOO_MANY_ATTEMPTS: i18n.t('errorTypes.authTooManyAttempts'),
    AUTH_USER_LOGIN_FAILURE_INVALID_CREDENTIALS: i18n.t('errorTypes.authUserLoginFailureInvalidCredentials'),
    AUTH_USER_NOT_CONFIRMED: i18n.t('errorTypes.authUserNotConfirmed'),
    AUTH_VALIDATION_ERROR: i18n.t('errorTypes.authValidationError'),
    EMAIL_CONFIRMATION_DYNAMIC_MODULE_ENTRY_NOT_FOUND: i18n.t('errorTypes.emailConfirmationInvalidEmailChangeConfirmationCode'),
    EMAIL_CONFIRMATION_INVALID_EMAIL_CHANGE_CONFIRMATION_CODE: i18n.t('errorTypes.emailConfirmationInvalidEmailChangeConfirmationCode'),
    EMAIL_CONFIRMATION_INVALID_USER_CONFIRMATION_CODE: i18n.t('errorTypes.emailConfirmationInvalidUserConfirmationCode'),
    EMAIL_CONFIRMATION_TOKEN_ABSENT: i18n.t('errorTypes.emailConfirmationTokenAbsent'),
    EMAIL_CONFIRMATION_TOO_MANY_ATTEMPTS: i18n.t('errorTypes.emailConfirmationTooManyAttempts'),
    GENERATOR_TRYING_TO_USE_NON_EXISTING_FIELD_AS_ANCHOR_TEXT: i18n.t('errorTypes.generatorTryingToUseNonExistingFieldAsAnchorText'),
    GENERATOR_VALIDATION_ERROR: i18n.t('errorTypes.generatorValidationError'),
    GENERATOR_PHP_NATIVE_EXCEPTION: i18n.t('errorTypes.adminPhpNativeException'),
    IMPERSONATE_IMPERSONATION_ALREADY_OFF: i18n.t('errorTypes.impersonateImpersonationAlreadyOff'),
    MENU_EDITOR_DELETE_SYSTEM_MENU_FORBIDDEN: i18n.t('errorTypes.menuEditorDeleteSystemMenuForbidden'),
    MENU_EDITOR_VALIDATION_ERROR: i18n.t('errorTypes.menuEditorValidationError'),
    MENU_ITEMS_EDITOR_MENU_ITEM_HAS_CHILDREN: i18n.t('errorTypes.menuItemsEditorMenuItemHasChildren'),
    MENU_ITEMS_EDITOR_VALIDATION_ERROR: i18n.t('errorTypes.menuItemsEditorValidationError'),
    RESET_PASSWORD_ALREADY_USED: i18n.t('errorTypes.resetPasswordAlreadyUsed'),
    RESET_PASSWORD_CHANGE_FAILURE: i18n.t('errorTypes.resetPasswordChangeFailure'),
    RESET_PASSWORD_CHANGE_INVALID_USER: i18n.t('errorTypes.resetPasswordChangeInvalidUser'),
    RESET_PASSWORD_RESET_FAILURE: i18n.t('errorTypes.resetPasswordResetFailure'),
    RESET_PASSWORD_RESET_INVALID_TOKEN: i18n.t('errorTypes.resetPasswordResetInvalidToken'),
    RESET_PASSWORD_RESET_INVALID_USER: i18n.t('errorTypes.resetPasswordResetInvalidUser'),
    RESET_TOO_MANY_ATTEMPTS: i18n.t('errorTypes.resetTooManyAttempts'),
    PHOTON_LICENSE_MAX_NUMBER_OF_USERS: i18n.t('errorTypes.photonLicenseMaxNumberOfUsers'),
    RESET_VALIDATION_ERROR: i18n.t('errorTypes.resetValidationError'),
    UNSUPPORTED_MIME_TYPE: i18n.t('errorTypes.unsupportedMimeType'),
    VALIDATION_ERROR: i18n.t('errorTypes.validationError'),

    // Translate field error function
    translateFieldError (error) {
        // Special cases for generator errors (need some parsing first)
        if (error.indexOf('The module.') > -1) {
            error = 'Module ' + error.split('.')[1] + '.';
        }

        if (error.indexOf('The fields.') > -1) {
            error = 'Field\'s ' + error.split('.')[2] + '.';
        }

        if (fieldErrorTranslations[error]) {
            // If translation for field error is found return it
            return fieldErrorTranslations[error];
        }

        // Else return the field error API returns (which is good 99% of the time)
        return error;
    },
};

// Field error translations
const fieldErrorTranslations = {
    'The fields field is required.': 'At least one module field needs to be set.',
    'The agreement accepted must be accepted.': 'You must accept the user agreement.'
};
