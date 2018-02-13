import _ from 'lodash';

export const getCommonFields =  (ComponentReference) => {
    return [
        {
            disabled: !ComponentReference.moduleOptions.length > 0,
            id: ComponentReference.widget.id + '|module',
            inline: false,
            label: 'Module',
            mutation: 'widget/UPDATE_WIDGET_PROPERTY',
            name: ComponentReference.widget.id + '|module',
            optionsData: ComponentReference.moduleOptions.length > 0 ? ComponentReference.moduleOptions : [{ id: null, text: null }],
            preselectFirst: true,
            relatedTableName: false,
            required: true,
            tooltip: 'Data source module.',
            value: _.has(ComponentReference.widget, 'module') ? ComponentReference.widget.module : null,
            vueComponent: 'ManyToMany',
        },

        {
            id: ComponentReference.widget.id + '|heading',
            inline: false,
            label: 'Heading Text',
            mutation: 'widget/UPDATE_WIDGET_PROPERTY',
            name: ComponentReference.widget.id + '|heading',
            placeholder: 'My Widget Title',
            tooltip: 'The text which will appear in widget heading.',
            value: _.has(ComponentReference.widget, 'heading') ? ComponentReference.widget.heading : null,
            vueComponent: 'InputText',
        },

        {
            id: ComponentReference.widget.id + '|icon',
            inline: false,
            label: 'Icon',
            mutation: 'widget/UPDATE_WIDGET_PROPERTY',
            name: ComponentReference.widget.id + '|icon',
            tooltip: 'The icon which will appear in widget heading.',
            value: _.has(ComponentReference.widget, 'icon') ? ComponentReference.widget.icon : null,
            vueComponent: 'BootstrapIconPicker',
        },

        {
            id: ComponentReference.widget.id + '|theme',
            inline: false,
            label: 'Theme',
            mutation: 'widget/UPDATE_WIDGET_PROPERTY',
            name: ComponentReference.widget.id + '|theme',
            optionsData: [
                {
                    id: 'panel-primary',
                    text: 'Blue'
                },

                {
                    id: 'panel-success',
                    text: 'Green'
                },

                {
                    id: 'panel-warning',
                    text: 'Orange'
                },

                {
                    id: 'panel-danger',
                    text: 'Red'
                },

                {
                    id: 'panel-info',
                    text: 'Light Blue'
                },
            ],
            preselectFirst: true,
            relatedTableName: false,
            required: true,
            tooltip: 'Widget theme.',
            value: _.has(ComponentReference.widget, 'theme') ? ComponentReference.widget.theme : null,
            vueComponent: 'ManyToMany',
        },

        {
            id: ComponentReference.widget.id + '|refresh_interval',
            inline: false,
            label: 'Data Refresh Interval',
            mutation: 'widget/UPDATE_WIDGET_PROPERTY',
            name: ComponentReference.widget.id + '|refresh_interval',
            optionsData: [
                {
                    id: 0,
                    text: 'Don\'t refresh automatically.'
                },

                {
                    id: 1000,
                    text: 'Every second'
                },

                {
                    id: 5 * 1000,
                    text: 'Every 5 seconds'
                },

                {
                    id: 10 * 1000,
                    text: 'Every 10 seconds'
                },

                {
                    id: 30 * 1000,
                    text: 'Every 30 second'
                },

                {
                    id: 60 * 1000,
                    text: 'Every minute'
                },

                {
                    id: 5 * 60 * 1000,
                    text: 'Every 5 minutes'
                },
            ],
            relatedTableName: false,
            required: true,
            tooltip: 'Widget data refresh interval.',
            value: _.has(ComponentReference.widget, 'refresh_interval') ? ComponentReference.widget.refresh_interval : null,
            vueComponent: 'ManyToMany',
        },

        {
            id: ComponentReference.widget.id + '|private',
            inline: true,
            label: 'Private',
            mutation: 'widget/UPDATE_WIDGET_PROPERTY',
            name: ComponentReference.widget.id + '|private',
            tooltip: 'Private widgets are available only to users who created them.',
            value: _.has(ComponentReference.widget, 'private') ? ComponentReference.widget.private : null,
            vueComponent: 'Boolean',
        },
    ];
};
