
const input_uuid = document.getElementById('uuid');
const button_regenerate_uuid = document.getElementById('button-regenerate-uuid');
const button_delete_uuid = document.getElementById('button-delete-uuid');

button_regenerate_uuid.addEventListener('click', () => input_uuid.value = crypto.randomUUID());
button_delete_uuid.addEventListener('click', () => input_uuid.value = '');