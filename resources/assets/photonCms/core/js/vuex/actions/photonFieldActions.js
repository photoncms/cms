import { api } from '_/services/api';
import {
    apiResponseCommit,
    errorCommit,
} from '_/vuex/actions/commonActions';

export default {
    /**
     * Retrieves an array of fields.
     *
     * @param  {function}  options.commit
     * @param  {object}  options.state
     * @param  {boolean}  refreshList  Forces data pull from API, instead of getting the cached version from the state.
     * @return  {promise}
     */
    getPhotonFields({ commit, state }, { refreshList = false } = {}) {
        if (state.photonField && !refreshList) {
            return new Promise(function(resolve) {
                resolve(state.photonField);
            });
        }

        return api.get('field_types')
            .then((response) => {
                apiResponseCommit({ commit }, response, 'FIELDS');
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'FIELDS');
            });
    },
};
