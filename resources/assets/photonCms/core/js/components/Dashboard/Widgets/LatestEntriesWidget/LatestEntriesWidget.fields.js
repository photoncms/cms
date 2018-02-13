import _ from 'lodash';

import { getCommonFields } from '_/components/Dashboard/Widgets/DashboardWidgets.commonFields.js';

export const getFields = (LatestEntriesWidget) => {
    const commonFields = getCommonFields(LatestEntriesWidget);

    const fields = [
        ...commonFields,
        {
            disabled: !LatestEntriesWidget.imageFields.length > 0,
            id: LatestEntriesWidget.widget.id + '|image_field',
            inline: false,
            label: 'Image Field',
            mutation: 'widget/UPDATE_WIDGET_PROPERTY',
            name: LatestEntriesWidget.widget.id + '|image_field',
            optionsData: LatestEntriesWidget.imageFields.length > 0 ? LatestEntriesWidget.imageFields : [{ id: null, text: null }],
            preselectFirst: true,
            relatedTableName: false,
            required: true,
            tooltip: 'Field to be used as entry image.',
            value: _.has(LatestEntriesWidget.metaData, 'image_field') ? LatestEntriesWidget.metaData.image_field : null,
            vueComponent: 'ManyToMany',
        },
    ];

    return fields;
};
