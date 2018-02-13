import _ from 'lodash';

import { markdown } from 'markdown';

import md from './MenuEditorHelp.md';

import getSlug from 'speakingurl';

export default {
    /**
     * Set the component data
     *
     * @return  {object}
     */
    data () {
        return {
            helpPages: {},
        };
    },

    /**
     * Set the computed properties
     *
     * @type {object}
     */
    computed: {
        $helpHTML () {
            const htmlString = markdown.toHTML(md, 'Maruku');

            return $.parseHTML(htmlString);
        },

        /**
         * Return the help pages
         *
         * @return  {Array}
         */
        pages () {
            // There's no need to show different docs for update vs create
            // const moduleTableName = this.$route.params.moduleTableName;
            // const helpSection = moduleTableName ? 'edit' : 'create';

            const helpSection = 'menus';

            if (_.has(this.helpPages, helpSection)) {
                return this.helpPages[helpSection];
            }

            return false;
        },
    },

    /**
     * Set the methods
     *
     * @type  {Object}
     */
    methods: {
        createHelpPages () {
            let helpObject = {};

            let pageIndex = '';

            let pageSection = {};

            let isFirst = true;

            $(this.$helpHTML).each(function() {
                if ($(this).is('h1')) {
                    return;
                }

                if ($(this).is('h2')) {
                    if (!_.isEmpty(pageSection)) {

                        helpObject[pageIndex].push(pageSection);

                        pageSection = {};

                        isFirst = true;
                    }

                    pageIndex = $(this).find('code').text();

                    helpObject[pageIndex] = [];

                    return;
                }

                if ($(this).is('h3')) {
                    if (!_.isEmpty(pageSection)) {
                        helpObject[pageIndex].push(pageSection);

                        pageSection = {};
                    }

                    pageSection['title'] = $(this).text();

                    pageSection['slug'] = getSlug(pageSection['title']);

                    pageSection['isFirst'] = isFirst;

                    pageSection['content'] = '';

                    isFirst = false;

                    return;
                }

                if ($(this).prop('outerHTML') !== undefined) {
                    pageSection['content'] += $(this).prop('outerHTML');
                }
            });

            if (!_.isEmpty(pageSection)) {
                helpObject[pageIndex].push(pageSection);

                pageSection = {};
            }

            return helpObject;
        },
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted: function() {
        this.helpPages = this.createHelpPages();
    }
};
