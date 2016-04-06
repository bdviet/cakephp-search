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

    Search.prototype.generateCriteriaFields = function(criteriaFields) {
        if (!$.isEmptyObject(criteriaFields)) {
            $.each(criteriaFields, function(k, v) {
                if ('object' === typeof v) {
                    data = v[Object.keys(v)[0]];
                    that._generateField(k, that.fieldProperties[k], data.value, data.operator);
                }
            });
        }
    };

    Search.prototype.setFieldProperties = function(fieldProperties) {
        this.fieldProperties = fieldProperties;
    };

    Search.prototype.setFieldTypeOperators = function(fieldTypeOperators) {
        this.fieldTypeOperators = fieldTypeOperators;
    };

    Search.prototype._onfieldSelect = function() {
        that = this;
        $(this.addFieldId).change(function() {
            if ('' !== this.value) {
                var props = that.fieldProperties[this.value];
                that._generateField(this.value, props);
                this.value = '';
            }
        });
    };

    Search.prototype._generateField = function(field, properties, value, operator) {
        var timestamp = new Date().getUTCMilliseconds();
        var inputHtml = '';
        inputHtml += '<div class="form-group">';
            inputHtml += this._generateFieldLabel(properties);
            inputHtml += this._generateFieldType(field, properties.type, timestamp);
            inputHtml += '<div class="row">';
                inputHtml += '<div class="col-xs-3">';
                    inputHtml += this._generateFieldOperator(field, properties.type, timestamp, operator);
                inputHtml += '</div>';
                inputHtml += '<div class="col-xs-4">';
                    inputHtml += this._generateFieldInput(field, properties, timestamp, value);
                inputHtml += '</div>';
            inputHtml += '</div>';
        inputHtml += '</div>';
        $(this.formId + ' fieldset').append(inputHtml);
    };

    Search.prototype._generateFieldLabel = function(properties) {
        var result = '';
        result += '<label>' + properties.label + '</label>';

        return result;
    };

    Search.prototype._generateFieldType = function(field, type, timestamp) {
        var result = '';
        result += '<input type="hidden" name="' + field + '[' + timestamp + '][type]" value="' + type + '">';

        return result;
    };

    Search.prototype._generateFieldOperator = function(field, type, timestamp, operator) {
        var result = '';
        if (this.fieldTypeOperators.hasOwnProperty(type)) {
            result += '<select name="' + field + '[' + timestamp + '][operator]" class="form-control input-sm">';
            $.each(this.fieldTypeOperators[type], function(k, v) {
                result += '<option value="' + k + '"';
                if (operator === k) {
                    result += ' selected';
                }
                result += '>';
                result += v + '</option>';
            });
            result += '</select>';
        }

        return result;
    };

    Search.prototype._generateFieldInput = function(field, properties, timestamp, value) {
        var result = '';
        if ('undefined' === typeof value) {
            value = '';
        }
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
                result += '<input type="' + properties.type + '" name="' + field + '[' + timestamp + '][value]"';
                result += ' class="form-control input-sm" value="' + value + '">';
        }

        return result;
    };

    search = new Search({
        addFieldId: '#addFilter',
        formId: '#SearchFilterForm'
    });

    search.init();

})(jQuery);
