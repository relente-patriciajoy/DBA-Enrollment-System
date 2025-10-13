$(document).ready(function () {

  if (window.location.href.indexOf('new_term=') > -1) {
    setTimeout(function () {
      // Remove the parameter and reload to show proper order
      window.history.replaceState({}, document.title, 'index.php');
      location.reload();
    }, 3000); // timeout to allow user to see the highlight
  }

  // Handle Add Term Form Submission
  $('#termAddForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
      url: 'add_term.php',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          alert(response.message);
          $('#termAddModal').modal('hide');
          $('#termAddForm')[0].reset();
          // Redirect with new term ID to highlight it
          window.location.href = 'index.php?new_term=' + response.term_id;
        } else {
          alert('Error: ' + response.message);
        }
      },
      error: function () {
        alert('An error occurred while adding the term.');
      }
    });
  });

  // Handle Edit Term Form Submission
  $('#termEditForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
      url: 'update_term.php',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          alert(response.message);
          $('#termEditModal').modal('hide');
          location.reload();
        } else {
          alert('Error: ' + response.message);
        }
      },
      error: function () {
        alert('An error occurred while updating the term.');
      }
    });
  });

  // Handle Delete Term
  window.deleteTerm = function (termId) {
    if (confirm('Are you sure you want to delete this term?')) {
      $.ajax({
        url: 'delete_ajax.php',
        type: 'POST',
        data: { term_id: termId },
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            alert(response.message);
            location.reload();
          } else {
            alert('Error: ' + response.message);
          }
        },
        error: function () {
          alert('An error occurred while deleting the term.');
        }
      });
    }
  };

  // Load Term Data for Edit Modal using get_term.php
  window.editTerm = function (termId) {
    $.ajax({
      url: 'get_term.php',
      type: 'GET',
      data: { term_id: termId },
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          $('#edit_term_id').val(response.data.term_id);
          $('#edit_term_code').val(response.data.term_code);
          $('#edit_start_date').val(response.data.start_date);
          $('#edit_end_date').val(response.data.end_date);
          $('#termEditModal').modal('show');
        } else {
          alert('Error: ' + response.message);
        }
      },
      error: function () {
        alert('An error occurred while loading the term data.');
      }
    });
  };
});