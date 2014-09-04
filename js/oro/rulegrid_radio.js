/**
 * @category   Oro
 * @package    Oro_SalesRule
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Adds possibility to use radio buttons in ajax grid widgets
 *
 * @param grid
 * @param element
 * @param checked
 */
VarienRulesForm.prototype.chooserGridRadioCheck = function (grid, element, checked) {
    if (!element.up('th')) {
        var $this = this;
        this.chooserSelectedItems.each(function (pair) { $this.chooserSelectedItems.unset(pair.key) });
        this.chooserSelectedItems.set(element.value,1);
    }

    grid.reloadParams = {'selected[]':this.chooserSelectedItems.keys()};
    this.updateElement.value = this.chooserSelectedItems.keys().join(', ');
}

/**
 * When hiding grid we have to destroy it
 *
 * @param container
 * @param event
 */
VarienRulesForm.prototype.hideChooser = function (container, event) {
    this.toggleChooser(container, event);
}

/**
 * Make sure chooser grid will be destroyed before form submit
 */
document.observe("dom:loaded", function() {
   if (typeof editForm != 'undefined' && typeof varienGlobalEvents != 'undefined') {
       varienGlobalEvents.attachEventHandler('formSubmit', function () {
           if (typeof rule_conditions_fieldset != 'undefined' && $('nestingrule_grid_chooser_promo_quote_grid')) {
               rule_conditions_fieldset.hideChooser($('nestingrule_grid_chooser_promo_quote_grid'));
           }
       });
   }
});
