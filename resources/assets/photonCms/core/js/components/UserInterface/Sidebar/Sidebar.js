import {
    mapActions,
    mapGetters,
} from 'vuex';

import * as sidebarComponents from '@/sidebarComponents';

import * as customSidebarComponents from '~/config/sidebarComponents';

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
     * Set the computed variables
     *
     * @type  {object}
     */
    computed: mapGetters({
        sidebar: 'sidebar/sidebar',
        ui: 'ui/ui',
    }),

    /**
     * Set the components
     *
     * @type  {object}
     */
    components: {
        ...sidebarComponents,
        ...customSidebarComponents,
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        resizable () {
            if ($(this.$el).hasClass('ui-resizable')) {
                $(this.$el).resizable('destroy');
            }

            const sidebarWidthGroup = this.$route.meta.sidebarWidthGroup
                ? this.$route.meta.sidebarWidthGroup
                : 'default';

            const cookieName = sidebarWidthGroup == 'admin'
                ? `${sidebarWidthGroup}-${this.$route.params.moduleTableName}`
                : sidebarWidthGroup;

            const width = $.cookie(cookieName);

            if(width != null) {
                $(this.$el).css('width', width + 'px');
            }

            const handle = `
                <div class="sidebar-handle ui-resizable-handle ui-resizable-e">
                    <i class="fa fa-ellipsis-h"></i>
                    <i class="fa fa-ellipsis-v"></i>
                </div>`;

            $(this.$el).append(handle);

            $(this.$el).resizable({
                distance: 0,
                handles: {
                    e: '.sidebar-handle'
                },
                stop: function(event, ui) {
                    $.cookie(cookieName, ui.size.width);
                },
            });
        }
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted () {
        this.$nextTick(function() {
            this.resizable();
        });
    },

    /**
     * Define watched properties
     *
     * @type  {Object}
     */
    watch: {
        /**
         * Watching the route change
         *
         * @return  {void}
         */
        $route (newEntry, oldEntry) {
            if(newEntry.meta.sidebarWidthGroup == 'admin') {
                if (newEntry.params.moduleTableName != oldEntry.params.moduleTableName) {
                    this.resizable();
                }
            }
        }
    },
};
