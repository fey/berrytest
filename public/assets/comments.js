$(document).ready(function () {
  $(this).on("click", ".answer", function () {
    const linkedForm = $(this).next();
    $('.comment > .answer-form').not(linkedForm).hide();
    linkedForm.toggle();
  });
  $(this).on("submit", ".answer-form", function (event) {
    eventedForm = this;
    const submitButton = $('button[type=submit], input[type=submit]');
    submitButton.prop('disabled', true);
    event.preventDefault();
    const formData = $(this).serialize();
    $.ajax({
      type: "POST",
      url: "/comments",
      data: formData,
      dataType: "text",
      beforeSend: function (response) {
        $(".bg-warning").remove();
      },
      success: function (response) {
        const newComment = $.parseJSON(response);
        $.get('', function (data) {
          const counter = $(data).find('.countComments');
          $('#comment-' + newComment['parent_id']).append($(data).find('#comment-' + newComment['id']));
          $('.countComments').text($(data).find('.countComments').text());
          $(eventedForm).find("textarea").val("");
          $('.comment > .answer-form').hide();
        });
      },
      error: function (response) {
        $.each($.parseJSON(response.responseText), function (index, item) {
          $(eventedForm).find(".comment-" + index).before(function () {
            return `<p class='bg-warning'>Поле ${item} </p>`;
          });
        });
      },
      complete: function (response) {
        submitButton.prop('disabled', false);
      }
    });
  });
});