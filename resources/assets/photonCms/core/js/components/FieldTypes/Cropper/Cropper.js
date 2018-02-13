export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        /**
         * The desired height of the resized image
         *
         * @type  {Object}
         */
        fileUrl: {
            required: true,
            type: String,
        },

        /**
         * Defines whether height should be fixed or not
         *
         * @type  {Object}
         */
        lockHeight: {
            required: true,
            type: Boolean,
        },

        /**
         * Defines whether width should be fixed or not
         *
         * @type  {Object}
         */
        lockWidth: {
            required: true,
            type: Boolean,
        },

        /**
         * A watched parameter used to reinstantiate the plugin as needed
         *
         * @type  {Object}
         */
        refresh: {
            type: Boolean,
        },

        /**
         * A resized image id. Used as a watched parameter to be able to reinstantiate the ImgAreaSelect plugin.
         *
         * @type  {Object}
         */
        resizedImageId: {
            required: true,
            type: Number,
        },

        /**
         * The height of the predefined selection. Used to calculate the bottom Y coordinate
         *
         * @type  {Object}
         */
        selectionHeight: {
            type: Number,
        },

        /**
         * The width of the predefined selection. Used to calculate the bottom X coordinate
         *
         * @type  {Object}
         */
        selectionWidth: {
            type: Number,
        },

        /**
         * The top X coordinate of the predefined selection area
         *
         * @type  {Object}
         */
        topX: {
            type: Number,
        },

        /**
         * The top Y coordinate of the predefined selection area
         *
         * @type  {Object}
         */
        topY: {
            type: Number,
        },
    },

    computed: {
        preventVerticalResize: function() {
            if (this.lockHeight && this.lockWidth) {
                return false;
            }

            if (this.lockHeight) {
                return true;
            }

            return false;
        },

        preventHorizontalResize: function() {
            if (this.lockHeight && this.lockWidth) {
                return false;
            }

            if (this.lockWidth) {
                return true;
            }

            return false;
        },
    },

    methods: {
        /**
         * Initializes the bootstrapSwitch plug-in
         *
         * @return  {void}
         */
        initializeCropper: function() {
            const self = this;

            // Get select element
            const $image = $(this.$el).find('img');

            const options = {
                checkCrossOrigin: false,
                viewMode: 2,
                checkOrientation: false,
                highlight: false,
                movable: false,
                rotatable: false,
                scalable: false,
                zoomable: false,
                zoomOnTouch: false,
                zoomOnWheel: false,
                crop: this.onSelectEnd,
                autoCropArea: 0,
                cropmove: function cropmove (event) {
                    // Don't allow new selection start
                    if (event.action === 'crop') {
                        event.preventDefault();
                    }

                    // Commented out as no constraint in px should be ever imposed.
                    // API methods fixed to be inline with this decision.

                    // const horizontalResizeConstraints = [
                    //     'e',
                    //     'ne',
                    //     'nw',
                    //     'se',
                    //     'sw',
                    //     'w'
                    // ];

                    // const verticalResizeConstraints = [
                    //     'n',
                    //     'ne',
                    //     'nw',
                    //     's',
                    //     'se',
                    //     'sw',
                    // ];

                    // if (self.preventHorizontalResize && horizontalResizeConstraints.indexOf(event.action) > -1) {
                    //     event.preventDefault();
                    // }

                    // if (self.preventVerticalResize && verticalResizeConstraints.indexOf(event.action) > -1) {
                    //     event.preventDefault();
                    // }

                },
                cropend: function cropend () {
                    const canvasData = $image.cropper('getCanvasData');

                    const scalingFactor = (canvasData.naturalWidth > canvasData.width)
                        ? canvasData.width / canvasData.naturalWidth
                        : 1;

                    const cropBoxData = $image.cropper('getCropBoxData');

                    const selection = {
                        id: self.resizedImageId,
                        topX: Math.round((cropBoxData.left - canvasData.left) / scalingFactor),
                        topY: Math.round((cropBoxData.top - canvasData.top) / scalingFactor),
                        width: Math.round(cropBoxData.width / scalingFactor),
                        height: Math.round(cropBoxData.height / scalingFactor),
                    };

                    self.$emit('change', selection);
                },
                ready: function setCropBoxData () {
                    const canvasData = $image.cropper('getCanvasData');

                    const scalingFactor = (canvasData.naturalWidth > canvasData.width)
                        ? canvasData.width / canvasData.naturalWidth
                        : 1;

                    const cropSettings = {
                        left: self.topX * scalingFactor + canvasData.left,
                        top: self.topY * scalingFactor + canvasData.top,
                        width: self.selectionWidth * scalingFactor,
                        height: self.selectionHeight * scalingFactor,
                    };

                    if (self.lockHeight && self.lockWidth) {
                        $image.cropper('setAspectRatio', cropSettings.width / cropSettings.height);
                    }

                    $image.cropper('setCropBoxData', cropSettings);

                    if (self.maxHeight) {
                        options.minCropBoxHeight = cropSettings.height;
                    }

                    if (self.maxWidth) {
                        options.minCropBoxWidth = cropSettings.width;
                    }
                },
            };

            $image.cropper(options);
        },

        reinstantiatePlugin: function() {
            $(this.$el).find('img').cropper('destroy');

            this.initializeCropper();
        }
    },

    mounted: function() {
        this.$nextTick(function() {
            this.initializeCropper();
        });
    },

    beforeDestroy: function() {
        $(this.$el).find('img').cropper('destroy');
    },

    watch: {
        'resizedImageId'(newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                this.reinstantiatePlugin();
            }
        },

        'refresh'(newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                this.reinstantiatePlugin();
            }
        },
    },
};
