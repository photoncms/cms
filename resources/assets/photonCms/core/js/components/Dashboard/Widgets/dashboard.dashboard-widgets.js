import Vue from 'vue';
var uiStore = require('../../stores/store.ui');
var userStore = require('../../stores/store.user');

var DashboardWidgets = Vue.extend({
    template: require('./dashboard.dashboard-widgets.template.html'),
    data: function() {
        return {
            ui: uiStore.state,
            user: userStore.state.user
        };
    },
    components: {
        'widget-latest': require('./dashboard.widget-latest'),
        'widget-stats': require('./dashboard.widget-stats'),
        'widget-barchart': require('./dashboard.widget-barchart')
    },
    computed: {
        widgets: function() {
            if (this.user && this.user.widgets.length) {
                return this.user.widgets;
            }
        }
    },
    methods: {
        lightUp: function(progressive) {
            var time = progressive ? 200 : 0;
            $('.proton-widget').map(function(index, widget) {
                setTimeout(function() {
                    $(widget).addClass('lit');
                }, time * index);
            });
        },
        sortWidgets: function() {
            $('.widget-group').sortable({
                cancel: '.placeholder, .flip-it',
                items: '.proton-widget, .modal',
                placeholder: 'drag-placeholder',
                start: function() {
                    $('.tooltip').tooltip('hide');
                    $('#new-widget-modal').modal('hide');
                },
                stop: function() {
                    // proton.dashboard.saveWidgetPositions();
                },
                tolerance: 'pointer',
                handle: '.panel-heading'
            });
        },
        newWidget: function(type) {
            this.user.widgets.push({
                id: 6 + Math.random() * 1000,
                module: null,
                type: type,
                theme: 'panel-primary',
                heading: null,
                period: (type === 'widget-barchart') ? 'day' : null,
                refreshInterval: 5000,
                icon: null,
                setupMode: true
            });
            $('#new-widget-modal').modal('hide');
            setTimeout(function() {
                this.lightUp();
                $('.proton-widget').last().addClass('animated pulse');
                setTimeout(function() {
                    $('.proton-widget.animated').removeClass('animated pulse');
                }, 2000);
                $('.widget-group').sortable('refresh');
            }.bind(this));
        }
    },
    mounted: function() {
        this.$nextTick(function() {
            this.lightUp(true);
            this.sortWidgets();
        });
    },
    beforeDestroy: function() {
        $('.widget-group').sortable('destroy');
        $('#new-widget-modal').modal('hide');
    }
});

export default DashboardWidgets;
