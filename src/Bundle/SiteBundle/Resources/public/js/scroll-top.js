jQuery(document).ready(
  function () {
    $("#navbar-main a[href^='/pl/#'], #navbar-main a[href^='/en/#']").on('click', function(e) {
       // prevent default anchor click behavior
       e.preventDefault();

       // store hash
       var hash = this.hash;

       // animate
       $('html, body').animate({
           scrollTop: $(this.hash).offset().top
         }, 300, function(){
            // when done, add hash to url
            // (default click behaviour)
            window.location.hash = hash;
      });
    });
  }
);
