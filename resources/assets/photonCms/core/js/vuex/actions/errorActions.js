import * as types from '_/vuex/mutation-types';

export default {
    /**
     * Sets error page properties to 404 state
     *
     * @param  {function}  options.commit
     * @return  {void}
     */
    set404State({commit}) {
        commit(types.UPDATE_ERROR_PAGE, {
            errorCode: '404',
            errorText: 'Page Not Found',
            errorIcon: 'fa fa-frown'
        });
    },

    /**
     * Sets error page properties to arguments given in options object passed to setError
     *
     * @param  {function}  options.commit
     * @param  {string}  options.errorCode
     * @param  {string}  options.errorText
     * @param  {string}  options.errorIcon
     * @return  {void}
     */
    setError({commit}, {errorCode, errorText, errorIcon}) {
        commit(types.UPDATE_ERROR_PAGE, {
            errorCode: errorCode,
            errorText: errorText,
            errorIcon: errorIcon
        });
    }
};
