import { items } from '~/components/FieldTypes/Redactor/Redactor.clips.plugin.items';

(function($)
{
    $.Redactor.prototype.clips = function()
    {
        return {
            init: function()
            {
                this.clips.template = $('<ul id="redactor-modal-list">');

                for (var i = 0; i < items.length; i++)
                {
                    var li = $('<li>');
                    var anchor = '<span style="float: left; padding-top: 25px;">' + items[i][0] + '</span><img src="' + items[i][2] + '" style="float: right;"/>';
                    var a = $('<a href="#" class="redactor-clips-link" style="height: auto; overflow: hidden;">').html(anchor);
                    var div = $('<div class="redactor-clips">').hide().html(items[i][1]);

                    li.append(a);
                    li.append(div);
                    this.clips.template.append(li);
                }

                this.modal.addTemplate('clips', '<div class="modal-section">' + this.utils.getOuterHtml(this.clips.template) + '</div>');

                var button = this.button.add('clips', 'Clips');
                this.button.setIcon(button, '<i class="re-icon-clips"></i>');
                this.button.addCallback(button, this.clips.show);

            },
            show: function()
            {
                this.modal.load('clips', 'Insert Clips', 500);

                $('#redactor-modal-list').find('.redactor-clips-link').each($.proxy(this.clips.load, this));

                this.modal.show();
            },
            load: function(i,s)
            {
                $(s).on('click', $.proxy(function(e)
                {
                    e.preventDefault();
                    this.clips.insert($(s).next().html());

                }, this));
            },
            insert: function(html)
            {
                this.buffer.set();
                this.air.collapsedEnd();
                this.insert.html(html);
                this.modal.close();
            }
        };
    };
})(jQuery, items);

