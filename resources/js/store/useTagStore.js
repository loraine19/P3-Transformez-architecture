import { create } from 'zustand';
import { tagsService } from '../api/tagsService';

export const useTagStore = create((set) => ({
    tags: [],
    loading: false,
    error: null,

    fetchTags: async () => {
        set({ loading: true, error: null });
        try {
            const { data } = await tagsService.getTags();
            set({ tags: data.data, loading: false });
        } catch (err) {
            set({ error: err.response?.data?.message ?? 'Erreur réseau', loading: false });
        }
    },

    addTag: async (payload) => {
        const { data } = await tagsService.createTag(payload);
        set((state) => ({ tags: [...state.tags, data.data] }));
    },
}));
