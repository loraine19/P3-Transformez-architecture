import React from 'react';
import { useNoteStore } from '../store/useNoteStore';

export default function NoteList() {
    const { notes, loading, error, removeNote } = useNoteStore();

    if (loading) return <p className="text-sm text-gray-400">Chargement des notes...</p>;
    if (error) return <p className="text-sm text-red-500">{error}</p>;
    if (notes.length === 0) return <p className="text-sm text-gray-400">Aucune note pour l'instant.</p>;

    return (
        <ul className="space-y-3">
            {notes.map((note) => (
                <li
                    key={note.id}
                    className="bg-white border rounded-lg px-4 py-3 flex items-start justify-between gap-4"
                >
                    <div>
                        <p className="text-sm">{note.content}</p>
                        {note.tags?.length > 0 && (
                            <div className="flex flex-wrap gap-1 mt-2">
                                {note.tags.map((tag) => (
                                    <span
                                        key={tag.id}
                                        className="text-xs bg-gray-100 text-gray-600 rounded-full px-2 py-0.5"
                                    >
                                        {tag.name}
                                    </span>
                                ))}
                            </div>
                        )}
                    </div>
                    <button
                        onClick={() => removeNote(note.id)}
                        className="text-xs text-red-400 hover:text-red-600 shrink-0"
                        title="Supprimer"
                    >
                        ✕
                    </button>
                </li>
            ))}
        </ul>
    );
}
