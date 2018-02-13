import getSlug from 'speakingurl';

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

import _ from 'lodash';

import { getEntryFields } from '_/components/MenuItemsEditor/MenuItemsEditor.fields';

import { refreshJsTree } from '_/components/UserInterface/Sidebar/MenuItemsEditorSidebar/MenuItemsEditorSidebar.jsTree';

import { userHasRole } from '_/vuex/actions/userActions';

import {
    pError,
    pWarn,
} from '_/helpers/logger';

import { router } from '_/router/router';

import { store } from '_/vuex/store';

import {
    mapActions,
    mapGetters,
} from 'vuex';

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
             * A reference to refreshJsTree method
             */
            refreshJsTree,

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
            menuItemsEditor: 'menuItemsEditor/menuItemsEditor',
            photonModule: 'photonModule/photonModule',
            ui: 'ui/ui',
        }),

        /**
         * Gets the menuLinkType id
         *
         * @return  {integer}
         */
        menuLinkTypeId () {
            const menuLinkTypeId = this.menuItemsEditor.editedEntry.menu_link_type_id;

            if(_.isObject(menuLinkTypeId)) {
                return parseInt(menuLinkTypeId.id);
            }

            return parseInt(menuLinkTypeId);
        },

        /**
         * Manages the disable state of the adminEntry field
         *
         * @return  {boolean}
         */
        adminEntryDisabled () {
            // Disable the field for any menu_link_type_id other than 'Admin Panel Module' link
            if (this.menuLinkTypeId !== 4) {
                return true;
            }

            if (!this.menuItemsEditor.editedEntry.adminModule) {
                return true;
            }

            return false;
        },

        /**
         * Manages the disable state of the icon field
         *
         * @return  {boolean}
         */
        iconDisabled () {
            /**
             * Disable the field for menu_link_type_id 'Admin Panel Module' and 'Admin Panel Single Entry' options
             */
            if ([2, 3].indexOf(this.menuLinkTypeId) === -1) {
                return true;
            }

            return false;
        },

        /**
         * Manages the disable state of the adminModule field
         *
         * @return  {boolean}
         */
        adminModuleDisabled () {
            /**
             * Disable the field for any menu_link_type_id other than 'Admin Panel Module'
             * and 'Admin Panel Single Entry' links
             */
            if ([1, 4].indexOf(this.menuLinkTypeId) === -1) {
                return true;
            }

            return false;
        },

        /**
         * Manages the disable state of the staticLink field
         *
         * @return  {boolean}
         */

        staticLinkDisabled () {
            // Disable the field for any menu_link_type_id other than 'Static Link' link
            if (this.menuLinkTypeId !== 2) {
                return true;
            }

            return false;
        },

        /**
         * Manages the disable state of the slug field
         *
         * @return  {boolean}
         */
        slugDisabled () {
            // Disable the field only for menu_link_type_id 'Menu Item Group'
            if (this.menuLinkTypeId === 3) {
                return true;
            }

            return false;
        }
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
         * Sets the formFieldsReset data property in order to force refresh of the EntryForm component
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

        const menuItemId = to.params.menuItemId;

        const bootstrap = store.dispatch('menuItemsEditor/bootstrap', { menuId, menuItemId });

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
        this.$nextTick(function () {
            if(!userHasRole('super_administrator')) {
                router.push('/error/resource-not-found');
            }

            this.setBodyClass('generator');

            this.setSidebarType('MenuItemsEditorSidebar');

            this.animateWrapper();

            this.fields = getEntryFields(this);
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
                    'MenuItemsEditor',
                    '$route',
                    newEntry,
                    oldEntry,
                    this
                );

                this.disableAutomaticSlugGeneration = false;

                const menuId = this.$route.params.menuId;

                const menuItemId = this.$route.params.menuItemId;

                const bootstrap = store.dispatch('menuItemsEditor/bootstrap', { menuId, menuItemId });

                bootstrap.then((success) => {
                    if (success) {
                        this.fields = getEntryFields(this);

                        this.resetFormFields();
                    }
                })
                .catch((error) => {
                    pError('menuItemsEditor/bootstrap action failed with an error', error);

                    router.push('/error/resource-not-found');
                });
            },
        },

        'menuItemsEditor.entryUpdated' () {
            this.fields = getEntryFields(this);

            this.resetFormFields();
        },

        'menuItemsEditor.editedEntry.menu_link_type_id' (newEntry, oldEntry) {
            if(newEntry !== oldEntry && !this.menuItemsEditor.bootstrapInProgress) {
                pWarn(
                    'Watched property fired.',
                    'MenuItemsEditor',
                    'menuItemsEditor.editedEntry.menu_link_type_id',
                    newEntry,
                    oldEntry,
                    this
                );

                this.fields = getEntryFields(this);

                this.resetFormFields({ includeFields: ['adminEntry', 'adminModule']});
            }
        },

        'menuItemsEditor.editedEntry.adminModule' (newEntry, oldEntry) {
            oldEntry = _.isObject(oldEntry) ? oldEntry.id : oldEntry;

            if(parseInt(newEntry) !== parseInt(oldEntry)
                && !_.isEmpty(newEntry)
                && !this.menuItemsEditor.bootstrapInProgress) {

                if(this.menuItemsEditor.editorMode === 'edit' && this.menuLinkTypeId !== 4) {
                    return;
                }

                pWarn(
                    'Watched property fired.',
                    'MenuItemsEditor',
                    'menuItemsEditor.editedEntry.adminModule',
                    newEntry,
                    oldEntry,
                    this
                );

                if (!this.menuItemsEditor.submitInProgress) {
                    store.dispatch('menuItemsEditor/unsetAdminEntryField');
                }

                this.fields = getEntryFields(this);

                this.resetFormFields({ includeFields: ['adminEntry']});
            }
        },

        'menuItemsEditor.editedEntry.title' (newValue) {
            if(this.menuItemsEditor.editorMode !== 'create' || this.disableAutomaticSlugGeneration) {
                return false;
            }

            const slug = getSlug(newValue);

            store.dispatch('menuItemsEditor/updateMenuField', { name: 'slug', newValue: slug });

            this.fields = getEntryFields(this);

            this.resetFormFields({ includeFields: ['slug']});
        },

        'menuItemsEditor.editedEntry.slug' (newValue) {
            if(this.menuItemsEditor.editorMode !== 'create'
                || this.disableAutomaticSlugGeneration
                || !newValue) {
                return false;
            }

            const slug = getSlug(this.menuItemsEditor.editedEntry.title);

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
