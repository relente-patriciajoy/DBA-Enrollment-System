$(document).ready(function () {

  // Remove the new_prereq parameter from URL after 1 second (after highlight animation)
  if (window.location.href.indexOf('new_prereq=') > -1) {
    setTimeout(function () {
      // Remove the parameter and reload to show proper order
      window.history.replaceState({}, document.title, 'index.php');
      location.reload();
    }, 3000); // 3 seconds - quick transition to proper order
  }

  // Handle Add Prerequisite Form Submission
  $('#prereqAddForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
      url: 'add_prereq.php',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          alert(response.message);
          $('#prereqAddModal').modal('hide');
          $('#prereqAddForm')[0].reset();
          // Redirect with new prereq ID to highlight it
          window.location.href = 'index.php?new_prereq=' + response.prereq_id;
        } else {
          alert('Error: ' + response.message);
        }
      },
      error: function () {
        alert('An error occurred while adding the prerequisite.');
      }
    });
  });

  // Handle Edit Prerequisite Form Submission
  $('#prereqEditForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
      url: 'update_prereq.php',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          alert(response.message);
          $('#prereqEditModal').modal('hide');
          location.reload();
        } else {
          alert('Error: ' + response.message);
        }
      },
      error: function () {
        alert('An error occurred while updating the prerequisite.');
      }
    });
  });

  // Handle Delete Prerequisite
  window.deletePrereq = function (prereqId) {
    if (confirm('Are you sure you want to delete this prerequisite?')) {
      $.ajax({
        url: 'delete.php',
        type: 'POST',
        data: { prereq_id: prereqId },
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
          alert('An error occurred while deleting the prerequisite.');
        }
      });
    }
  };

  // Load Prerequisite Data for Edit Modal using get_prereq.php
  window.editPrereq = function (prereqId) {
    $.ajax({
      url: 'get_prereq.php',
      type: 'GET',
      data: { prereq_id: prereqId },
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          $('#edit_prereq_id').val(response.data.prereq_id);
          $('#edit_course_id').val(response.data.course_id);
          $('#edit_prerequisite_course_id').val(response.data.prerequisite_course_id);
          $('#prereqEditModal').modal('show');
        } else {
          alert('Error: ' + response.message);
        }
      },
      error: function () {
        alert('An error occurred while loading the prerequisite data.');
      }
    });
  };
});