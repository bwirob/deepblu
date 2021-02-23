
var config = {
    paths: {
        Cmsmart_FAQ:'Netbaseteam_Faq/js/faq',
        Cmsmart_FAQ_Require : 'Netbaseteam_Faq/js/require',
        Cmsmart_FAQ_Contact_Form :'Netbaseteam_Faq/js/contact-form'
    },
    shim: {
            'Cmsmart_FAQ': {
                deps: ['jquery']
            }, 
            'Cmsmart_FAQ_Require': {
                deps: ['jquery']
            },
            'Cmsmart_FAQ_Contact_Form':{
                deps: ['jquery']
            }
            
        }

};



