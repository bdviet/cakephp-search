var view_search_result = view_search_result || {};

(function($) {
    /**
     * View Search Result Logic.
     *
     * @param {object} options
     */
    function ViewSearchResult() {}

    /**
     * Initialize method.
     *
     * @return {void}
     */
    ViewSearchResult.prototype.init = function(options) {
        this.table_id = options.hasOwnProperty('table_id') ? options.table_id : null;
        this.sort_by_field = options.hasOwnProperty('sort_by_field') ? options.sort_by_field : 0;
        // set default value, if empty string is passed to the sort_by_field option
        if ('' === this.sort_by_field) {
            this.sort_by_field = 0;
        }
        this.sort_by_order = options.hasOwnProperty('sort_by_order') ? options.sort_by_order : 'asc';
        if ('' === this.sort_by_order) {
            this.sort_by_order = 'asc';
        }

        this.datatable();
    };

    /**
     * Initialize datatables.
     *
     * @return {void}
     */
    ViewSearchResult.prototype.datatable = function() {
        var that = this;
        $(this.table_id).DataTable({
            paging: false,
            searching: false,
            order: [[that.sort_by_field, that.sort_by_order]]
        });
    };

    view_search_result = new ViewSearchResult();

})(jQuery);
