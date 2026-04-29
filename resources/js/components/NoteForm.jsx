import React, { useState } from 'react';
import { useNoteStore } from '../store/useNoteStore';
import { useTagStore } from '../store/useTagStore';

export default function NoteForm() {
    const [content, setContent] = useState('');
    const [selectedTags, setSelectedTags] = useState([]);
    const [loading, setLoading] = useState(false);
    const addNote = useNoteStore((s) => s.addNote);
    const tags = useTagStore((s) => s.tags);

    const toggleTag = (id) => {
        setSelectedTags((prev) =>
            prev.includes(id) ? prev.filter((t) => t !== id) : [...prev, id]
        );
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!content.trim()) return;
        setLoading(true);
        try {
            await addNote({ content, tag_ids: selectedTags });
            setContent('');
            setSelectedTags([]);
        } finally {
            setLoading(false);
        }
    };

    return (
        <form onSubmit={handleSubmit} className="bg-white border rounded-lg p-4 space-y-3">
            <h2 className="text-sm font-semibold">Nouvelle note</h2>
            <textarea
                className="w-full border rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-black"
                rows={3}
                placeholder="Contenu de la note..."
                value={content}
                onChange={(e) => setContent(e.target.value)}
                required
            />
            {tags.length > 0 && (
                <div className="flex flex-wrap gap-2">
                    {tags.map((tag) => (
                        <button
                            key={tag.id}
                            type="button"
                            onClick={() => toggleTag(tag.id)}
                            className={`text-xs rounded-full px-3 py-1 border transition-colors ${
                                selectedTags.includes(tag.id)
                                    ? 'bg-black text-white border-black'
                                    : 'bg-gray-50 text-gray-600 border-gray-200 hover:border-gray-400'
                            }`}
                        >
                            {tag.name}
                        </button>
                    ))}
                </div>
            )}
            <button
                type="submit"
                disabled={loading}
                className="bg-black text-white text-sm rounded-lg px-4 py-2 hover:bg-gray-800 disabled:opacity-50"
            >
                {loading ? 'Ajout...' : 'Ajouter la note'}
            </button>
        </form>
    );
}
