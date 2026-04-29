import React, { useState } from 'react';
import { useTagStore } from '../store/useTagStore';

export default function TagForm() {
    const [name, setName] = useState('');
    const [loading, setLoading] = useState(false);
    const { tags, addTag } = useTagStore();

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!name.trim()) return;
        setLoading(true);
        try {
            await addTag({ name });
            setName('');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="bg-white border rounded-lg p-4 space-y-4">
            <h2 className="text-sm font-semibold">Tags</h2>
            <form onSubmit={handleSubmit} className="flex gap-2">
                <input
                    type="text"
                    className="flex-1 border rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-black"
                    placeholder="Nouveau tag..."
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    required
                />
                <button
                    type="submit"
                    disabled={loading}
                    className="bg-black text-white text-sm rounded-lg px-3 py-1.5 hover:bg-gray-800 disabled:opacity-50"
                >
                    +
                </button>
            </form>
            {tags.length > 0 && (
                <ul className="space-y-1">
                    {tags.map((tag) => (
                        <li key={tag.id} className="text-xs text-gray-600 bg-gray-50 rounded-full px-3 py-1 inline-block me-1 mb-1">
                            {tag.name}
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
}
