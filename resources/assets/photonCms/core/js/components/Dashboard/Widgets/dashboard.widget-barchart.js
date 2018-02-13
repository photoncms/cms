/* global Chart */

import Vue from 'vue';
var uiStore = require('../../stores/store.ui');
var modulesStore = require('../../stores/store.modules');
var widgetStore = require('../../stores/store.widget');

var WidgetStats = Vue.extend({
    template: require('./dashboard.widget-barchart.template.html'),
    props: ['widget', 'widgetTitle'],
    data: function() {
        return {
            ui: uiStore.state,
            polling: false,
            setupMode: this.widget.setupMode || false,
            timeSeries: null
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
            var fields = require('./dashboard.widget-barchart.fields')
                .call(this);
            return fields;
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
                    this.getTimeseries(true);
                }.bind(this), 500);
            } else {
                if (this.barChart) {
                    this.barChart.clear();
                    setTimeout(function() {
                        this.barChart.destroy();
                        delete this.barChart;
                    }.bind(this), 500);
                }
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
                this.widget.heading = selectedText + ' chart';
                this.widget.heading = this.widget.heading.substring(0, 41);
                this.widget.currentHeading = this.widget.heading;
            }
        },
        onPeriodSelect: function() {},
        processChartData: function(chart) {
            // var list = $(this.$el).find('.front .list-group');
            if (chart) {
                if (this.barChart) {
                    this.updateChart(chart.data);
                } else {
                    var chartData = {
                        labels: chart.labels,
                        data: chart.data
                    };
                    this.initializeChart(chartData);
                }
            }
        },
        getTimeseries: function(longpoll) {
            if (!this.widget.module || !this.widget.period) {
                return;
            }
            widgetStore.getTimeSeries(
                this.widget.module,
                this.widget.period,
                function(err, res) {
                    if (err) {
                        console.log(err);
                    }
                    try {
                        this.processChartData(res.results);
                    } catch (e) {
                        console.log(e);
                    }
                    if (this.polling) {
                        clearTimeout(this.polling);
                    }
                    var refreshInterval = parseInt(this.widget.refreshInterval, 10);
                    if (refreshInterval && longpoll) {
                        this.polling = setTimeout(function() {
                            this.getTimeseries(longpoll);
                        }.bind(this), refreshInterval);
                    }
                }.bind(this)
            );
        },
        initializeChart: function(chartData) {
            var ctx = document.getElementById('widget-chart-' + this.widget.id).getContext('2d');
            var color = $(this.$el).find('.front .panel-heading').css('background-color');
            var labels = chartData.labels;
            // if (this.widget.period === 'hour') {
            //     labels = chartData.labels.map(function(label) {
            //         if (parseInt(label, 10) % 2) {
            //             return label;
            //         } else {
            //             return '';
            //         }
            //     });
            // }
            // console.log(labels);
            var data = {
                labels: labels,
                datasets: [{
                    label: 'My First dataset',
                    fillColor: color,
                    strokeColor: color,
                    highlightFill: color,
                    highlightStroke: color,
                    data: chartData.data
                }]
            };
            this.barChart = new Chart(ctx).Bar(data);
        },
        updateChart: function(chartData) {
            this.barChart.datasets[0].bars.map(function(bar, index) {
                bar.value = chartData[index];
            });
            this.barChart.update();
        }
    },
    mounted: function() {
        this.$nextTick(function() {
            if (this.widget.module && this.widget.period) {
                this.widget.currentHeading = this.widget.heading;
                if (this.setupMode) {
                    this.getTimeseries();
                } else {
                    this.getTimeseries(true);
                }
            }
        });
    },
    beforeDestroy: function() {
        if (this.barChart) {
            this.barChart.destroy();
        }
        if (this.polling) {
            clearTimeout(this.polling);
        }
        if (this.animationTimeout) {
            clearTimeout(this.animationTimeout);
        }
    }
});
export default WidgetStats;
