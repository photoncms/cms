import getSlug from 'speakingurl';

import { router } from '_/router/router';

import Vue from 'vue';

import {
    Assets,
    Breadcrumb,
    EntryForm,
    HelpBlock,
    InfoPanel,
    Instructions,
    LicenseExpiringNotification,
    MainMenu,
    Sidebar,
    TitleBlock,
    UserMenu,
} from '@/components';

import { getEntryFields } from '_/components/MenuEditor/MenuEditor.fields';

import {
    pError,
    pWarn,
} from '_/helpers/logger';

import { store } from '_/vuex/store';

import {
    mapActions,
    mapGetters,
} from 'vuex';

import { userHasRole } from '_/vuex/actions/userActions';

const MenuEditorHelpBlock = Vue.component(
        'MenuEditorHelpBlock',
        require('_/components/MenuEditor/MenuEditorHelpBlock/MenuEditorHelpBlock.vue')
    );

export default {
    /**
     * Set the component data
     *
     * @return  {object}
     */
    data: function () {
        return {
            /**
             * Should automatic slug generation happen or not
             *
             * @type  {boolean}
             */
            disableAutomaticSlugGeneration: false,

            /**
             * Stores the entry fields configuration object
             *
             * @type  {Array}
             */
            fields: [],

            /**
             * An object sent to child components; used to signal that the fields need to be reset, with the ability
             * to limit the reset to only certain fields
             *
             * @type  {Object}
             */
            formFieldsReset: {
                /**
                 * A list of field names to include in a reset action
                 *
                 * @type  {Array}
                 */
                includeFields: [],

                /**
                 * A parameter that is listened by the child components. Changed via moment().valueOf() method to always
                 * set the fresh value.
                 *
                 * @type  {[type]}
                 */
                resetData: null,
            },
        };
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            menuEditor: 'menuEditor/menuEditor',
            ui: 'ui/ui',
        })
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        ...mapActions('ui', [
            'animateWrapper',
            'setBodyClass',
        ]),

        ...mapActions('sidebar', [
            'setSidebarType',
        ]),

        /**
         * Toggles the formFieldsReset data property in order to force refresh of the EntryForm component
         *
         * @param   {object}  options.includeFields The names of field components that need to be included in reset
         * @return  {void}
         */
        resetFormFields ({ includeFields = null } = {}) {
            this.formFieldsReset.resetData = moment().valueOf();

            this.formFieldsReset.includeFields = [];

            if(includeFields) {
                this.formFieldsReset.includeFields = includeFields;
            }
        },
    },

    /**
     * Set the beforeRouteEnter hook
     *
     * @return  {void}
     */
    beforeRouteEnter (to, from, next) {
        const menuId = to.params.menuId;

        const bootstrap = store.dispatch('menuEditor/bootstrap', { menuId });

        bootstrap.then((success) => {
            if (success) {
                next();
            }
        });
    },

    /**
     * Set the components
     *
     * @type  {Object}
     */
    components: {
        Assets,
        Breadcrumb,
        EntryForm,
        HelpBlock,
        InfoPanel,
        Instructions,
        LicenseExpiringNotification,
        MainMenu,
        MenuEditorHelpBlock,
        Sidebar,
        TitleBlock,
        UserMenu,
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        if(!userHasRole('super_administrator')) {
            router.push('/error/resource-not-found');
        }

        this.fields = getEntryFields(this.menuEditor);

        this.$nextTick(function () {
            this.setBodyClass('generator');

            this.setSidebarType('MenuEditorSidebar');

            this.animateWrapper();
        });
    },

    /**
     * Set the beforeDestroy hook
     *
     * @return  {void}
     */
    beforeDestroy () {
        this.setSidebarType(null);
    },

    /**
     * Set the watched properties
     *
     * @type  {Object}
     */
    watch: {
        /**
         * Watching the route change
         *
         * @return  {void}
         */
        $route: {
            deep: true,
            handler (newEntry, oldEntry) {
                pWarn(
                    'Watched property fired.',
                    'MenuEditor',
                    '$route',
                    newEntry,
                    oldEntry,
                    this
                );

                this.disableAutomaticSlugGeneration = false;

                const menuId = this.$route.params.menuId;

                const bootstrap = store.dispatch('menuEditor/bootstrap', { menuId });

                bootstrap.then((success) => {
                    if (success) {
                        this.fields = getEntryFields(this.menuEditor);

                        this.resetFormFields();
                    }
                })
                .catch((error) => {
                    pError('menuEditor/bootstrap action failed with an error', error);

                    router.push('/error/resource-not-found');
                });
            },
        },

        'menuEditor.entryUpdated' (newEntry, oldEntry) {
            pWarn(
                'Watched property fired.',
                'MenuEditor',
                'menuEditor.entryUpdated',
                newEntry,
                oldEntry,
                this
            );

            this.fields = getEntryFields(this.menuEditor);

            this.resetFormFields();
        },

        'menuEditor.editedEntry.title' (newValue) {
            if(this.menuEditor.editorMode !== 'create' || this.disableAutomaticSlugGeneration) {
                return false;
            }

            const slug = getSlug(newValue);

            store.dispatch('menuEditor/updateMenuField', { name: 'name', newValue: slug });

            this.fields = getEntryFields(this.menuEditor);

            this.resetFormFields({ includeFields: ['name']});
        },

        'menuEditor.editedEntry.name' (newValue) {
            if(this.menuEditor.editorMode !== 'create'
                || this.disableAutomaticSlugGeneration
                || !newValue) {
                return false;
            }

            const slug = getSlug(this.menuEditor.editedEntry.title);

            if(newValue !== slug) {
                this.disableAutomaticSlugGeneration = true;
            }
        },

        'menuItemsEditor.submitInProgress' (newEntry) {
            if (newEntry === false) {
                this.disableAutomaticSlugGeneration = false;
            }
        },
    }
};
