varienGridMassaction.addMethods({
    apply: function() {
        if(varienStringArray.count(this.checkedString) == 0) {
                alert(this.errorText);
                return;
            }

        var item = this.getSelectedItem();
        if(!item) {
            this.validator.validate();
            return;
        }
        this.currentItem = item;
        var fieldName = (item.field ? item.field : this.formFieldName);
        var fieldsHtml = '';

        if(this.currentItem.confirm && !window.confirm(this.currentItem.confirm)) {
            return;
        }

        this.formHiddens.update('');

        var shippingCost = new Hash();
        var trackingNo   = new Hash();

        var trackingNoFields = $$('#'+this.grid.containerId+' input.tracking_number');
        if (trackingNoFields.length) {
            var tableId = this.grid.containerId + this.grid.tableSufix;
            var rowCounter = 0;
            $$('#'+tableId+' tr').each(function(tableRow){
                rowCounter++;
                /**
                 * Heading and Filters
                 */
                if (rowCounter <= 2) {
                    return;
                }

                var selected = false;
                var objectId = 0;

                Element.select($(tableRow), 'input').each(function(inputElm){
                    if ($(inputElm).isMassactionCheckbox) {
                        selected = $(inputElm).checked;
                        objectId = $(inputElm).value;
                    } else {
                        if (selected) {
                            if ($(inputElm).readAttribute('name') == 'tracking_number') {
                                trackingNo.set(objectId, ($(inputElm).value) ? $(inputElm).value : '');
                            }
                        }
                    }
                });
            });

            trackingNo.each(function(value){
                var fieldName = 'tracking_number[' + value.key + ']';
                new Insertion.Bottom(this.formHiddens, this.fieldTemplate.evaluate({name: fieldName, value: value.value}));
            }.bind(this));
        }


        var shippingCostFields = $$('#'+this.grid.containerId+' input.shipping_cost');

        if (shippingCostFields.length) {
            var tableId = this.grid.containerId + this.grid.tableSufix;
            var rowCounter = 0;
            $$('#'+tableId+' tr').each(function(tableRow){
                rowCounter++;
                /**
                 * Heading and Filters
                 */
                if (rowCounter <= 2) {
                    return;
                }

                var selected = false;
                var objectId = 0;

                Element.select($(tableRow), 'input').each(function(inputElm){
                    if ($(inputElm).isMassactionCheckbox) {
                        selected = $(inputElm).checked;
                        objectId = $(inputElm).value;
                    } else {
                        if (selected) {
                            if ($(inputElm).readAttribute('name') == 'base_shipping_cost') {
                                shippingCost.set(objectId, ($(inputElm).value) ? $(inputElm).value : 0);
                                //shippingCost[objectId] = ($(inputElm).value) ? $(inputElm).value : 0;
                            }
                        }
                    }
                });
            });

            shippingCost.each(function(value){
                var fieldName = 'base_shipping_cost[' + value.key + ']';
                new Insertion.Bottom(this.formHiddens, this.fieldTemplate.evaluate({name: fieldName, value: value.value}));
            }.bind(this));
        }

        new Insertion.Bottom(this.formHiddens, this.fieldTemplate.evaluate({name: fieldName, value: this.checkedString}));
        new Insertion.Bottom(this.formHiddens, this.fieldTemplate.evaluate({name: 'massaction_prepare_key', value: fieldName}));

        if(!this.validator.validate()) {
            return;
        }

        if(this.useAjax && item.url) {
            new Ajax.Request(item.url, {
                'method': 'post',
                'parameters': this.form.serialize(true),
                'onComplete': this.onMassactionComplete.bind(this)
            });
        } else if(item.url) {
            this.form.action = item.url;
            this.form.submit();
        }
    }

});