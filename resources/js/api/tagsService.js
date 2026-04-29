import apiClient from './apiClient';

export const tagsService = {
    getTags: () => apiClient.get('/tags'),
    createTag: (payload) => apiClient.post('/tags', payload),
};
