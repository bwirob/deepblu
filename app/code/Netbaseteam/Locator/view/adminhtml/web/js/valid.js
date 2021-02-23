define([
    'jquery'
], function($){   
    $(document).ready(function(){
        $('.work-status').change(function(){
            var tar = $(this).parents().eq(1);
            tar.nextAll().toggleClass('hide');
        });

        var statusDay =  $('.work-status');
        $.each(statusDay,function(key,item){
            var status = $(item).val();
            if(status=='1'){
                var taget = $(item).parents().eq(1);
                taget.nextAll().addClass('hide');

                
            }
            
        });

        
       
    });
        
});