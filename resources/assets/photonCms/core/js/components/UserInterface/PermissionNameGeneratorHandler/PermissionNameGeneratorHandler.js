import { store } from '_/vuex/store';

import Vue from 'vue';

import { mapGetters } from 'vuex';

export const PermissionNameGenerator = Vue.component(
        'PermissionNameGenerator',
        require('_/components/UserInterface/PermissionNameGenerator/PermissionNameGenerator.vue')
    );

export default {
    /**
     * Set the component data
     *
     * @return  {object}
     */
    data () {
        return {
            showPermissionGenerator: true,
        };
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        ...mapGetters({
            admin: 'admin/admin',
        })
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        /**
         * Sets the permissions generator visibility
         *
         * @param  {boolean}  options.value
         */
        setPermissionGeneratorVisibility ({ visible }) {
            this.showPermissionGenerator = visible;
        },

        /**
         * Updates the permission data in admin model
         *
         * @param   {string}  options.permissionName
         * @param   {string}  options.permissionTitle
         * @return  {void}
         */
        updatePermissionModelData ({ permissionName, permissionTitle }) {
            let promisesArray = [];

            promisesArray.push(store.dispatch('admin/updateEntryField', { name: 'name', newValue: permissionName }));

            promisesArray.push(store.dispatch('admin/updateEntryField', { name: 'title', newValue: permissionTitle }));

            Promise.all(promisesArray)
                .then(() => {
                    store.dispatch('admin/toggleEntryUpdated');
                });
        },
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(function() {
            this.showPermissionGenerator = this.admin.editorMode === 'create';
        });
    },

    /**
     * Define watched properties
     *
     * @type  {Object}
     */
    watch: {
        $route () {
            this.showPermissionGenerator = this.admin.editorMode === 'create';
        },
    },
};
