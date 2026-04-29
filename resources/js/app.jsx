import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import Router from './router';
import './index.css';

ReactDOM.createRoot(document.getElementById('app')).render(
    <React.StrictMode>
        <BrowserRouter basename="/app">
            <Router />
        </BrowserRouter>
    </React.StrictMode>
);
