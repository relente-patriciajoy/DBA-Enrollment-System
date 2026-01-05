$(function () {
  console.log('program.js loaded');

  // ADD PROGRAM
  $('#addProgramForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: 'add.php',
      method: 'POST',
      data: $(this).serialize(),
      dataType: 'json'
    }).done(function (resp) {
      if (resp.success) {
        $('#addModal').modal('hide');
        $('#addProgramForm')[0].reset();
        alert('Program added');

        const newRow = `
          <tr class="new-row">
            <td>${resp.program_code}</td>
            <td>${resp.program_name}</td>
            <td>${resp.dept_name || ''}</td>
            <td class="text-center">
              <button class="btn btn-sm btn-warning edit-program" data-id="${resp.program_id}">Edit</button>
              <button class="btn btn-sm btn-danger delete-program" data-id="${resp.program_id}">Delete</button>
            </td>
          </tr>`;
        $('table tbody').prepend(newRow);
      } else {
        alert(resp.error || 'Failed to add program');
        console.error('add.php response', resp);
      }
    }).fail(function (xhr, status, err) {
      alert('Server error adding program — see console.');
      console.error('add_ajax fail', status, err, xhr.responseText);
    });
  });

  // LOAD program into edit modal (delegated)
  $(document).on('click', '.edit-program', function (e) {
    e.preventDefault();
    let id = $(this).data('id');
    console.log("Edit clicked for id:", id);

    // Show loading message if it exists
    if ($('#loadingMsg').length) $('#loadingMsg').show();

    $.ajax({
      url: 'get_program.php',
      type: 'GET',
      data: { id: id },
      dataType: 'json'
    }).done(function (resp) {  // Use .done() for consistency with other handlers
      console.log(resp);
      if (resp.success) {
        $('#edit_program_id').val(resp.data.program_id);
        $('#edit_program_code').val(resp.data.program_code);
        $('#edit_program_name').val(resp.data.program_name);
        $('#edit_dept_id').val(resp.data.dept_id || '');  // Fallback to empty
        $('#editModal').modal('show');
        $('#loadingMsg').hide();  // Hide loading
      } else {
        alert(resp.error || 'Error loading program details');
        $('#loadingMsg').hide();
      }
    }).fail(function (xhr, status, error) {
      console.log("AJAX Error:", error, xhr.responseText);
      alert('Error loading program details: ' + (xhr.responseJSON?.error || error));
      $('#loadingMsg').hide();
    });
  });

  // UPDATE program
  $('#editProgramForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: 'update.php',
      method: 'POST',
      data: $(this).serialize(),
      dataType: 'json'
    }).done(function (resp) {
      if (resp.success && resp.data) {
        $('#editModal').modal('hide');
        alert('Program updated');

        // Replace the row that had this program id (delegated selector)
        const id = resp.data.program_id;
        const $oldRow = $(`.edit-program[data-id='${id}']`).closest('tr');
        const newRow = `
          <tr class="new-row">
            <td>${resp.data.program_code}</td>
            <td>${resp.data.program_name}</td>
            <td>${resp.data.dept_name || ''}</td>
            <td class="text-center">
              <button class="btn btn-sm btn-warning edit-program" data-id="${id}">Edit</button>
              <button class="btn btn-sm btn-danger delete-program" data-id="${id}">Delete</button>
            </td>
          </tr>`;
        $oldRow.replaceWith(newRow);

        // temporary highlight
        const $nr = $('table tbody tr').first();
        setTimeout(() => $nr.addClass('fade-out'), 500);
        setTimeout(() => $nr.removeClass('new-row fade-out'), 2500);
      } else {
        alert(resp.error || 'Failed to update program');
        console.error('update.php resp', resp);
      }
    }).fail(function (xhr, status, err) {
      alert('Server error updating program — see console.');
      console.error('update_ajax fail', status, err, xhr.responseText);
    });
  });

  // DELETE program (delegated)
  $(document).on('click', '.delete-program', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    if (!confirm('Are you sure you want to delete this program?')) return;

    $.ajax({
      url: 'delete.php',
      method: 'POST',
      data: { program_id: id },
      dataType: 'json'
    }).done(function (resp) {
      if (resp.success) {
        $(`.delete-program[data-id='${id}']`).closest('tr').fadeOut();
      } else {
        alert(resp.error || 'Failed to delete program');
        console.error('delete.php resp', resp);
      }
    }).fail(function (xhr, status, err) {
      alert('Server error deleting program — see console.');
      console.error('delete_ajax fail', status, err, xhr.responseText);
    });
  });

});
