import { create } from 'zustand';
import { notesService } from '../api/notesService';

export const useNoteStore = create((set) => ({
    notes: [],
    loading: false,
    error: null,

    fetchNotes: async () => {
        set({ loading: true, error: null });
        try {
            const { data } = await notesService.getNotes();
            set({ notes: data.data, loading: false });
        } catch (err) {
            set({ error: err.response?.data?.message ?? 'Erreur réseau', loading: false });
        }
    },

    addNote: async (payload) => {
        const { data } = await notesService.createNote(payload);
        set((state) => ({ notes: [data.data, ...state.notes] }));
    },

    removeNote: async (id) => {
        await notesService.deleteNote(id);
        set((state) => ({ notes: state.notes.filter((n) => n.id !== id) }));
    },
}));
