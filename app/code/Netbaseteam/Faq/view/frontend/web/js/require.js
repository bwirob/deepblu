define(['jquery'], function($){ 
    "use strict";
    
    $(document).ready(function(){
        $('#tags a.tag-links').click(function(){
          $('#faq-loading').show();
          var url = $(this).attr('href');
          var data = {
            'request_type':'tag'
          };
          $.ajax({
                url : url,
                type: 'POST',
                data:data,
                dataType: "json", 
                success: function(result){
                  $('#faq-loading').hide();
                  $('#faq-content .main-column').html(result);

                }
          });

          return false;
        });

        $('#faq-search-form').submit(function(){
          var q_str = $('#faq-search-form .search-box').val();
          if (q_str.length > 0) {
            $('#faq-loading').show();
            var action = $(this).attr('action');
            var data = {
              'q_str':q_str,
              'request_type':'search'
            };
            $.ajax({
                  url : action,
                  type: 'POST',
                  data:data,
                  dataType: "json", 
                  success: function(result){
                    $('#faq-loading').hide();
                    $('#faq-content .main-column').html(result);
                  }
            });
          }
          return false;
        });

        $('.require-category').click(function(){
          $('#faq-loading').show();
          var url = $(this).attr('href');
          var data = {
            'request_type':'category'
          };
          $.ajax({
                url : url,
                type: 'POST',
                data:data,
                dataType: "json", 
                success: function(result){
                  $('#faq-loading').hide();
                  $('#faq-content .main-column').html(result);

                }
          });
          return false;
        });

        $('#form').submit(function(){
          if($(this).validation('isValid')){
            
            $('input#submit').addClass('.not-active');
            var action = $(this).attr('action');
            var data = $( this ).serialize();
            $('#faq-loading').show();
            $.ajax({
                  url : action,
                  type: 'POST',
                  data:data,
                  dataType: "json", 
                  success: function(result){
                    $('input#submit').removeClass('.not-active');
                    $('#mainform').hide();
                    $('#faq-loading').hide();
                    if(result.error&&result.error==1){
                      var html = '<div class="message-error error message"><div data-bind="html: message.text">';
                      html+=result.message;
                      html+='</div>';
                      html+='</div>';
                      $('.messages[data-bind]').eq(1).html(html);
                    }else{
                      var html = '<div class="message-success success message"><div data-bind="html: message.text">';
                      html+=result.message;
                      html+='</div>';
                      html+='</div>';
                      $('.messages[data-bind]').eq(1).html(html);
                    }
                  }
            });
            return false;
          }
        });


    });
});