function toggleEditForm(userId) {
    var editForm = document.getElementById('edit-form-container');
    var editUserId = document.getElementById('edit_user_id');
    editUserId.value = userId;
    if (editForm.style.display === 'none') {
        editForm.style.display = 'block';
    } else {
        editForm.style.display = 'none';
    }
}