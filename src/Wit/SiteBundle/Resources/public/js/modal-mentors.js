jQuery(document).ready(
  function () {
    $("button.show-mentor").on('click', function() {
      var mentorSelector = $(this).data('target');
      var $mentor = $(mentorSelector);

      var mentorName = $mentor.find(".mentor-name").html();
      var mentorBio = $mentor.find(".biogram").html();
      var mentorPic = $mentor.find(".picture").html();
      var mentorOccupation = $mentor.find('.occupation');

      if (mentorOccupation.length > 0) {
        $(".modal .modal-body .occupation").html(mentorOccupation.html() + '<hr />');
      }

      $(".modal .modal-title").html(mentorName);
      $(".modal .modal-body .photo").html(mentorPic);
      $(".modal .modal-body .bio").html(mentorBio);

      $(".modal").modal();
    });
  }
);
