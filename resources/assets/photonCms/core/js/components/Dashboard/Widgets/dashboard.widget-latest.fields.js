var fields = function() {
    var commonFields = require('./dashboard.widget-common.fields').call(this);
    var specificFields = [{
        id: 'widget.' + this.widget.id + '.titleField',
        name: 'widget.' + this.widget.id + '.titleField',
        type: 'select-basic',
        model: {
            object: this.widget,
            key: 'titleField'
        },
        label: 'Title Field',
        tooltip: 'Field to be used for entry title (required, plain text fields only).',
        options: this.textFields.length ? this.textFields : []
    }, {
        id: 'widget.' + this.widget.id + '.excerptField',
        name: 'widget.' + this.widget.id + '.excerptField',
        type: 'select-basic',
        model: {
            object: this.widget,
            key: 'excerptField'
        },
        label: 'Excerpt Field',
        tooltip: 'Field to be used for entry excerpt (plain text fields only).',
        options: this.textFields.length ? this.textFields : []
    }, {
        id: 'widget.' + this.widget.id + '.imageField',
        name: 'widget.' + this.widget.id + '.imageField',
        type: 'select-basic',
        model: {
            object: this.widget,
            key: 'imageField'
        },
        label: 'Image Field',
        tooltip: 'Field to be used for entry image (image fields only).',
        options: this.imageFields.length ? this.imageFields : []
    }];
    return commonFields.concat(specificFields);
};
export default fields;
