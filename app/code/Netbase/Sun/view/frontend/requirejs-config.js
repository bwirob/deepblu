/* var config = {
    map: {
        '*': {
            owlCarousel: 'Netbase_Sun/js/owl.carousel/owl.carousel.min',
			timeCircles: 'Netbase_Sun/js/TimeCircles',
			bootstrap: 'Netbase_Sun/js/bootstrap/js/bootstrap.min'
        }
    }

}; */
var config = {
    paths: {            
            owlCarousel: 'Netbase_Sun/js/owl.carousel/owl.carousel.min',
			TimeCircles: 'Netbase_Sun/js/time-circles',
			bootstrap: 'Netbase_Sun/js/bootstrap/js/bootstrap.min'
        },   
    shim: {
        'owlcarousel': {
            deps: ['jquery']
        },
		'TimeCircles': {
            deps: ['jquery']
        },
		'bootstrap': {
            deps: ['jquery']
        }
    }
};