import { api } from '_/services/api';

import {
    apiResponseCommit,
    errorCommit
} from '_/vuex/actions/commonActions';

import { store } from '_/vuex/store';

export default {
    /**
     * Retrieves an array of modules.
     *
     * @param  {function}  options.commit
     * @param  {object}  options.state
     * @param  {boolean}  refreshList  Forces data pull from API, instead of getting the cached version from the state.
     * @return  {promise}
     */
    getPhotonModules({commit, state}, {refreshList = false} = {}) {
        if (state.modules && !refreshList) {
            return new Promise(function(resolve) {
                return resolve(state.modules);
            });
        }

        return api.get('modules')
            .then((response) => {
                for (var i = 0; i < response.data.body.modules.length; i++) {
                    if (!response.data.body.modules[i].icon) {
                        // Set default icon if module has no icon set (required by Photon UI)
                        response.data.body.modules[i].icon = store.state.ui.photonConfig.defaultModuleIcon;
                    }
                }

                commit('GET_ALL_MODULES_SUCCESS', response.data.body);

                store.dispatch('generator/populateNodes', response.data.body);

                return response;
            })
            .catch((response) => {
                errorCommit({ commit }, response);
            });
    },

    /**
     * Retrieves a single module by moduleTableName.
     *
     * @param  {function}  options.commit
     * @param  {object}  options.state
     * @param  {string}  moduleTableName
     * @param  {boolean}  refreshModule  Forces data pull from API, instead of getting the cached version from the state.
     * @return  {promise}
     */
    getPhotonModuleInformation({commit, state}, { moduleTableName, refreshModule } = {}) {
        if (state.moduleInformation[moduleTableName] && !refreshModule) {
            return new Promise(function(resolve) {
                return resolve(state.moduleInformation[moduleTableName]);
            });
        }

        return api.get('modules/' + moduleTableName)
            .then((response) => {
                if (!response.data.body.module.icon) {
                    // Set default icon if module has no icon set (required by Photon UI)
                    response.data.body.module.icon = store.state.ui.photonConfig.defaultModuleIcon;
                }

                apiResponseCommit({ commit }, response, 'MODULES');

                return response;
            })
            .catch((response) => {
                errorCommit({ commit }, response, 'MODULES');
            });
    }
};
