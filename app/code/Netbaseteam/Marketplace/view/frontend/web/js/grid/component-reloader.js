define([
    "uiRegistry"
], function (registry) {
    'use strict';
    return {
        reloadUIComponent: function (gridName, products, param) {
            if (gridName) {
                var params = [];
                var paramName = 'params.'+ param;
                var target = registry.get(gridName);
                if (target && typeof target === 'object') {
                    target.set(paramName, products);
                    target.set('params.t ', Date.now());
                }
            }
        }
    };
});