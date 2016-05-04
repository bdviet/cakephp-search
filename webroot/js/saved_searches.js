var saved_searches = saved_searches || {};

(function($) {

    /**
     * Saved Searches Logic.
     *
     * @param {object} options configuration options
     */
    function SavedSearches(options) {
        this.resultsSelectId = options.hasOwnProperty('resultsSelectId') ? options.resultsSelectId : '#savedResultsSelect';
        this.resultsViewId = options.hasOwnProperty('resultsViewId') ? options.resultsViewId : '#savedResultsView';
        this.resultsDeleteId = options.hasOwnProperty('resultsDeleteId') ? options.resultsDeleteId : '#savedResultsDelete';

        this.criteriasSelectId = options.hasOwnProperty('criteriasSelectId') ? options.criteriasSelectId : '#savedCriteriasSelect';
        this.criteriasViewId = options.hasOwnProperty('criteriasViewId') ? options.criteriasViewId : '#savedCriteriasView';
        this.criteriasDeleteId = options.hasOwnProperty('criteriasDeleteId') ? options.criteriasDeleteId : '#savedCriteriasDelete';

        this.viewPrefixId = options.hasOwnProperty('viewPrefixId') ? options.viewPrefixId : '#view_';
        this.deletePrefixId = options.hasOwnProperty('deletePrefixId') ? options.deletePrefixId : '#delete_';
    }

    /**
     * Initialize method.
     *
     * @return {undefined}
     */
    SavedSearches.prototype.init = function() {
        this._onResultsSelect();
        this._onCriteriasSelect();
    };

    /**
     * Enable saves search results view and delete functionality on click.
     *
     * @return {undefined}
     */
    SavedSearches.prototype._onResultsSelect = function() {
        var that = this;

        $(this.resultsViewId).on('click', function() {
            selElement = $(that.resultsSelectId).val();
            window.location.href = $(that.viewPrefixId + selElement).attr('href');
        });

        $(this.resultsDeleteId).on('click', function() {
            selElement = $(that.resultsSelectId).val();
            $(that.deletePrefixId + selElement).click();
        });
    };

    /**
     * Enable saves search criterias view and delete functionality on click.
     *
     * @return {undefined}
     */
    SavedSearches.prototype._onCriteriasSelect = function() {
        var that = this;

        $(this.criteriasViewId).on('click', function() {
            selElement = $(that.criteriasSelectId).val();
            $(that.viewPrefixId + selElement).submit();
        });

        $(this.criteriasDeleteId).on('click', function() {
            selElement = $(that.criteriasSelectId).val();
            $(that.deletePrefixId + selElement).click();
        });
    };

    saved_searches = new SavedSearches({
        resultsSelectId: '#savedResultsSelect',
        resultsViewId: '#savedResultsView',
        resultsDeleteId: '#savedResultsDelete',
        criteriasSelectId: '#savedCriteriasSelect',
        criteriasViewId: '#savedCriteriasView',
        criteriasDeleteId: '#savedCriteriasDelete',
        viewPrefixId: '#view_',
        deletePrefixId: '#delete_'
    });

    saved_searches.init();

})(jQuery);
