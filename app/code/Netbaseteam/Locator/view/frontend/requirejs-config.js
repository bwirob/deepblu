
var config = {
    paths: {
        Cmsmart_Localtor:'Netbaseteam_Locator/js/cmsmart_localtor',
        localtor_init:'Netbaseteam_Locator/js/localtor_init',
        localtor_creator : 'Netbaseteam_Locator/js/localtor_creator',
        Cmsmart_Store : 'Netbaseteam_Locator/js/cmsmart_store'

    },
    shim: {
            'Cmsmart_Localtor': {
                deps: ['jquery']
            },
            'localtor_init': {
                deps: ['jquery']
            },
            'localtor_creator': {
                deps: ['jquery']
            },
            'Cmsmart_Store': {
                deps: ['jquery']
            }
            
        }

};



