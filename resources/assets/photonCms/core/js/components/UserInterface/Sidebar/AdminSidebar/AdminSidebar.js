import _ from 'lodash';

import { mapGetters } from 'vuex';

import { store } from '_/vuex/store';

import { pWarn } from '_/helpers/logger';

import {
    destroyJsTree,
    jsTreeInstance,
    jsTreeReselectNode,
    refreshJsTree,
    setupJsTree,
} from '_/components/UserInterface/Sidebar/AdminSidebar/AdminSidebar.jsTree';

import InfiniteLoading from 'vue-infinite-loading';

import Vue from 'vue';

const QuickSearch = Vue.component(
        'QuickSearch',
        require('_/components/UserInterface/Sidebar/AdminSidebar/QuickSearch/QuickSearch.vue')
    );

const AdvancedSearch = Vue.component(
        'AdvancedSearch',
        require('_/components/UserInterface/Sidebar/AdminSidebar/AdvancedSearch/AdvancedSearch.vue')
    );

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        /**
         * An array of fields for the selected module
         */
        fields: {
            type: Array,
        },
    },

    /**
     * Set the component data
     *
     * @return  {object}
     */
    data: function () {
        return {
            /**
             * True if jstree initialization was successfully completed
             *
             * @type  {boolean}
             */
            jsTreeInitialized: false,

            /**
             * Slected module (as determined by the module name in the URL)
             *
             * @type  {Object}
             */
            selectedModule: {}
        };
    },

    /**
     * Setup Components
     *
     * @type  {Object}
     */
    components: {
        AdvancedSearch,
        InfiniteLoading,
        QuickSearch,
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        // Map getters
        ...mapGetters({
            admin: 'admin/admin',
            photonModule: 'photonModule/photonModule',
            ui: 'ui/ui',
        }),

        infiniteEnabled () {
            return (this.selectedModule.type === 'non_sortable'
                && this.admin.editorMode !== 'search'
                && this.jsTreeInitialized);
        },
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        getSelectedModule () {
            const moduleTableName = this.$route.params.moduleTableName;

            return _.find(this.photonModule.modules, { 'table_name': moduleTableName });
        },

        /**
         * Lazy loads nodes from the API
         *
         * @return  {void}
         */
        onInfinite () {
            if(this.selectedModule.type !== 'non_sortable') {
                return;
            }

            const tableName = this.selectedModule.table_name;

            const getEntries = store.dispatch('admin/loadPaginatedNodes', { tableName });

            getEntries.then((results) => {
                this.processEntries(results, tableName);

                this.$refs.infiniteLoading.$emit('$InfiniteLoading:loaded');
            }, (results) => {
                this.processEntries(results, tableName);

                if (!_.isEmpty(results)) {
                    this.$refs.infiniteLoading.$emit('$InfiniteLoading:loaded');
                }

                this.$refs.infiniteLoading.$emit('$InfiniteLoading:complete');
            });
        },

        generateIconHTML(iconName) {
            return `<i class="${iconName} photonTreeIcon"></i>`;
        },

        processEntries (results, tableName) {
            const self = this;

            if(!_.isEmpty(results)) {
                results.map((node) => {
                    const anchor = this.generateIconHTML(this.selectedModule.icon) + node.anchor_text;

                    const newNode = {
                        a_attr: {
                            class: 'anchor-html',
                        },
                        li_attr: {
                            class: 'anchor-html',
                        },
                        children: false,
                        icon: false,
                        id: `${tableName}.${node.id}`,
                        originalId: node.id,
                        tableName: tableName,
                        scopeId: node.scope_id,
                        text: _.isEmpty(node.anchor_html) ? anchor : node.anchor_html,
                        type: tableName,
                    };

                    const count = jsTreeInstance.get_json('#', { 'flat' : true }).length;

                    if (count == 0) {
                        // Added a pause of 250 as Firefox has an issue of
                        // occasionally overwriting nodes only on a first processEntries run.
                        setTimeout(()=>{
                            jsTreeInstance.create_node('#',  newNode, 'last');

                            if(_.has(self.admin.editedEntry, 'id')
                                && parseInt(node.id) === parseInt(self.admin.editedEntry.id)) {
                                jsTreeInstance.select_node(`${tableName}.${node.id}`, true);
                            }
                        }, 250);
                    } else {
                        jsTreeInstance.create_node('#',  newNode, 'last');

                        if(_.has(self.admin.editedEntry, 'id')
                            && parseInt(node.id) === parseInt(self.admin.editedEntry.id)) {
                            jsTreeInstance.select_node(`${tableName}.${node.id}`, true);
                        }
                    }
                });
            }
        }
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted: function() {
        this.$nextTick(function() {
            this.selectedModule = this.getSelectedModule();

            if (this.admin.editorMode !== 'search') {
                store.dispatch('admin/emptyLazyLoadedNodes');

                setupJsTree(this);
            }
        });
    },

    /**
     * Set the beforeDestroy hook
     *
     * @return  {void}
     */
    beforeDestroy: function() {
        destroyJsTree();
    },

    /**
     * Set the watched properties
     *
     * @type  {Object}
     */
    watch: {
        $route (newEntry, oldEntry) {
            if (newEntry.params.moduleEntryId === 'search') {
                store.dispatch('admin/emptyLazyLoadedNodes');

                return;
            }

            pWarn(
                'Watched property fired.',
                'AdminSidebar',
                '$route',
                newEntry,
                oldEntry,
                this
            );

            const moduleTableName = newEntry.params.moduleTableName;

            const oldModuleTableName = oldEntry.params.moduleTableName;

            const moduleEntryId = !isNaN(newEntry.params.moduleEntryId) ? newEntry.params.moduleEntryId : null;

            const oldModuleEntryId = !isNaN(oldEntry.params.moduleEntryId) ? oldEntry.params.moduleEntryId : null;

            this.selectedModule = this.getSelectedModule();

            // If module has changed
            if (moduleTableName !== oldModuleTableName) {
                pWarn('AdminSidebar.js', 'Module Changed');

                store.dispatch('admin/emptyLazyLoadedNodes');

                destroyJsTree();

                setupJsTree(this);

                // if(_.has(this.$refs, 'infiniteLoading.$emit')) {
                this.$refs.infiniteLoading.$emit('$InfiniteLoading:reset');
                // }

                return;
            }

            // If entry id has changed
            if (moduleEntryId !== oldModuleEntryId && this.selectedModule.type !== 'multilevel_sortable') {
                pWarn('AdminSidebar.js', 'Entry Changed');

                jsTreeReselectNode(moduleEntryId);

                return;
            }

            pWarn('AdminSidebar.js', 'Normal Refresh');

            // Else refresh normally
            refreshJsTree();
        },

        'admin.submitInProgress' (newEntry, oldEntry) {
            if(this.admin.selectedModule.type === 'single_entry') {
                return;
            }

            if (newEntry === false) {
                pWarn(
                    'Watched property fired.',
                    'AdminSidebar',
                    'admin.submitInProgress',
                    newEntry,
                    oldEntry,
                    this
                );

                store.dispatch('admin/emptyLazyLoadedNodes');

                destroyJsTree();

                setupJsTree(this);

                this.$refs.infiniteLoading.$emit('$InfiniteLoading:reset');
            }
        },
    },
};
