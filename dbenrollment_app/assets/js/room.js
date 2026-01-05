$(document).ready(function () {

  // Remove the new_room parameter from URL after 1 second (after highlight animation)
  if (window.location.href.indexOf('new_room=') > -1) {
    setTimeout(function () {
      // Remove the parameter and reload to show proper order
      window.history.replaceState({}, document.title, 'index.php');
      location.reload();
    }, 2000); // 2 seconds - quick transition to proper order
  }

  // Handle Add Room Form Submission
  $('#roomAddForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
      url: 'add_room.php',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          alert(response.message);
          $('#roomAddModal').modal('hide');
          $('#roomAddForm')[0].reset();
          // Redirect with new room ID to highlight it
          window.location.href = 'index.php?new_room=' + response.room_id;
        } else {
          alert('Error: ' + response.message);
        }
      },
      error: function () {
        alert('An error occurred while adding the room.');
      }
    });
  });

  // Handle Edit Room Form Submission
  $('#roomEditForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
      url: 'update_room.php',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          alert(response.message);
          $('#roomEditModal').modal('hide');
          location.reload();
        } else {
          alert('Error: ' + response.message);
        }
      },
      error: function () {
        alert('An error occurred while updating the room.');
      }
    });
  });

  // Handle Delete Room
  window.deleteRoom = function (roomId) {
    if (confirm('Are you sure you want to delete this room?')) {
      $.ajax({
        url: 'delete.php',
        type: 'POST',
        data: { room_id: roomId },
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
          alert('An error occurred while deleting the room.');
        }
      });
    }
  };

  // Load Room Data
  window.editRoom = function (roomId) {
    $.ajax({
      url: 'get_room.php',
      type: 'GET',
      data: { room_id: roomId },
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          $('#edit_room_id').val(response.data.room_id);
          $('#edit_room_code').val(response.data.room_code);
          $('#edit_building').val(response.data.building);
          $('#edit_capacity').val(response.data.capacity);
          $('#roomEditModal').modal('show');
        } else {
          alert('Error: ' + response.message);
        }
      },
      error: function () {
        alert('An error occurred while loading the room data.');
      }
    });
  };
});