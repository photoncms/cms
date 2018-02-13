import _ from 'lodash';

import InfiniteLoading from 'vue-infinite-loading';

import {
    mapActions,
    mapGetters,
} from 'vuex';

import Vue from 'vue';

import { api } from '_/services/api';

import { config } from '_/config/config';

import { pError } from '_/helpers/logger';

import { store } from '_/vuex/store';

const Layout = Vue.component(
        'Layout',
        require('_/components/FieldTypes/AssetsManager/Layout/Layout.vue')
    );

const ImageSizes = Vue.component(
        'ImageSizes',
        require('_/components/FieldTypes/Select2/Select2.vue')
    );

const Cropper = Vue.component(
        'Cropper',
        require('_/components/FieldTypes/Cropper/Cropper.vue')
    );

const AssetEditor = Vue.component(
        'AssetEditor',
        require('_/components/FieldTypes/AssetsManager/AssetEditor/AssetEditor.vue')
    );

const AssetAdvancedSearch = Vue.component(
        'AssetAdvancedSearch',
        require('_/components/FieldTypes/AssetsManager/AssetAdvancedSearch/AssetAdvancedSearch.vue')
    );

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        assetsManager: {
            type: Object,
            required: true,
        },
    },

    /**
     * Set the data property
     *
     * @return  {object}
     */
    data() {
        return {
            /**
             * Sest the active view
             *
             * @type  {string}
             */
            activeView: 'thumbnail',

            /**
             * Toggles the assets manager delete confirmation buttons
             *
             * @type  {boolean}
             */
            assetsDeleteConfirmationVisible: false,

            /**
             * Defines the sorting options values
             *
             * @type  {Array}
             */
            sortingOptions: [
                {
                    title: 'File Name &uarr;',
                    value: 'file_name|asc',
                },
                {
                    title: 'File Name &darr;',
                    value: 'file_name|desc',
                },
                {
                    title: 'Modification Date &uarr;',
                    value: 'updated_at|asc',
                },
                {
                    title: 'Modification Date &darr;',
                    value: 'updated_at|desc',
                    selected: true,
                },
            ],

            /**
             * Containg the data passed to Select2 field-type module
             *
             * @type  {Object}
             */
            imageSizesOptions: {
                disabled: false,
                id: 0,
                lazyLoading: false,
                multiple: false,
                name: 'imageSizesPicker',
                placeholder: '',
                preloadDataConfig: {
                    method: 'post',
                    payload: {
                        include_relations: false,
                    },
                    resultsObjectPath: 'body.body.entries',
                    valuesOfInterest: {
                        id: 'id',
                        text: 'anchor_html',
                    },
                    url: false,
                },
                readonly: false,
                relatedTableName: 'image_sizes',
                required: true,
                value: null,
            },

            /**
             * Contains all image sizes, as returned by the API
             *
             * @type  {Array}
             */
            imageSizes: [],

            /**
             * If an image size is selected, this proprty will containg the selected image size object
             *
             * @type  {mixed}
             */
            selectedImageSize: null,

            /**
             * Toggles the visibility of imgAreaSelect module
             *
             * @type  {boolean}
             */
            imgAreaSelectActive: false,

            /**
             * If an image crop area is edited this property will be an object containing values needed to perform
             * the imageUpdate action
             *
             * @type  {mixed}
             */
            editedResizeImageData: false,

            /**
             * A property watched by ImgAreaSelect module used to reinstantiate the plugin as needed
             *
             * @type  {boolean}
             */
            imgAreaSelectActiveRefresh: false,

            /**
             * Used as prop to instruct the AssetEditor to engage a getFields() action
             *
             * @type  {string}
             */
            updateAssetEditor: 1,
        };
    },

    /**
     * Set the components
     *
     * @return  {object}
     */
    components: {
        AssetAdvancedSearch,
        AssetEditor,
        Cropper,
        ImageSizes,
        InfiniteLoading,
        Layout,
    },

    /**
     * Set the methods
     *
     * @type  {object}
     */
    methods: {
        ...mapActions('admin', [
            'setAssetManagerSearchFilterObject',
        ]),

        /**
         * Cancels image framing and opens the advanced search tab
         *
         * @return  {void}
         */
        cancelImageFraming () {
            $(this.$el).find('#accordion-assets-manager-search-label').click();
        },

        /**
         * Shows deletion confirmation dialogue
         *
         * @param   {boolean}  value
         * @return  {void}
         */
        deleteAsset: function(id) {
            store.dispatch(`${this.assetsManager.moduleName}/deleteAsset`, { id });

            this.assetsDeleteConfirmationVisible = false;
        },

        /**
         * Fired on detected onChange event
         *
         * @param   {integer}  options.value
         * @return  {void}
         */
        onChange: function({ value }) {
            this.selectedImageSize = _.find(this.imageSizes, { 'id': parseInt(value) });
        },

        /**
         * Fired upon receiving the onImgAreaSelect event from the ImgAreaSelect module
         *
         * @param   {integer}  options.id
         * @param   {integer}  options.height
         * @param   {integer}  options.topX
         * @param   {integer}  options.topY
         * @param   {integer}  options.width
         * @return  {void}  [description]
         */
        onImgAreaSelect: function({ id, height, topX, topY, width }) {
            this.editedResizeImageData = {
                height,
                id,
                width,
                x: topX,
                y: topY,
            };
        },

        /**
         * Instructs the AssetEditor to engage a getFields() action
         *
         * @return  {void}
         */
        setUpdateAssetEditor () {
            this.updateAssetEditor = moment().valueOf();
        },

        /**
         * Shows deletion confirmation dialogue
         *
         * @param   {boolean}  value
         * @return  {void}
         */
        predeleteAsset (value) {
            this.assetsDeleteConfirmationVisible = value;
        },

        /**
         * Preloads image sizes
         *
         * @return  {void}
         */
        preloadImageSizes () {
            const uri = `${config.ENV.apiBasePath}/filter/image_sizes`;

            const payload = {
                include_relations: false,
            };

            api.post(uri, payload)
                .then((response) => {
                    this.imageSizes = response.body.body.entries;

                    if (!_.isEmpty(this.imageSizes[this.ui.photonConfig.assetManager.preselectedImageSizeIndex])) {
                        this.selectedImageSize = this.imageSizes[this.ui.photonConfig.assetManager.preselectedImageSizeIndex];
                    } else {
                        this.selectedImageSize = this.imageSizes[0] ? this.imageSizes[0] : null;
                    }

                    this.imageSizesOptions.value = this.selectedImageSize.id;
                })
                .catch((response) => {
                    pError('Failed to preload image sizes.', response);
                });
        },

        /**
         * Dispatches the resizeImage action after clicking the 'Update Resized Image' button
         *
         * @return  {void}
         */
        resizeImage () {
            if (!this.editedResizeImageData) {
                return;
            }

            store.dispatch(`${this.assetsManager.moduleName}/resizeImage`, this.editedResizeImageData)
                .then(() => {
                    this.editedResizeImageData = false;
                })
                .catch((response) => {
                    pError('Failed to complete the resize image action.', response);
                });
        },

        /**
         * Toggles the imgAreaSelectActiveRefresh property in order to reinstantiate the ImgAreaSelect plugin
         *
         * @return  {void}
         */
        revertSelection () {
            this.editedResizeImageData = false;

            this.imgAreaSelectActiveRefresh = !this.imgAreaSelectActiveRefresh;
        },

        /**
         * Sets the search parameter
         *
         * @param  {event}  event
         * @return  {void}
         */
        search: _.debounce(
            function(event) {
                const payload = {
                    file_name: {
                        like: event.target.value,
                    },
                };

                store.dispatch(`${this.assetsManager.moduleName}/setFilterObject`, {
                    value: payload,
                })
                    .then(() => {
                        store.dispatch(`${this.assetsManager.moduleName}/setFilterObject`, { value: payload })
                            .then(() => {
                                // Remember the search filter in admin module as well
                                this.setAssetManagerSearchFilterObject({ value: payload })
                                    .then(() => {
                                        this.$refs.infiniteLoading.$emit('$InfiniteLoading:reset');
                                    });
                            });

                    });
            }
            , 500),

        /**
         * Adds the asset to a list of selected assets
         *
         * @param   {object}  asset
         * @return  {void}
         */
        selectAsset (asset) {
            this.setUpdateAssetEditor();

            store.dispatch(`${this.assetsManager.moduleName}/selectAsset`, { asset });
        },

        /**
         * Sets the active view
         *
         * @param   {string}  view
         * @return  {void}
         */
        setActiveView (view) {
            this.activeView = view;
        },

        /**
         * Sets the sorting option
         *
         * @param  {event}  event
         * @return  {void}
         */
        setSortingOption (event) {
            store.dispatch(`${this.assetsManager.moduleName}/setSortingOption`, { value: event.target.value });

            this.$refs.infiniteLoading.$emit('$InfiniteLoading:reset');
        },

        /**
         * Submits the search, triggered by the advanced search emit
         *
         * @return  {void}
         */
        submitSearch () {
            this.$refs.infiniteLoading.$emit('$InfiniteLoading:reset');
        },

        /**
         * Emits the event upon hitting bottom of an assets container
         *
         * @return  {void}
         */
        onInfinite () {
            store.dispatch(`${this.assetsManager.moduleName}/loadPaginatedAssets`)
                .then(() => {
                    this.$refs.infiniteLoading.$emit('$InfiniteLoading:loaded');
                }, () => {
                    this.$refs.infiniteLoading.$emit('$InfiniteLoading:complete');
                });
        },
    },

    /**
     * Set the computed properties
     *
     * @type  {object}
     */
    computed: {
        ...mapGetters({
            admin: 'admin/admin',
            ui: 'ui/ui',
        }),

        /**
         * Returns true if there's an asset selected
         *
         * @return  {Boolean}
         */
        isAssetSelected () {
            return _.has(this.assetsManager.editedEntry, 'id');
        },

        /**
         * Calculates if the table class should be activated or not
         *
         * @return  {object}
         */
        tableClassActive () {
            return {
                active: this.activeView === 'table'
            };
        },

        /**
         * Calculates if the thumbnail class should be activated or not
         *
         * @return  {object}
         */
        thumbnailClassActive () {
            return {
                active: this.activeView === 'thumbnail'
            };
        },

        /**
         * Fetches the resized image object
         *
         * @return  {object}
         */
        resizedImage () {
            const resizedImages = this.assetsManager.editedEntry.resized_images;

            return _.find(resizedImages, { 'image_size': parseInt(this.selectedImageSize.id) });
        },

        /**
         * Creates the resized image URL
         *
         * @return  {string}
         */
        resizedImageURL () {
            if (_.isObject(this.resizedImage)
                && _.has(this.resizedImage, 'file_url')
                && _.has(this.resizedImage, 'updated_at')) {

                return `${this.resizedImage.file_url}?${this.resizedImage.updated_at}`;
            }

            return null;
        },

        /**
         * Gets the value
         *
         * @return  {string}
         */
        getSearchByFilenameValue () {
            const path = 'filter.file_name.like';

            if (_.has(this.assetsManager, path)) {
                return this.assetsManager.filter.file_name.like;
            }

            return null;
        }

    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(function() {
            // Load the shared per-photon-module-based search filter
            store.dispatch(`${this.assetsManager.moduleName}/setFilterObject`, {
                value: this.admin.assetManagerSearchFilter,
            });

            // Load the shared per-photon-module-based search filter to advancedSearch model as well,
            // so that advanced serch fields are populated with correct search parameters
            store.dispatch('assetsAdvancedSearch/setAdvancedSearchFilterObject', {
                value: this.admin.assetManagerSearchFilter,
            });

            this.preloadImageSizes();

            this.imageSizesOptions.disabled = !this.isAssetSelected;

            $('#accordion-image-editor').off();

            $('#accordion-image-editor').on('shown.bs.collapse', () => {
                this.imgAreaSelectActive = true;
            });

            $('#accordion-image-editor').on('hidden.bs.collapse', () => {
                this.imgAreaSelectActive = false;
            });
        });
    },

    watch: {
        'assetsManager.editedEntry': {
            deep: true,
            handler () {
                this.imageSizesOptions.disabled = !this.isAssetSelected;
            },
        },

    },

};
