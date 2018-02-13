import Vue from 'vue';
var uiStore = require('../../stores/store.ui');
var modulesStore = require('../../stores/store.modules');
var widgetStore = require('../../stores/store.widget');

var WidgetLatest = Vue.extend({
    template: require('./dashboard.widget-latest.template.html'),
    props: ['widget', 'widgetTitle'],
    data: function() {
        return {
            ui: uiStore.state,
            polling: false,
            setupMode: this.widget.setupMode || false,
            sourceModule: { }
        };
    },
    computed: {
        moduleOptions: function() {
            var options = [{
                value: null,
                text: null
            }];
            modulesStore.state.modules.map(function(currentModule) {
                if (currentModule.type !== 'module_group') {
                    options.push({
                        value: currentModule.table_name,
                        text: currentModule.name
                    });
                }
            });
            return options;
        },
        textFields: function() {
            var options = [{
                value: null,
                text: null
            }];
            if (this.sourceModule.fields) {
                this.sourceModule.fields
                    .map(function(field) {
                        if (field.field_type === 'input-text') {
                            options.push({
                                value: field.column_name,
                                text: field.field_name
                            });
                        }
                    });
            }
            return options;
        },
        imageFields: function() {
            var options = [{
                value: null,
                text: null
            }];
            if (this.sourceModule.fields) {
                this.sourceModule.fields
                    .map(function(field) {
                        if (field.field_type === 'input-image') {
                            options.push({
                                value: field.column_name,
                                text: field.field_name
                            });
                        }
                    });
            }
            return options;
        },
        fields: function() {
            var fields = require('./dashboard.widget-latest.fields')
                .call(this);
            return fields;
        },
        condensed: function() {
            if (this.sourceModule.items && this.widget.excerptField) {
                return false;
            }
            return true;
        },
        items: function() {
            var items = [];
            var list = $(this.$el).find('.front .list-group');
            var length = this.widget.excerptField ? 5 * 2 : 6 * 2;
            if (this.sourceModule.items) {
                var offsetCount = null;
                for (var index = 0; index < length; index++) {
                    items[index] = this.sourceModule.items[index] || {
                        id: 'blank.' + index
                    };
                    if (items[index] && items[index].created_at) {
                        items[index].timeAgo = moment(items[index].created_at).fromNow();
                    }
                    var offset = this.widget.excerptField ? -50 : -40;
                    if (this.lastAddedId === items[index].id) {
                        // console.log(this.lastAddedId, index);
                        offsetCount = index;
                    }
                }
                if (offsetCount === null && items[0].id > this.lastAddedId) {
                    offsetCount = this.widget.excerptField ? 4 : 5;
                } else if (offsetCount === null) {
                    offsetCount = 0;
                }
                if (!this.setupMode) {
                    list.css('top', offsetCount * offset);
                    setTimeout(function() {
                        list.addClass('scrolling');
                        list.css('top', 0);
                    });
                    this.animationTimeout = setTimeout(function() {
                        list.find('li:first-child').addClass('animated flash');
                        list.removeClass('scrolling');
                    // list.find('.animated.flash').removeClass('animated flash');
                    }, 200);
                }
                this.lastAddedId = items[0].id;
            }
            // console.log(JSON.stringify(items[index].created_at, null, ' '), JSON.stringify(items[index].timeAgo, null, ' '));
            return items;
        }
    },
    components: {
        'input-text': require('../input-text/input-text'),
        'select-basic': require('../select-basic/select-basic')
    },
    methods: {
        toggleSetup: function() {
            if (this.polling) {
                clearTimeout(this.polling);
            }
            if (this.animationTimeout) {
                clearTimeout(this.animationTimeout);
            }
            if (this.setupMode) {
                this.setupMode = false;
                setTimeout(function() {
                    this.getModuleData(true);
                }.bind(this), 300);
            } else {
                $(this.$el).find('.animated.flash').removeClass('animated flash');
                this.setupMode = true;
                setTimeout(function() {
                    $(this.$el).find('.scrolling').removeClass('scrolling');
                }.bind(this), 200);
            }
        },
        removeWidget: function() {
            // TODO: remove logic
            if (this.polling) {
                clearTimeout(this.polling);
            }
        },
        onModuleSelect: function(selectedValue, selectedText) {
            if (selectedValue === this.widget.oldModuleSelection) {
                return;
            }
            this.widget.oldModuleSelection = selectedValue;
            if (!this.widget.heading ||
                this.widget.heading === '' ||
                this.widget.heading === this.widget.currentHeading
            ) {
                this.widget.heading = 'Latest ' + selectedText;
                this.widget.heading = this.widget.heading.substring(0, 41);
                this.widget.currentHeading = this.widget.heading;
            }
            this.widget.titleField = null;
            this.widget.excerptField = null;
            this.widget.imageField = null;
            this.getModuleData();
        },
        getModuleData: function(longpoll) {
            if (!this.widget.module) {
                this.sourceModule = { };
                return;
            }
            widgetStore.getLatest(this.widget.module, function(err, res) {
                if (err) {
                    console.log(err);
                }
                this.sourceModule = res.results;
                if (this.polling) {
                    clearTimeout(this.polling);
                }
                var refreshInterval = parseInt(this.widget.refreshInterval, 10);
                if (refreshInterval && longpoll) {
                    this.polling = setTimeout(function() {
                        this.getModuleData(longpoll);
                    }.bind(this), refreshInterval);
                }
            }.bind(this));
        }
    },
    mounted: function() {
        this.$nextTick(function() {
            if (this.widget.module) {
                this.widget.currentHeading = this.widget.heading;
                this.widget.oldModuleSelection = this.widget.module;
                if (this.setupMode) {
                    this.getModuleData();
                } else {
                    this.getModuleData(true);
                }
            }
        });
    },
    beforeDestroy: function() {
        if (this.polling) {
            clearTimeout(this.polling);
        }
        if (this.animationTimeout) {
            clearTimeout(this.animationTimeout);
        }
    }
});

export default WidgetLatest;
