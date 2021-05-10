jQuery(document).ready(function($){
   $('body').on('click', '.toggle-search-options', function(e){
       $('.search-options').toggleClass('active');
       if($('.search-options').hasClass('active')){
           $(this).html('Less Options <i class="fas fa-caret-up"></i>');
       }else{
           $(this).html('More Options <i class="fas fa-caret-down"></i>');
       }
   });
    if($('.search-options').hasClass('active')){
        $(this).html('Less Options <i class="fas fa-caret-up"></i>');
    }else{
        $(this).html('More Options <i class="fas fa-caret-down"></i>');
    }
});