import { requireElementById } from './util.js';

const input_api_key = requireElementById('api_key');
const button_regenerate_api_key = requireElementById('button-regenerate-api-key');
const button_delete_api_key = requireElementById('button-delete-api-key');

button_regenerate_api_key.addEventListener('click', () => input_api_key.value = crypto.randomUUID());
button_delete_api_key.addEventListener('click', () => input_api_key.value = '');