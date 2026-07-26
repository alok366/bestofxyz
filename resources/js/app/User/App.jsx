/**
 * BestofXyz User entry — mounts the react SPA Router into #user-app
 */
import React from 'react';
import { createRoot } from 'react-dom/client';
import UserRouter from './Router';

const spaRoot = document.getElementById('user-app');

if (spaRoot) {
  const root = createRoot(spaRoot);
  root.render(<UserRouter bootstrap={{}} />);
}
