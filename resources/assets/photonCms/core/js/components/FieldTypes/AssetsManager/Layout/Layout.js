import _ from 'lodash';

import { mapGetters } from 'vuex';

import { formatBytes } from '_/helpers/formatBytes';

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        activeView: {
            required: true,
            type: String,
        },
        assetsManager: {
            type: Object,
            required: true,
        },
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            ui: 'ui/ui',
        }),

        /**
         * Date format getter
         *
         * @return  {string}
         */
        dateFormat () {
            return this.ui.photonConfig.dateFormat;
        },
    },

    /**
     * Set the methods
     *
     * @type  {object}
     */
    methods: {
        /**
         * Calculates if the 'active' class should be activated or not
         *
         * @return  {object}
         */
        isActiveAsset (id) {
            return (this.assetsManager.editedEntry.id && this.assetsManager.editedEntry.id === id);
        },

        /**
         * Calculates if the 'selected' class should be activated or not
         *
         * @return  {object}
         */
        isSelectedAsset (id) {
            return this.assetsManager.selectedAssetsIds.indexOf(id) >= 0;
        },

        /**
         * Formats byte to human readable value
         *
         * @param   {integer}  bytes
         * @return  {string}
         */
        formatBytes,

        /**
         * Returns asset thumbnail
         *
         * @param   {object}  asset
         * @return  {string}
         */
        getAssetThumbnail (asset) {
            return !_.isEmpty(asset.resized_images) ? asset.resized_images[0].file_url : '';
        },

        /**
         * Emits the event upon file selection
         *
         * @param   {integer}  id
         * @return  {void}
         */
        selectAsset: _.throttle(
            function (id) {
                this.$emit('selectedAsset', id);
            },
            200,
            {
                leading: true,
                trailing: false
            }),

        /**
         * Updated at getter
         *
         * @param   {integer}  entry
         * @return  {string}
         */
        updatedAt (entry) {
            return entry.updated_at
                ? moment(
                    entry.updated_at
                ).format(this.dateFormat)
                : null;
        },
    },
};
