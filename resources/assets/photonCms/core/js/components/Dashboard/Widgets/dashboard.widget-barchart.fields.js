var fields = function() {
    var commonFields = require('./dashboard.widget-common.fields').call(this);
    var specificFields = [{
        id: 'widget.' + this.widget.id + '.period',
        name: 'widget.' + this.widget.id + '.period',
        type: 'select-basic',
        model: {
            object: this.widget,
            key: 'period'
        },
        label: 'Time period',
        tooltip: 'Select chart time period.',
        options: [{
            value: 'hour',
            text: '12-Hour'
        }, {
            value: 'day',
            text: '7-Day'
        }, {
            value: 'week',
            text: '5-Week'
        }, {
            value: 'month',
            text: '12-Month'
        }],
        onSelect: this.onPeriodSelect
    }];
    return commonFields.concat(specificFields);
};
export default fields;
