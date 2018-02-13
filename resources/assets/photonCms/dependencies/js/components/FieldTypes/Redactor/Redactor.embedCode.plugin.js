(function($) {
    $.Redactor.prototype.embedCode = function () {
        return {
            langs: {
                en: {
                    'embed-code': 'Embed Code'
                }
            },

            getTemplate: function ()
            {
                return String()
                + '<div class="modal-section" id="redactor-modal-embed-code-insert">'
                    + '<section>'
                        + '<label>' + this.lang.get('embed-code') + '</label>'
                        + '<textarea id="redactor-insert-embed-code-area" style="height: 160px;"></textarea>'
                    + '</section>'
                    + '<section>'
                        + '<button id="redactor-modal-button-action">Insert</button>'
                        + '<button id="redactor-modal-button-cancel">Cancel</button>'
                    + '</section>'
                + '</div>';
            },

            init: function ()
            {
                var button = this.button.addAfter('image', 'embedCode', this.lang.get('embed-code'));

                this.button.setIcon(button, '<i class="fa fa-code"></i>');

                this.button.addCallback(button, this.embedCode.show);
            },

            show: function ()
            {
                this.modal.addTemplate('embedCode', this.embedCode.getTemplate());

                this.modal.load('embedCode', this.lang.get('embed-code'), 700);

                // action button
                this.modal.getActionButton().text(this.lang.get('insert')).on('click', this.embedCode.insert);

                this.modal.show();

                // focus
                if (this.detect.isDesktop()) {
                    setTimeout(function () {
                        $('#redactor-insert-embed-code-area').focus();
                    }, 1);
                }
            },

            insert: function()
            {
                var data = $('#redactor-insert-embed-code-area').val();

                data = '<div class="embed-code-container">' + data + '</div><br>';

                this.modal.close();

                this.placeholder.hide();

                // buffer
                this.buffer.set();

                // insert
                this.air.collapsed();

                this.insert.raw(data);
            }
        };
    };
})(jQuery);
