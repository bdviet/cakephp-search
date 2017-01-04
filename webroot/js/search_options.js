var search_options = search_options || {};

(function ($) {
    /**
     * SearchOptions Logic.
     *
     * @param {object} options configuration options
     */
    function SearchOptions(options)
    {
        this.formId = options.hasOwnProperty('formId') ? options.formId : '#SearchFilterForm';
        this.displayId = options.hasOwnProperty('displayId') ? options.displayId : '#displayColumns';
        this.availableId = options.hasOwnProperty('availableId') ? options.availableId : '#availableColumns';
        this.connectId = options.hasOwnProperty('connectId') ? options.connectId : '.connectedSortable';
    }

    /**
     * Initialize method.
     *
     * @return {undefined}
     */
    SearchOptions.prototype.init = function () {
        var that = this;

        $(this.displayId + ',' + this.availableId).sortable({
            connectWith: this.connectId
        }).disableSelection();

        $(this.formId).submit(function (e) {
            that._getDisplayColumns();

            return true;
        });
    };

    /**
     * Generate related saved searches inputs.
     *
     * @return {undefined}
     */
    SearchOptions.prototype._getDisplayColumns = function () {
        var that = this;
        $(that.displayId).children().each(function (k, v) {
            $(that.formId).append(
                $('<input>')
                   .attr('type', 'hidden')
                   .attr('name', 'display_columns[' + k + ']').val($(v).data('id'))
            );
        });
    };

    search_options = new SearchOptions({
        formId: '#SearchFilterForm',
        displayId: '#displayColumns',
        availableId: '#availableColumns',
        connectId: '.connectedSortable'
    });

    search_options.init();

})(jQuery);
