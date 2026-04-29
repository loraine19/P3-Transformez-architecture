import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuthStore } from '../store/useAuthStore';

export default function Register() {
    const [form, setForm] = useState({ name: '', email: '', password: '', password_confirmation: '' });
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(false);
    const register = useAuthStore((s) => s.register);
    const navigate = useNavigate();

    const handleChange = (e) => setForm((f) => ({ ...f, [e.target.name]: e.target.value }));

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError(null);
        setLoading(true);
        try {
            await register(form);
            navigate('/dashboard');
        } catch (err) {
            const errors = err.response?.data?.data;
            if (errors) {
                const first = Object.values(errors)[0];
                setError(Array.isArray(first) ? first[0] : first);
            } else {
                setError(err.response?.data?.message ?? 'Erreur lors de l\'inscription');
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-gray-50 px-4">
            <div className="w-full max-w-md bg-white rounded-xl shadow p-8">
                <h1 className="text-2xl font-semibold mb-6 text-center">Créer un compte</h1>
                {error && (
                    <div className="mb-4 text-red-600 text-sm text-center">{error}</div>
                )}
                <form onSubmit={handleSubmit} className="space-y-4">
                    {[
                        { label: 'Nom', name: 'name', type: 'text' },
                        { label: 'Email', name: 'email', type: 'email' },
                        { label: 'Mot de passe', name: 'password', type: 'password' },
                        { label: 'Confirmer le mot de passe', name: 'password_confirmation', type: 'password' },
                    ].map(({ label, name, type }) => (
                        <div key={name}>
                            <label className="block text-sm font-medium mb-1">{label}</label>
                            <input
                                type={type}
                                name={name}
                                className="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black"
                                value={form[name]}
                                onChange={handleChange}
                                required
                            />
                        </div>
                    ))}
                    <button
                        type="submit"
                        disabled={loading}
                        className="w-full bg-black text-white rounded-lg py-2 text-sm font-medium hover:bg-gray-800 disabled:opacity-50"
                    >
                        {loading ? 'Inscription...' : 'S\'inscrire'}
                    </button>
                </form>
                <p className="mt-4 text-sm text-center text-gray-500">
                    Déjà un compte ?{' '}
                    <Link to="/login" className="underline">
                        Se connecter
                    </Link>
                </p>
            </div>
        </div>
    );
}
