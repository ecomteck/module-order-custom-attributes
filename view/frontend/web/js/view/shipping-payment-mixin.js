define(
    [
        'ko'
    ], function (ko) {
        'use strict';

        var mixin = {

            initialize: function () {
                this._super();
                this.isVisible = ko.observable(false); // set visible to be initially false to have your step show first
                return this;
            }
        };

        return function (target) {
            return target.extend(mixin);
        };
    }
);