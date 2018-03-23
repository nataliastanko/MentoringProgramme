/* ========================================================================
 * Count menu header offset
 * Scroll to focused form field
 * ======================================================================== */

jQuery(document).ready(
  function () {
    $('input, textarea, select').focus(function () {
      var element = $(this);

      var mainMenuOffset = $('.navbar').height();

      if ($('.nav-account').length) {
        var navAdminOffset = $('.nav-account').height();
      } else {
        var navAdminOffset = 0;
      }
      if ($('.submenu').length) {
        var navAdminSubmenuOffset = $('.submenu').height();
      } else {
        var navAdminSubmenuOffset = 0;
      }

      // headerOffset is height of navbar + input label
      var headerOffset = mainMenuOffset + navAdminOffset + navAdminSubmenuOffset + 35;
      if (element.offset().top - $(window).scrollTop() < headerOffset)
      {
        $('html, body').animate({
            scrollTop: element.offset().top - headerOffset
        }, 0);
      }
    });
  }
);
