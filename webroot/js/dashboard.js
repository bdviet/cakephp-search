var dashboard = dashboard || {};

(function ($) {
    /**
     * Dashboard Logic.
     *
     * @param {object} options configuration options
     */
    function Dashboard(options)
    {
        this.formId = options.hasOwnProperty('formId') ? options.formId : '#dashboardForm';
        this.droppableId = options.hasOwnProperty('droppableId') ? options.droppableId : '.droppable-area';
        this.containerId = options.hasOwnProperty('containerId') ? options.containerId : '.savetrue';
        this.targetId = options.hasOwnProperty('targetId') ? options.targetId : '.droppable';
    }

    /**
     * Initialize method.
     *
     * @return {undefined}
     */
    Dashboard.prototype.init = function () {
        that = this;

        $(this.droppableId).sortable({
            connectWith: 'ul'
        });

        $(this.droppableId).disableSelection();

        // that = this;
        $(this.formId).submit(function (e) {
            that._generateInputs();

            return true;
        });
    };

    /**
     * Generate related saved searches inputs.
     *
     * @return {undefined}
     */
    Dashboard.prototype._generateInputs = function () {
        that = this;

        $(that.containerId).each(function (i, c) {
            $(c).find(that.targetId).each(function (k, e) {
                $form = $(that.formId);
                $form.append(
                    $('<input>')
                       .attr('type', 'hidden')
                       .attr('name', 'widgets[widget_id][]').val($(e).data('id'))
                );
                $form.append(
                    $('<input>')
                        .attr('type','hidden')
                        .attr('name','widgets[widget_type][]').val($(e).data('type'))
                );
                $form.append(
                    $('<input>')
                       .attr('type', 'hidden')
                       .attr('name', 'widgets[row][]').val(k)
                );
                $form.append(
                    $('<input>')
                       .attr('type', 'hidden')
                       .attr('name', 'widgets[column][]').val($(c).data('column'))
                );
            });
        });
    };

    dashboard = new Dashboard({
        formId: '#dashboardForm',
        droppableId: '.droppable-area',
        containerId: '.savetrue',
        targetId: '.droppable'
    });

    dashboard.init();

})(jQuery);
