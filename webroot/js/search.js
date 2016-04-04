var search = search || {};

(function($) {
    /**
     * Search Logic.
     * @param {object} options configuration options
     */
    function Search(options) {
        this.formId = options.hasOwnProperty('formId') ? options.formId : '#SearchFilterForm';
        this.addFieldId = options.hasOwnProperty('addFieldId') ? options.addFieldId : '#addFilter';
        this.fieldProperties = {};
        this.fieldTypeOperators = {};
    }

    /**
     * Initialize method.
     * @return {void}
     */
    Search.prototype.init = function() {
        this._onfieldSelect();
    };

    Search.prototype.setFieldProperties = function(fieldProperties) {
        this.fieldProperties = fieldProperties;
    };

    Search.prototype.setFieldTypeOperators = function(fieldTypeOperators) {
        this.fieldTypeOperators = fieldTypeOperators;
    };

    Search.prototype._onfieldSelect = function() {
        that = this;
        $(this.formId + ' ' + this.addFieldId).change(function() {
            if ('' !== this.value) {
                var props = that.fieldProperties[this.value];
                that._generateField(this.value, props);
                this.value = '';
            }
        });
    };

    Search.prototype._generateField = function(field, properties) {
        var timestamp = new Date().getUTCMilliseconds();
        var inputHtml = '<div class="row">';
            inputHtml += '<div class="col-xs-3">';
                inputHtml += this._generateFieldLabel(field);
                inputHtml += this._generateFieldType(field, properties.type, timestamp);
            inputHtml += '</div>';
            inputHtml += '<div class="col-xs-3">';
                inputHtml += this._generateFieldOperator(field, properties.type, timestamp);
            inputHtml += '</div>';
            inputHtml += '<div class="col-xs-4">';
                inputHtml += this._generateFieldInput(field, properties, timestamp);
            inputHtml += '</div>';
        inputHtml += '</div>';
        $(this.formId + ' .body').append(inputHtml);
    };

    Search.prototype._generateFieldLabel = function(field) {
        var result = '';
        result += '<p class="form-control-static">' + field + '</p>';

        return result;
    };

    Search.prototype._generateFieldType = function(field, type, timestamp) {
        var result = '';
        result += '<input type="hidden" name="' + field + '[' + timestamp + '][type]" value="' + type + '">';

        return result;
    };

    Search.prototype._generateFieldOperator = function(field, type, timestamp) {
        var result = '';
        if (this.fieldTypeOperators.hasOwnProperty(type)) {
            result += '<select name="' + field + '[' + timestamp + '][operator]" class="form-control input-sm">';
            $.each(this.fieldTypeOperators[type], function(k, v) {
                result += '<option value="' + k + '">';
                result += v + '</option>';
            });
            result += '</select>';
        }

        return result;
    };

    Search.prototype._generateFieldInput = function(field, properties, timestamp) {
        var result = '';
        switch (properties.type) {
            case 'list':
                result += '<select name="' + field + '[' + timestamp + '][value]" class="form-control input-sm">';
                $.each(properties.fieldOptions, function(k, v) {
                    result += '<option value="' + k + '">' + v + '</option>';
                });
                result += '</select>';
                break;
            case 'boolean':
                result += '<input type="hidden" name="' + field + '[' + timestamp + '][value]" value="0">';
                result += '<input type="checkbox" name="' + field + '[' + timestamp + '][value]" = value="1">';
                break;
            default:
                result += '<input type="' + properties.type + '" name="' + field + '[' + timestamp + '][value]" class="form-control input-sm">';
        }

        return result;
    };

    search = new Search({
        addFieldId: '#addFilter',
        formId: '#SearchFilterForm'
    });

    search.init();

})(jQuery);
