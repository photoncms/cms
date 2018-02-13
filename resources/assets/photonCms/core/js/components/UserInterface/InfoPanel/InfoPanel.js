import _ from 'lodash';

import getSlug from 'speakingurl';

import Vue from 'vue';

import {
    mapActions,
    mapGetters
} from 'vuex';

import { store } from '_/vuex/store';

const InputTextField = Vue.component(
        'InputTextField',
        require('_/components/FieldTypes/InputTag/InputTag.vue')
    );

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        vuexModule: {
            required: true,
            type: String,
        },
    },

    /**
     * Set the components
     *
     * @type  {Object}
     */
    components: {
        InputTextField,
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            photonModule: 'photonModule/photonModule',
            subscription: 'subscription/subscription',
            ui: 'ui/ui',
        }),

        /**
         * Create the admin getter based on the vuexModule prop
         *
         * @return  {object}
         */
        admin () {
            const getterName = `${this.vuexModule}/${this.vuexModule}`;

            return this.$store.getters[getterName];
        },

        /**
         * Date format getter
         *
         * @return  {string}
         */
        dateFormat () {
            return this.ui.photonConfig.dateFormat;
        },

        /**
         * Created at getter
         *
         * @return  {string}
         */
        createdAt () {
            return this.admin.editedEntry.created_at
                ? moment(
                    this.admin.editedEntry.created_at
                ).format(this.dateFormat)
                : null;
        },

        /**
         * Updated at getter
         *
         * @return  {string}
         */
        updatedAt () {
            return this.admin.editedEntry.updated_at
                ? moment(
                    this.admin.editedEntry.updated_at
                ).format(this.dateFormat)
                : null;
        },

        /**
         * Module type getter
         *
         * @return  {string}
         */
        moduleType: function() {
            return this.ui.photonConfig.moduleTypes[store.state.admin.selectedModule.type]
                ? this.ui.photonConfig.moduleTypes[store.state.admin.selectedModule.type]
                : 'System module (Type not set)';
        },

        /**
         * Parent entry getter
         *
         * @return  {string}
         */
        parentEntry: function() {
            if(this.admin.editorMode === 'create') {
                return !_.isEmpty(this.admin.selectedNode) ? this.admin.selectedNode : null;
            }

            return null;
        },

        /**
         * Checks if the entry is a root prent entry
         *
         * @return  {Boolean}
         */
        isRootParentEntry: function() {
            if(this.admin.selectedNode.url === null) {
                return true;
            }

            return false;
        },

        /**
         * Returns the parent module
         *
         * @return  {object}
         */
        parentModule: function() {
            if (this.admin.selectedModule.category) {
                const parentModule = _.find(
                    this.photonModule.modules,
                    { 'id': this.admin.selectedModule.category }
                );

                return parentModule;
            }

            return null;
        },

        /**
         * Returns slug error, if exists
         *
         * @return  {string}
         */
        slugError: function() {
            return _.has(this.admin.error.fields, 'slug') ? this.admin.error.fields.slug.message : null;
        },
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('ui', [
            'triggerCreateMode',
        ]),

        ...mapActions('admin', [
            'disableAutomaticSlugGeneration',
            'updateEntryField',
        ]),

        /**
         * Fired on slug input tag change
         *
         * @param   {string}  options.value
         * @return  {void}
         */
        onChange ({ value }) {
            store.dispatch(`${this.vuexModule}/updateEntryField`, { name: 'slug', newValue: value });

            if(this.admin.editorMode !== 'create'
                || this.vuexModule !== 'admin'
                || this.admin.disableAutomaticSlugGeneration) {
                return false;
            }

            const slug = this.slugify(this.admin.editedEntry);

            if(value !== slug) {
                store.dispatch(`${this.vuexModule}/disableAutomaticSlugGeneration`, { value: true });
            }
        },

        /**
         * Triggers the create mode
         *
         * @return  {void}
         */
        createNewEntry () {
            this.triggerCreateMode();
        },

        /**
         * Creates the slug version of the string template
         *
         * @param   {object}  editedEntry
         * @return  {string}
         */
        slugify (editedEntry) {
            if (!this.admin.selectedModule.slug) {
                return false;
            }

            _.templateSettings.interpolate = /{{([\s\S]+?)}}/g;

            const selectedModuleSlug = this.admin.selectedModule.slug;

            let slugTemplate = selectedModuleSlug.replace(/{{/g, '{{data.');

            let compiled = _.template(slugTemplate);

            /**
             * Instead of directly passing the editedEntry object, we're wrapping it in a data object to
             * avoid the issue of lodash _.template complaining about the template variable not being set
             */
            let slugCandidate = compiled({ data: { ...editedEntry }});

            return getSlug(slugCandidate);
        },
    },

    /**
     * Set the watched properties
     *
     * @type  {Object}
     */
    watch: {
        'admin.editedEntry': {
            deep: true,
            handler (newValue) {
                if(this.admin.editorMode !== 'create'
                    || this.vuexModule !== 'admin'
                    || this.admin.disableAutomaticSlugGeneration) {
                    return false;
                }

                const slug = this.slugify (newValue);

                store.dispatch(`${this.vuexModule}/updateEntryField`, { name: 'slug', newValue: slug });
            },
        },
    },
};
