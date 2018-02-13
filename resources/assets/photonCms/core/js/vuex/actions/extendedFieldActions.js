import { config } from '_/config/config';

import { errorCommit } from '_/vuex/actions/commonActions';

import { api } from '_/services/api';

import { pError } from '_/helpers/logger';

export default {
    /**
     * Creates a new gallery entry
     *
     * @param   {function}  options.commit
     * @param   {string}  options.title
     * @return  {object}
     */
    createGallery({ commit }, { title }) {
        const payload = {
            title
        };

        return api.post('galleries', payload)
            .then((response) => {
                // commit(types.LOAD_DYNAMIC_MODULE_ENTRY_SUCCESS, { entry: response.data.body.entry });

                return response;
            })
            .catch((response) => {
                pError('Failed to create the gallery', response);
            });
    },

    /**
     * Creates a new gallery item entry
     *
     * @param   {function}  options.commit
     * @param   {integer}  options.assetId
     * @param   {integer}  options.scopeId
     * @return  {object}
     */
    createGalleryItem({ commit }, { assetId, scopeId }) {
        const payload = {
            asset: assetId,
            scope_id: scopeId,
        };

        return api.post('gallery_items', payload)
            .then((response) => {
                return response;
            })
            .catch((response) => {
                pError('Failed to create gallery item', response);
            });
    },

    /**
     * Deletes a new gallery item entry
     *
     * @param   {function}  options.commit
     * @param   {integer}  options.assetId
     * @return  {object}
     */
    deleteGalleryItem({ commit }, { assetId }) {
        return api.delete(`gallery_items/${assetId}`)
            .then((response) => {
                return response;
            })
            .catch((response) => {
                pError('Failed to delete a gallery item', response);
            });
    },

    /**
     * Creates a new gallery item entry
     *
     * @param   {function}  options.commit
     * @param   {string}  options.action
     * @param   {integer}  options.affectedItemId
     * @param   {integer}  options.targetItemId
     * @return  {object}
     */
    repositionGalleryItem({ commit }, { action, affectedItemId, targetItemId }) {
        const payload = {
            action,
            affected: {
                table: 'gallery_items',
                id: affectedItemId
            },
            target: {
                id: targetItemId
            }
        };

        const uri = `${config.ENV.apiBasePath}/nodes/reposition`;

        return api.put(uri, payload)
            .then((response) => {
                return response;
            })
            .catch((response) => {
                pError('Failed to reposition gallery item', response);
            });
    },
};
