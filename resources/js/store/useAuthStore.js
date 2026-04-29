import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import { authService } from '../api/authService';

export const useAuthStore = create(
    persist(
        (set) => ({
            user: null,
            token: null,
            isAuthenticated: false,

            login: async (credentials) => {
                const { data } = await authService.login(credentials);
                const { token, user } = data.data;
                set({ token, user, isAuthenticated: true });
            },

            register: async (payload) => {
                const { data } = await authService.register(payload);
                const { token, user } = data.data;
                set({ token, user, isAuthenticated: true });
            },

            logout: async () => {
                try {
                    await authService.logout();
                } catch (_) {
                    // ignorer si le token est déjà invalide
                } finally {
                    set({ token: null, user: null, isAuthenticated: false });
                }
            },
        }),
        {
            name: 'auth-storage',
            partialize: (state) => ({ token: state.token, user: state.user, isAuthenticated: state.isAuthenticated }),
        }
    )
);
