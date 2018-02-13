import Vue from 'vue';
var uiStore = require('../../stores/store.ui');
var modulesStore = require('../../stores/store.modules');
var widgetStore = require('../../stores/store.widget');

var WidgetStats = Vue.extend({
    template: require('./dashboard.widget-stats.template.html'),
    props: ['widget', 'widgetTitle'],
    data: function() {
        return {
            ui: uiStore.state,
            polling: false,
            setupMode: this.widget.setupMode || false,
            statsData: { }
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
        fields: function() {
            var fields = require('./dashboard.widget-stats.fields')
                .call(this);
            return fields;
        },
        stats: function() {
            var stats = [];
            var list = $(this.$el).find('.front .list-group');
            if (this.statsData) {
                stats.push(this.statsData.hour ? {
                    text: 'Last Hour',
                    value: this.statsData.hour.current,
                    change: this.roundToDecimals((1 - this.statsData.hour.previous / this.statsData.hour.current) * 100, 1)
                } : null);
                stats.push(this.statsData.day ? {
                    text: 'Today',
                    value: this.statsData.day.current,
                    change: this.roundToDecimals((1 - this.statsData.day.previous / this.statsData.day.current) * 100, 1)
                } : null);
                stats.push(this.statsData.week ? {
                    text: 'This Week',
                    value: this.statsData.week.current,
                    change: this.roundToDecimals((1 - this.statsData.week.previous / this.statsData.week.current) * 100, 1)
                } : null);
                stats.push(this.statsData.month ? {
                    text: 'This Month',
                    value: this.statsData.month.current,
                    change: this.roundToDecimals((1 - this.statsData.month.previous / this.statsData.month.current) * 100, 1)
                } : null);
                stats.push(this.statsData.total ? {
                    text: 'Total',
                    value: this.statsData.total.current
                } : null);
                if (!this.setupMode) {
                    this.animationTimeout = setTimeout(function() {
                        list.find('li:first-child').addClass('animated flash');
                    }, 100);
                }
            }
            // console.log(JSON.stringify(stats));
            return stats;
        }
    },
    components: {
        'input-text': require('../input-text/input-text'),
        'select-basic': require('../select-basic/select-basic')
    },
    methods: {
        roundToDecimals: function(num, decimals) {
            // Correction is required to counter floating number behaviour
            const correction = 1 / Math.pow(10, decimals + 3);
            const multiplier = Math.pow(10, decimals);
            return Math.round(
                    (num + correction) * multiplier
                ) / multiplier;
        },
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
                    this.getStatsData(true);
                }.bind(this), 300);
            } else {
                $(this.$el).find('.animated.flash').removeClass('animated flash');
                this.setupMode = true;
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
                this.widget.heading = selectedText + ' stats';
                this.widget.heading = this.widget.heading.substring(0, 41);
                this.widget.currentHeading = this.widget.heading;
            }
            this.getStatsData();
        },
        getStatsData: function(longpoll) {
            if (!this.widget.module) {
                this.statsData = { };
                return;
            }
            widgetStore.getModuleStats(this.widget.module, function(err, res) {
                if (err) {
                    console.log(err);
                }
                this.statsData = res.results;
                if (this.polling) {
                    clearTimeout(this.polling);
                }
                var refreshInterval = parseInt(this.widget.refreshInterval, 10);
                if (refreshInterval && longpoll) {
                    this.polling = setTimeout(function() {
                        this.getStatsData(longpoll);
                    }.bind(this), refreshInterval);
                }
            }.bind(this));
        }
    },
    mounted: function() {
        this.$nextTick(function() {
            if (this.widget.module) {
                this.widget.currentHeading = this.widget.heading;
                if (this.setupMode) {
                    this.getStatsData();
                } else {
                    this.getStatsData(true);
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

export default WidgetStats;
