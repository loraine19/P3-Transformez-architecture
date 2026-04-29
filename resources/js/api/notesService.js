import apiClient from './apiClient';

export const notesService = {
    getNotes: () => apiClient.get('/notes'),
    createNote: (payload) => apiClient.post('/notes', payload),
    deleteNote: (id) => apiClient.delete(`/notes/${id}`),
};
