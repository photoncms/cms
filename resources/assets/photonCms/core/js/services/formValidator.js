import { pWarn } from '_/helpers/logger';
import { errorTypes } from '_/services/errorTypes';

/**
 * Binds Parsley validator to form
 *
 * @param   {object}  options
 * @return  {object}
 */
export const bindValidatorToForm = (options) => {
    return $(options.selector)
        .parsley()
        .on('form:submit', function() {
            if (typeof options.onSubmit === 'function') {
                options.onSubmit(); // If callback is given execute it here
            }

            return false; // Prevent default form submit
        });
};

// Resets provided validator

/**
 * Resets provided validator
 *
 * @param   {object}  validator
 * @return  {void}
 */
export const resetValidator = (validator) => {
    removeValidatorErrors(validator);

    validator.reset();
};

/**
 * Destroys provided validator
 *
 * @param   {object}  validator
 * @return  {void}
 */
export const destroyValidator = (validator) => {
    removeValidatorErrors(validator);

    validator.destroy();
};

export const removeValidatorErrors = (validator) => {
    validator.$element.find('.form-field-error').each(function() {
        $(this).parsley().removeError($(this).attr('id'));
    });
};

/**
 * Processes API error message and errors from error fields object
 *
 * @param   {object}  validator
 * @param   {object}  errorObject
 * @return  {string}
 */
export const processErrors = (validator, errorObject) => {
    if (errorObject.fields) {
        parseFieldsErrors(validator, errorObject.fields);
    }

    let errorMessage = '';

    // Try translating a message returned by the API using errorTypes else use the unprocessed API message
    if (errorObject.message) {
        errorMessage = (errorTypes[errorObject.message] || errorObject.message);
    }

    return errorMessage;
};

/**
 * Parses fields error object
 *
 * @param   {object}  validator
 * @param   {object}  errorFields
 * @return  {[type]}  [description]
 */
export const parseFieldsErrors = (validator, errorFields) => {
    for (const field in errorFields) {
        if (errorFields.hasOwnProperty(field)) {
            // Find the element the error belongs to by element name attribute
            let $formElementToValidate = validator.$element.find('[id="' + field + '-error"]');

            if (!$formElementToValidate.length) {
                pWarn('No such field name found while parsing fields error object', field);

                continue;
            }

            // Gets and translates (if needed) error value
            let message = errorTypes.translateFieldError(errorFields[field].message);

            // Adds error element to the field, assigning a random id to it, and a message to be displayed
            $formElementToValidate.parsley().addError(field + '-error', { message });
        }
    }
};
