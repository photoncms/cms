// Imported from https://github.com/Coffcer/vue-bootstrap-modal
export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        // [Cancel button] className
        cancelClass: {
            type: String,
            default: 'btn btn-lg btn-danger'
        },
        // [Cancel button] text
        cancelText: {
            type: String,
            default: 'Cancel'
        },
        // automatically close when click [OK button]
        closeWhenOK: {
            type: Boolean,
            default: false
        },
        // if it set false, click background will close modal
        force: {
            type: Boolean,
            default: true
        },
        // Bootstrap full style modal
        full: {
            type: Boolean,
            default: false
        },
        // Bootstrap large style modal
        large: {
            type: Boolean,
            default: false
        },
        // [OK button] className
        okClass: {
            type: String,
            default: 'btn btn-lg btn-success'
        },
        // [OK button] text
        okText: {
            type: String,
            default: 'OK'
        },
        show: {
            type: Boolean,
            twoWay: true,
            default: false
        },
        // Bootstrap small style modal
        small: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            default: 'Modal'
        },
        // vue transition name
        transition: {
            type: String,
            default: 'modal'
        },
    },

    /**
     * Set the data property
     *
     * @return  {object}
     */
    data() {
        return {
            duration: null,
        };
    },

    /**
     * Set the computed properties
     *
     * @type  {object}
     */
    computed: {
        modalClass() {
            return {
                'modal-full': this.full,
                'modal-lg': this.large,
                'modal-sm': this.small,
            };
        }
    },

    /**
     * Define the created hook
     *
     * @return  {void}
     */
    created() {
        if (this.show) {
            document.body.className += ' modal-open';
        }
    },

    /**
     * Define the beforeDestrot hook
     *
     * @return  {void}
     */
    beforeDestroy() {
        document.body.className = document.body.className.replace(/\s?modal-open/, '');
    },

    /**
     * Define watched properties
     *
     * @type  {object}
     */
    watch: {
        show(value) {
            if (value) {
                document.body.className += ' modal-open';
            } else {
                if (!this.duration) {
                    this.duration = window.getComputedStyle(this.$el)['transition-duration'].replace('s', '') * 1000;
                }

                window.setTimeout(() => {
                    document.body.className = document.body.className.replace(/\s?modal-open/, '');
                }, this.duration || 0);
            }
        }
    },

    /**
     * Define the methods
     *
     * @type  {object}
     */
    methods: {
        /**
         * Upon clicking the OK button emits the ok event
         *
         * @return  {void}
         */
        ok() {
            this.$emit('ok');
            if (this.closeWhenOK) {
                this.show = false;
            }
        },

        /**
         * Upon clicking the Cancel button emits the cancel event
         *
         * @return  {void}
         */
        cancel() {
            this.$emit('cancel');
            this.show = false;
        },

        /**
         * Upon clicking outside of modal emits the cancel event
         *
         * @return  {void}
         */
        clickMask() {
            if (!this.force) {
                this.cancel();
            }
        },

        /**
         * Upon hitting the esc key emits the cancel event
         *
         * @return  {void}
         */
        escape() {
            if (!this.force) {
                this.cancel();
            }
        },
    }
};
