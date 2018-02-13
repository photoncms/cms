import _ from 'lodash';

import { config } from '_/config/config';

import { store } from '_/vuex/store';

import { eventBus } from '_/helpers/eventBus';

import i18n from '_/i18n';

import {
    pError,
    pLog,
    pWarn,
} from '_/helpers/logger';

import { api } from '_/services/api';

import { storage } from '_/services/storage';

const photonConfig = require('~/config/config.json');

export default {
    /**
     * Define props
     *
     * @type  {Object}
     */
    props: {
        canCreateSearchChoice: {
            type: Boolean,
        },
        disabled: {
            type: Boolean,
        },
        flattenToOptgroups: {
            type: Boolean,
        },
        id: {
            required: true,
            type: [
                Number,
                String,
            ],
        },
        isActiveEntryFilter: {
            default: null,
            type: String,
        },
        lazyLoading: {
            type: Boolean,
        },
        multiple: {
            type: Boolean,
        },
        name: {
            required: true,
            type: String,
        },
        optionsData: {
            type: Array,
        },
        placeholder: {
            type: String,
        },
        preloadDataConfig: {
            default () {
                return {
                    method: 'post',
                    payload: {
                        include_relations: false,
                    },
                    resultsObjectPath: 'body.body.entries',
                    valuesOfInterest: {
                        id: 'id',
                        text: 'anchor_text',
                    },
                    url: false,
                };
            },
            type: Object
        },
        preselectFirst: {
            default: false,
            type: Boolean,
        },
        readonly: {
            type: Boolean,
        },
        refreshFields: {
            type: Number,
        },
        relatedTableName: {
            type: [
                Boolean,
                String,
            ],
        },
        required: {
            type: Boolean,
        },
        tabindex: {
            type: Number,
        },
        value: {
            default: null,
            type: [
                Array,
                Number,
                Object,
                String,
            ],
        }
    },

    /**
     * Set the component data
     *
     * @type  {Object}
     */
    data: function() {
        return {
            /**
             * Name of the field used as a adefault search choice
             *
             * @type  {string}
             */
            defaultSearchChoice: null,

            /**
             * Select2 plugin initialization options
             *
             * @type  {Object}
             */
            options: {},
        };
    },

    computed: {
        /**
         * Makes sure that value returned is always an array of values mapped
         * by the preloadDataConfig.valuesOfInterest.id parameter
         *
         * @return  {array}
         */
        values: function() {
            if(!this.value) {
                return this.value;
            }

            if (Array.isArray(this.value)) {
                const onlyIdsOfInterest = this.value.map((value) => {
                    if(_.isObject(value)) {
                        return [_.get(value, this.preloadDataConfig.valuesOfInterest.id)];
                    }

                    return [value];
                });

                return onlyIdsOfInterest;
            }

            if(_.isObject(this.value)) {
                return [_.get(this.value, this.preloadDataConfig.valuesOfInterest.id)];
            }

            return [this.value];
        }
    },

    methods: {
        /**
         * Creates the event payload. The payload is to be emmited on change event.
         *
         * @param   {object}  event
         * @return  {object}
         */
        createEventPayload (event) {
            let selection = this.getSelectedOptions(event);

            if(!this.multiple && !_.isEmpty(selection)) {
                selection = selection.shift();
            }

            return {
                event,
                id: this.id,
                name: this.name,
                value: selection,
            };
        },

        /**
         * Flattens the entry item that has a parent Id
         *
         * @param   {object}  entry
         * @param   {object}  entries
         * @param   {string}  path
         * @param   {string}  name
         * @return  {object}
         */
        flattenSingleEntry (entry, entries, path = null, name = null) {
            const parentId = entry.parent_id ? entry.parent_id : null;

            if (parentId) {
                path = path ? `${parentId}.${path}` : parentId;

                name = name ? `${entry.anchor_text} > ${name}` : entry.anchor_text;

                const parentEntry = _.find(entries, { id: parentId });

                return this.flattenSingleEntry(parentEntry, entries, path, name);
            }

            return {
                parentId: entry.id,
                text: `${entry.anchor_text} > ${name}`,
            };
        },

        /**
         * Flattens the multilevel sortable results to optiongroups with options
         *
         * @param   {object}  entries
         * @return  {object}
         */
        flattenToOptionGroups (entries) {
            let deepObject = {};

            entries.map(entry => {
                const parentId = entry.parent_id ? entry.parent_id : null;

                let path = '';

                if (!parentId) {
                    path = `${entry.id}.text`;

                    _.set(deepObject, path, entry.anchor_text);

                    return;
                }

                const flattenedEntry = this.flattenSingleEntry(entry, entries, 'children');

                if (!_.get(deepObject, `${flattenedEntry.parentId}.children`)) {
                    _.set(deepObject, `${flattenedEntry.parentId}.children`, []);
                }

                const child = {
                    id: entry.id,
                    text: flattenedEntry.text,
                };

                deepObject[flattenedEntry.parentId].children.push(child);
            });

            return _.values(deepObject);
        },

        /**
         * Flattens the multilevel sortable results
         *
         * @param   {object}  entries
         * @return  {object}
         */
        flattenOptions (entries, vueObject) {
            let deepObject = [];

            entries.map(entry => {
                const parentId = entry.parent_id ? entry.parent_id : null;

                if (!parentId) {
                    deepObject.push({
                        categoryId: entry[vueObject.preloadDataConfig.valuesOfInterest.id],
                        id: entry[vueObject.preloadDataConfig.valuesOfInterest.id],
                        text: entry[vueObject.preloadDataConfig.valuesOfInterest.text],
                    });

                    return;
                }

                const flattenedEntry = this.flattenSingleEntry(entry, entries, 'children');

                if (!_.get(deepObject, `${flattenedEntry.parentId}.children`)) {
                    // _.set(deepObject, `${flattenedEntry.parentId}.children`, []);
                }

                const spliceIndex = _.findLastIndex(deepObject, { 'categoryId': parentId }) + 1;

                deepObject.splice(spliceIndex, 0, {
                    categoryId: parentId,
                    id: entry.id,
                    text: flattenedEntry.text,
                });
            });

            return _.values(deepObject);
        },

        /**
         * Returns the text wrapped as a jQuery object in order to avoid automatic HTML escaping
         *
         * @param   {object}  state
         * @return  {object}
         */
        formatState (state) {
            // https://jsfiddle.net/the94air/awzqtd4w/
            return state.text;
        },

        /**
         * Initializes the Select2 plugin
         *
         * @return  {void}
         */
        initializeSelect2 () {
            pLog('Select2 initializeSelect2 (name, value)', this.name, this.value);

            this.options = {
                allowClear: true,
                escapeMarkup (m) {
                    return m;
                },
                language: {
                    noResults: function() {
                        return $('<li class="not-found">' + i18n.t('select2.noMatchesFound') + '</li>');
                    }
                },
                placeholder: this.placeholder,
                templateResult: this.formatState,
                theme: 'bootstrap',
            };

            if (this.lazyLoading) {
                this.setAjaxOptions();
            }

            if (this.canCreateSearchChoice) {
                this.options.tags = true;

                this.options.createTag = function (params) {
                    return {
                        id: params.term,
                        tag: true,
                        text: params.term,
                    };
                };
            }

            if (this.required || this.multiple) {
                this.options.allowClear = false;
            }

            this.preloadData();
        },

        /**
         * Selects the DOM element and binds the Select2 plugin to it
         *
         * @return  {void}
         */
        initializeSelect2Plugin: function() {
            pLog('Select2 initializeSelect2Plugin (name, value)', this.name, this.value);

            const $select2 = $(this.$el).find('select');

            if ($($select2).data('select2')) {
                $select2.select2('destroy');

                $select2.off();
            }

            $select2.find('option, optgroup').remove();

            $select2.prop('disabled', this.disabled);

            if (!this.required && !this.multiple) {
                const option = new Option();

                $select2.append(option);

                $select2.trigger('change');
            }

            $select2.select2(this.options);

            this.loadInitialValues($select2);

            $select2.off('select2:unselecting');

            $select2.on('select2:unselecting', (event) => {
                if (!this.lazyLoading) {
                    return;
                }

                const id = event.params.args.data.id;

                $select2.find(`option[value="${id}"]`).remove();

                $select2.trigger('change');
            });

            $select2.off('select2:select');

            $select2.on('select2:select', (event) => {
                const newData = event.params.data;

                if(newData.tag !== true) {
                    return;
                }

                store.dispatch('admin/createSearchChoice', {
                    selectedModule: this.relatedTableName,
                    term: newData.text,
                }).then((response) => {
                    const option = new Option(response.text, response.id, true, true);

                    if ($select2.find('[value="' + $.escapeSelector(response.id) + '"]').length) {
                        $select2.find('[value="' + $.escapeSelector(newData.id) + '"]').remove();

                        $select2.trigger('change');

                        return;
                    }

                    $select2.find('[value="' + $.escapeSelector(newData.id) + '"]').replaceWith(option);

                    $select2.trigger('change');
                })
                    .catch(() => {
                        return;
                    });
            });

            $select2.on('change', (event) => {
                this.$emit('change', this.createEventPayload(event));

                eventBus.$emit('fieldTypeChange', this.createEventPayload(event));
            });

            if (this.preselectFirst && _.isEmpty(this.values)) {
                const firstOptionValue = $select2.find('option[value]').first().val();

                if (firstOptionValue) {
                    $select2.val(firstOptionValue).trigger('change');
                }
            }
        },

        /**
         * Returns Select2 selected values.
         *
         * @param   {Object}  event  Select2 event object
         * @return  {array}
         */
        getSelectedOptions: function(event) {
            let options = [];

            if (event.target.selectedOptions.length > 0) {
                // Loop through the jQuery collection of selected options
                for (var i = 0; i < event.target.selectedOptions.length; i++) {
                    let option = event.target.selectedOptions[i];

                    options.push(option.value);
                }
            }

            return options;
        },

        /**
         * Load initial values wrapper function
         *
         * @param   {object}  $select2  Select2 DOM element
         * @return  {void}
         */
        loadInitialValues: function($select2) {
            pLog('Select2 loadInitialValues (name, value)', this.name, this.value);

            if (this.lazyLoading) {
                this.loadInitialValuesFromAPI($select2);

                return;
            }

            this.loadInitialValuesFromStore($select2);
        },

        /**
         * Loads initial values from the API
         *
         * @param   {object}  $select2  Select2 DOM element
         * @return  {void}
         */
        loadInitialValuesFromAPI ($select2) {
            pLog('Select2 loadInitialValuesFromAPI (name, values)', this.name, this.value, this.relatedTableName);

            if (!this.relatedTableName) {
                return;
            }

            let initialSelectionPayload = {
                filter: {
                    id: {
                        in: this.values,
                    }
                },
                include_relations: false,
            };

            api.post(config.ENV.apiBasePath + '/filter/' + this.relatedTableName, initialSelectionPayload)
                .then((response) => {
                    const entries =  _.get(response, this.preloadDataConfig.resultsObjectPath, []);

                    if(!entries.length > 0) {
                        return;
                    }

                    let selection = $select2.select2('data');

                    for (const key in entries) {
                        const self = this;

                        if (entries.hasOwnProperty(key)) {
                            const entry = entries[key];

                            if(_.findIndex(selection, { id: String(entry[self.preloadDataConfig.valuesOfInterest.id]) }) > -1) {
                                continue;
                            }

                            let anchor = entry[self.preloadDataConfig.valuesOfInterest.text];

                            // Use anchor_text as the selected text value even if anchor_html is set
                            if (self.preloadDataConfig.valuesOfInterest.text == 'anchor_html') {
                                anchor = entry['anchor_text'];
                            }

                            const text = `<option selected>${anchor}</option>`;

                            const $option = $(text).val(entry[self.preloadDataConfig.valuesOfInterest.id]);

                            $select2.append($option);
                        }
                    }

                    $select2.trigger('change');
                })
                .catch((response) => {
                    pError(`Failed to load initial values for select2 component #${this.id}`, response);
                });
        },

        /**
         * Loads initial values from already selected data stored in component's store
         *
         * @param   {object}  $select2  Select2 DOM element
         * @return  {void}
         */
        loadInitialValuesFromStore ($select2) {
            pLog('Select2 loadInitialValuesFromStore (name, value)', this.name, this.value);

            if (!this.values) {
                return;
            }

            $select2.val(this.values);

            pLog('Select2 values', this.name, this.value, this.values);

            $select2.trigger('change');
        },

        /**
         * Preloads all availabe data from the API for non-lazy-loading type of select field and continues with
         * the plugin intitalization process
         *
         * @return  {void}
         */
        preloadData: function() {
            pLog('Select2 preloadData (name, value)', this.name, this.value);

            if(!this.lazyLoading && _.isEmpty(this.options.data)) {
                // if optionsData prop has data, use it instead of fetching from remote source
                if (!_.isEmpty(this.optionsData)) {
                    this.options.data = this.optionsData;

                    this.initializeSelect2Plugin();

                    return;
                }

                const uri = this.preloadDataConfig.url;

                const payload = this.preloadDataConfig.payload;

                if(this.isActiveEntryFilter) {
                    _.set(payload, `filter.${this.isActiveEntryFilter}.equal`, true);
                }

                api[this.preloadDataConfig.method](uri, payload)
                    .then((response) => {

                        let entries =  _.get(response, this.preloadDataConfig.resultsObjectPath, []);

                        if(!_.isEmpty(this.preloadDataConfig.sortBy)) {
                            entries = _.sortBy(entries, this.preloadDataConfig.sortBy);
                        }

                        const self = this;

                        let preloadedOptions = {};

                        if (this.flattenToOptgroups) {
                            preloadedOptions = this.flattenToOptionGroups(entries);
                        } else {
                            // If anchor_html is set, don't flatten options
                            if (self.preloadDataConfig.valuesOfInterest.text == 'anchor_html') {
                                preloadedOptions = entries.map(function(entry){
                                    let anchor = entry[self.preloadDataConfig.valuesOfInterest.text];

                                    anchor = (_.isEmpty(entry.anchor_html))
                                        ? entry['anchor_text']
                                        : `<div class="select2-anchor-html">${entry['anchor_html']}</div>`;

                                    return {
                                        id: entry[self.preloadDataConfig.valuesOfInterest.id],
                                        text: anchor,
                                    };
                                });
                            } else {
                                // Flattens the options using the 'parent > child' template
                                preloadedOptions = this.flattenOptions(entries, self);
                            }
                        }

                        if (_.isEmpty(this.options.data)) {
                            this.options.data = preloadedOptions;
                        }

                        this.initializeSelect2Plugin();
                    })
                    .catch((response) => {
                        this.options.data = [];

                        this.initializeSelect2Plugin();

                        pError(`Failed to load values for select2 component #${this.id}`, response);
                    });

                return;
            }

            this.initializeSelect2Plugin();
        },

        setAjaxOptions: function() {
            if (!this.relatedTableName) {
                return;
            }

            const apiToken = storage.get('apiToken');

            const itemsPerPage = photonConfig.paginatedNodesItemsPerPage;

            const self = this;

            let searchTerm = null;

            if(!this.defaultSearchChoice && this.canCreateSearchChoice) {
                store.dispatch('admin/getDefaultSearchChoice', {
                    selectedModule: self.relatedTableName,
                }).then((response) => {
                    this.defaultSearchChoice = response.column_name;
                });
            }

            this.options.ajax = {
                cache: true,
                data: params => {
                    searchTerm = params.term;

                    let payload = {
                        filter: {
                            anchor_text: {},
                        },
                        include_relations: false,
                        pagination: {
                            current_page: params.page || 1,
                            items_per_page: itemsPerPage
                        }
                    };

                    if(self.isActiveEntryFilter) {
                        _.set(payload, `filter.${self.isActiveEntryFilter}.equal`, true);
                    }

                    payload.filter.anchor_text[photonConfig.select2MatchType] = params.term;

                    return payload;
                },
                dataType: 'json',
                delay: 250,
                headers: {
                    Authorization: `Bearer ${apiToken}`
                },
                processResults: data => {
                    if (this.canCreateSearchChoice && this.defaultSearchChoice) {
                        const searchObject = {};

                        searchObject[this.defaultSearchChoice] = searchTerm;

                        const test = _.find(data.body.entries, (entry) => {
                            return (entry[this.defaultSearchChoice].toLowerCase() == searchTerm.toLowerCase());
                        });

                        if (_.isEmpty(test)) {
                            data.body.entries.unshift({
                                id: searchTerm,
                                newSearchChoice: true,
                                tag: true,
                                text: searchTerm,
                            });
                        }
                    }

                    return {
                        pagination: {
                            more: data.body.pagination.has_more_pages,
                        },
                        results: data.body.entries,
                    };
                },
                results: function(data) {
                    return {
                        more: true,
                        results: data.body.entries,
                    };
                },
                type: 'POST',
                url: config.ENV.apiBasePath + '/filter/' + this.relatedTableName,
            };

            this.options.minimumInputLength = 2;

            this.options.templateResult = (data) => {
                let anchor = data[self.preloadDataConfig.valuesOfInterest.text];

                if (self.preloadDataConfig.valuesOfInterest.text == 'anchor_html') {
                    anchor = (_.isEmpty(data.anchor_html))
                        ? data['anchor_text']
                        : `<div class="select2-anchor-html">${data['anchor_html']}</div>`;
                }

                if (data['newSearchChoice'] && data['tag'] && self.canCreateSearchChoice) {
                    return $('<div class="create-new-tag" title="' + data['text'] + '">' + i18n.t('select2.createNewTag') + ' <strong>' + data['text'] + '</strong></div>');
                }

                return anchor;
            };

            this.options.templateSelection = function(data) {
                if (data.anchor_text) {
                    return data.anchor_text;
                }

                return data.text;
            };
        }
    },

    /**
     * Set the mounted hook
     *
     * @return  {void}
     */
    mounted: function() {
        this.$nextTick(function() {
            pWarn('Select2 Mounted (name, value, relatedTableName)', this.name, this.value, this.relatedTableName);

            /**
             * Set the this.preloadDataConfig.url if not passed as a prop, since we can't use other prop value as a
             * default value for the preloadDataConfig prop (undefined).
             */
            if (! this.preloadDataConfig.url) {
                this.preloadDataConfig.url = `${config.ENV.apiBasePath}/filter/${this.relatedTableName}`;
            }

            this.initializeSelect2();
        });
    },

    beforeDestroy: function() {
        const $select2 = $(this.$el).find('select');

        if ($($select2).data('select2')) {
            $select2.select2('destroy');
        }
    },

    watch: {
        'refreshFields'(newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                pWarn(
                    'Watched property fired.',
                    `Select2.${this.name}`,
                    'refreshFields',
                    newEntry,
                    oldEntry,
                    this
                );

                this.initializeSelect2();

                this.$forceUpdate();
            }
        },

        'disabled'(newEntry, oldEntry) {
            if (newEntry !== oldEntry) {
                $(this.$el).find('select').prop('disabled', this.disabled);
            }
        },
    },
};
