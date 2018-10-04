import { mapGetters } from 'vuex';

import { ManyToOneField } from '_/config/fieldTypeComponents';

import { store } from '_/vuex/store';

export default {
    /**
     * Set the component data
     *
     * @return  {object}
     */
    data: function() {
        return {
            /**
             * Module field option config object
             *
             * @type  {string}
             */
            fieldOption: {
                disabled: false,
                fieldType: 'ManyToMany',
                id: 'related_module',
                label: 'Related Module',
                name: 'related_module',
                optionGroup: 0,
                optionsData: [{
                    id: 0,
                    text: 'Select module'
                }],
                preselectFirst: true,
                refreshFields: 0,
                required: true,
                tooltip: 'ID of a related module.',
                value: 0,
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
            admin: 'admin/admin',
            photonModules: 'photonModule/photonModules',
        }),
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        /**
         * Hydrates the field_groups module field with a module id
         *
         * @return  {void}
         */
        hydrateModuleField ({ value }) {
            let promisesArray = [];

            promisesArray.push(store.dispatch('admin/updateEntryField', {
                name: 'module_id',
                newValue: value > 0 ? value : null,
            }));

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
    mounted: function() {
        this.$nextTick(function() {
            this.fieldOption.optionsData = mapModulesSelect2(this.photonModules);

            this.fieldOption.refreshFields = moment().valueOf();
        });
    },
};

const mapModulesSelect2 = (modules) => {
    return [{
        id: 0,
        text: 'Select Module',
    }]
        .concat(modules.map((module) => {
            return {
                id: module.id,
                text: module.name
            };
        }));
};
