import { updateValueExtender } from '_/extenders/updateValueExtender';

/**
 * Action to update field values using passed arguments
 *
 * @param   {function}  options.commit
 * @param   {string}  mutation
 * @param   {integer}  id
 * @param   {string}  name
 * @param   {object}  newValue
 * @return  {void}
 */
export const updateValue = ({ dispatch, commit }, mutation, id, name, newValue) => {
    commit(mutation, { id, name, newValue });

    updateValueExtender({ dispatch, commit }, id, name, newValue);
};
