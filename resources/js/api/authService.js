import apiClient from './apiClient';

export const authService = {
    register: (payload) => apiClient.post('/register', payload),
    login: (payload) => apiClient.post('/login', payload),
    logout: () => apiClient.post('/logout'),
};
