import _ from 'lodash';

import Vue from 'vue';

import { config } from '_/config/config';

import Dropzone from 'dropzone';

import i18n from '_/i18n';

import { store } from '_/vuex/store';

import { eventBus } from '_/helpers/eventBus';

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        autoProcessQueue: {
            default: false,
        },
        multiple: {
            default: true
        },
        path: {
            default: config.ENV.apiBasePath + '/assets'
        },
        file: {
            default: 'file',
            type: String,
        },
        previewTemplate: {
            default: `
                <div class="dz-preview">
                    <div class="dz-image">
                        <i class="fa fa-file-o file-icon"></i>
                        <img data-dz-thumbnail />
                    </div>
                    <div class="dz-details">
                        <div class="progress progress-striped progress-thin active">
                            <div class="progress-bar progress-bar-success" role="progressbar" data-dz-uploadprogress>
                            </div>
                        </div>
                        <div class="dz-filename"><span data-dz-name></span></div>
                        <div class="dz-size" data-dz-size></div>
                        <div class="dz-error-message"><span data-dz-errormessage></span></div>
                    </div>
                    <a type="button" class="close">&times;</a>
                </div>
            `
        },
        showRemoveLink: {
            default: true,
        },
        onSuccess: {
            default: null
        },
        createImageThumbnails: {
            default: true,
        },
        vuexModule: {
            required: true,
            type: String,
        },
    },

    /**
     * Set the data property
     *
     * @return  {object}
     */
    data() {
        return {
            // Stores files array
            files: [],

            // Stores dropZone plugin instance
            dropZone: null
        };
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        /**
         * Bind evenBus listeners
         *
         * @return  {void}
         */
        initEventBusListener () {
            const self = this;

            eventBus.$on('submitSelectedAssets', () => {
                $(self.$el).find('.dz-error-message').each(function() {
                    $(this).html('<span data-dz-errormessage></span>');
                });

                $(self.$el).find('.progress-bar-danger').each(function() {
                    $(this).removeClass('progress-bar-danger')
                        .addClass('progress-bar-success')
                        .css('width', 0);
                });

                this.dropZone.processQueue();
            });
        },
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        /**
         * Create the admin getter based on the vuexModule prop
         *
         * @return  {object}
         */
        admin () {
            const getterName = `${this.vuexModule}/${this.vuexModule}`;

            return this.$store.getters[getterName];
        },
    },

    /**
     * Define the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(() => {
            const self = this;

            $('#clear-files').click(() => {
                this.dropZone.removeAllFiles();
            });

            self.initEventBusListener();

            Dropzone.prototype.requeueFiles = function(file) {
                file.status = Dropzone.QUEUED;

                file.upload.progress = 0;

                file.upload.bytesSent = 0;
            };

            // Set Dropzone options
            Dropzone.autoDiscover = false;

            let params = {
                autoProcessQueue: this.autoProcessQueue,
                clickable: '#upload-files',
                createImageThumbnails: this.createImageThumbnails,
                dictResponseError: 'General Error',
                headers: { 'Authorization': Vue.http.headers.common['Authorization'] },
                init: function() {
                    this.on('sending', function(file, xhr, formData) {
                        _.forEach(self.admin.editedEntry, (value, key) => {
                            formData.append(key, value);
                        });
                    });
                },
                parallelUploads: 1,
                paramName: this.file,
                previewTemplate: this.previewTemplate,
                showRemoveLink: this.showRemoveLink,
                url: this.path,
            };

            // Creates and stores dropzone instance
            this.dropZone = new Dropzone(this.$refs.dropzone, params);

            this.dropZone.on('queuecomplete', () => {
                this.dropZone.options.autoProcessQueue = false;

                eventBus.$emit('dropZoneQueueComplete', true);
            });

            // On file uploaded successfully
            this.dropZone.on('success', (file, response) => {
                // If there was a callback function passed as prop, call it
                if (typeof this.onSuccess === 'function') {
                    this.onSuccess(file, response);
                }

                eventBus.$emit('dropZoneUploadComplete', response);

                this.dropZone.options.autoProcessQueue = true;

                this.dropZone.removeFile(file);
            });


            // On file added
            this.dropZone.on('addedfile', (file) => {
                const self = this;
                /**
                 * Bind listner to remove file from preview when user acknowladges the error
                 * by clicking the x button next to the file preview
                 */
                $(file.previewElement).on('click', '.close', function() {
                    self.dropZone.removeFile(file);
                });
            });

            // On file upload error
            this.dropZone.on('error', (file, error) => {
                const self = this;

                this.dropZone.options.autoProcessQueue = false;

                this.dropZone.requeueFiles(file);

                store.dispatch(`${this.vuexModule}/errorCommit`, {
                    apiResponse: { data: error },
                    nameSpace: 'ADMIN',
                });

                /**
                 * Do some DOM manipulations on that file's preview element, this must be done with
                 * jQuery since Vue isn't aware of what's happening
                 */
                $(file.previewElement).find('.dz-error-message').text(i18n.t('assetsManager.uploadFailed'));

                $(file.previewElement).find('.progress-bar-success').addClass('progress-bar-danger').removeClass('progress-bar-success');

                /**
                 * Bind listner to remove file from preview when user acknowladges the error
                 * by clicking the x button next to the file preview
                 */
                $(file.previewElement).on('click', '.close', function() {
                    self.dropZone.removeFile(file);
                });
            });

        // When all files completed callback, not used at the moment, but might display some success message
        // this.dropZone.on('queuecomplete', () => {
        //     console.log('queuecomplete');
        // });
        });
    },
    beforeDestroy() {
        // If instance of dropZone exists disable it (no other destroy method in docs)
        if (this.dropZone) {
            this.dropZone.disable();
        }
    }
};
