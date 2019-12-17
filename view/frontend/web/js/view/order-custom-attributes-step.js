define(
    [
        'ko',
        'jquery',
        'Magento_Ui/js/form/form',
        'underscore',
        'Magento_Checkout/js/model/step-navigator',
        'uiRegistry'
    ],
    function (
        ko,
        $,
        Component,
        _,
        stepNavigator,
        registry
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Ecomteck_OrderCustomAttributes/order-custom-attributes-step'
            },

            //add here your logic to display step,
            isVisible: ko.observable(false),
            /**
            *
            * @returns {*}
            */
            initialize: function () {
                var self = this;
                this._super();
                stepNavigator.hiddenAll();
                stepNavigator.registerStep(
                    this.code,
                    null,
                    this.title,
                    this.isVisible,

                    _.bind(this.navigate, this),
                    this.stepOrder
                );
                this.stepTitle = ko.observable(this.title);
                this.icon = ko.observable(this.icon);
                this.isVisible(this.visible);
                return this;
            },

            /**
			* The navigate() method is responsible for navigation between checkout step
			* during checkout. You can add custom logic, for example some conditions
			* for switching to your custom step
			*/
            navigate: function (step) {
                step && step.isVisible(true);
            },

            /**
            * @returns void
            */
            navigateToNextStep: function () {
                if(this.validate()){
                    stepNavigator.next();
                }
                
            },
            backStep: function() {
                stepNavigator.back();
            },

            validate : function() {
                this.source.set('params.invalid', false);
                this.source.trigger('order.'+this.code+'.data.validate');
                if(this.source.get('params.invalid')) {
                    this.focusInvalid();
                    return false;
                }
                return true;
            }
        });
    }
);