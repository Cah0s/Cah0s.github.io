jQuery(document).ready(function($) { 
  $(".scroll").click(function(event){        
    event.preventDefault();
    $('html,body').animate({scrollTop:$(this.hash).offset().top}, 800);
 });
});

$(document).ready(function() {
  //$('html').niceScroll({ cursorborder: '#006df0', cursorcolor:  '#006df0' });
  $('html').niceScroll({ cursorborder: '#2c3e50', cursorcolor:  '#2c3e50' });
});

// jQuery(document).ready(function($) { 
//   $(".scroll").click(function(event){        
//     event.preventDefault();
//     $('html,body').animate({scrollTop:$(this.hash).offset().top}, 800);
//     $('.scroll').removeClass("active").css({color:'#f9f9f9', background:'transparent'});
//     $(this).addClass("active").css({color:'#191919', background:'#f9f9f9'});
//  });
// });

// $(document).ready(function() {
//   $('html').niceScroll({ cursorborder: '#006df0', cursorcolor:  '#006df0' });
// });