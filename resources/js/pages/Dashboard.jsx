import React, { useEffect } from 'react';
import { useAuthStore } from '../store/useAuthStore';
import { useNoteStore } from '../store/useNoteStore';
import { useTagStore } from '../store/useTagStore';
import NoteList from '../components/NoteList';
import NoteForm from '../components/NoteForm';
import TagForm from '../components/TagForm';

export default function Dashboard() {
    const { user, logout } = useAuthStore();
    const { fetchNotes } = useNoteStore();
    const { fetchTags } = useTagStore();

    useEffect(() => {
        fetchNotes();
        fetchTags();
    }, []);

    const handleLogout = async () => {
        await logout();
        window.location.href = '/app/login';
    };

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Header */}
            <header className="bg-white border-b px-6 py-4 flex items-center justify-between">
                <h1 className="text-lg font-semibold">Renote</h1>
                <div className="flex items-center gap-4">
                    <span className="text-sm text-gray-500">{user?.email}</span>
                    <button
                        onClick={handleLogout}
                        className="text-sm px-4 py-1.5 border rounded-lg hover:bg-gray-50"
                    >
                        Déconnexion
                    </button>
                </div>
            </header>

            {/* Contenu */}
            <main className="max-w-4xl mx-auto px-4 py-8 grid gap-8 md:grid-cols-3">
                {/* Colonne gauche : notes */}
                <div className="md:col-span-2 space-y-6">
                    <NoteForm />
                    <NoteList />
                </div>

                {/* Colonne droite : tags */}
                <div>
                    <TagForm />
                </div>
            </main>
        </div>
    );
}
