var fields = function() {
    var commonFields = require('./dashboard.widget-common.fields').call(this);
    var specificFields = [];
    return commonFields.concat(specificFields);
};
export default fields;
