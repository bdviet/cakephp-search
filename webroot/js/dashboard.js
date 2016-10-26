var dashboard = dashboard || {};

(function($) {
    /**
     * Dashboard Logic.
     *
     * @param {object} options configuration options
     */
    function Dashboard(options) {
        this.formId = options.hasOwnProperty('formId') ? options.formId : '#dashboardForm';
        this.dropableId = options.hasOwnProperty('dropableId') ? options.dropableId : '.dropable-area';
        this.containerId = options.hasOwnProperty('containerId') ? options.containerId : '.savetrue';
        this.targetId = options.hasOwnProperty('targetId') ? options.targetId : '.dropable';
    }

    /**
     * Initialize method.
     *
     * @return {undefined}
     */
    Dashboard.prototype.init = function() {
        that = this;

        $(this.dropableId).sortable({
            connectWith: 'ul'
        });

        $(this.dropableId).disableSelection();

        // that = this;
        $(this.formId).submit(function(e) {
            that._generateInputs();

            return true;
        });
    };

    /**
     * Generate related saved searches inputs.
     *
     * @return {undefined}
     */
    Dashboard.prototype._generateInputs = function() {
        that = this;

        $(that.containerId).each(function(i, c) {
            $(c).find(that.targetId).each(function(k, e) {
                $form = $(that.formId);
                $form.append(
                    $('<input>')
                       .attr('type', 'hidden')
                       .attr('name', 'saved_searches[_ids][]').val($(e).data('id'))
                );
                $form.append(
                    $('<input>')
                       .attr('type', 'hidden')
                       .attr('name', 'saved_searches[_rows][]').val(k)
                );
                $form.append(
                    $('<input>')
                       .attr('type', 'hidden')
                       .attr('name', 'saved_searches[_columns][]').val($(c).data('column'))
                );
            });
        });
    };

    dashboard = new Dashboard({
        formId: '#dashboardForm',
        dropableId: '.dropable-area',
        containerId: '.savetrue',
        targetId: '.dropable'
    });

    dashboard.init();

})(jQuery);
