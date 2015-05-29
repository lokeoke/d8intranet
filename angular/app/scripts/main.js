(function ($) {
  // Use strict mode to avoid errors:
  // https://developer.mozilla.org/en/JavaScript/Strict_mode
  "use strict";

  $(document).ready(function () {
    var siteWrapper = $('html');

    siteWrapper.addClass('light');

    $('.theme-switcher').click(function(){
      if(siteWrapper.hasClass('light')){
        siteWrapper.removeClass('light').addClass('dark');
      }
      else {
        siteWrapper.removeClass('dark').addClass('light');
      }

    });

    var sideBar = $('.sidebar'),
        rollIcon = $('.roll-icon'),
        pageContent = $('.main-content');

    rollIcon.on('click', function(){
      pageContent.toggleClass('roll-bar');
    })

  });
})(jQuery);