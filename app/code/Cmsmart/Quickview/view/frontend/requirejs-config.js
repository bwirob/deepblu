/* var config = {
    map: {
        '*': {
            magnificPopup: 'Cmsmart_Quickview/js/lib/jquery.magnific-popup.min',
            cmsmartQuickview: 'Cmsmart_Quickview/js/quickview'
        }
    },
    shim: {
        magnificPopup: {
            deps: ['jquery']
        }
    }
}; */
var config = {
    paths: {            
           magnificPopup: 'Cmsmart_Quickview/js/lib/jquery.magnific-popup.min',
           cmsmartQuickview: 'Cmsmart_Quickview/js/quickview'
        },   
    shim: {
        'magnificPopup': {
            deps: ['jquery']
        },
		'cmsmartQuickview': {
            deps: ['jquery']
        }
    }
};