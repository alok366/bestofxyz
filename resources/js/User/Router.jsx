/**
 * User SPA Router — mounts all migrated Pages under a single react-router.
 */
import React, { useState, useEffect } from 'react';
import {
  BrowserRouter,
  Routes,
  Route,
  useLocation,
  Navigate,
} from 'react-router-dom';
import { Http } from '@Core/Http';
import { Layout } from './Components/Layout';
import { StatusMessage } from '@Components';
import HomePage from './Pages/Home'; 

const NotFound = () => (
  <StatusMessage
    variant="notfound"
    action={
      <a className="btn btn-primary" href="/home">
        Back to Home
      </a>
    }
  />
);

/**
 * Which engine owns a URL. Drives:
 *   - Http.setMode (request headers)
 *   - Layout (GlobalFilter + sidebar section)
 */
const engineForPath = (path) => 'broadcasts';

/**
 * Route → header title map. Longest-prefix match, so sub-section paths
 * (e.g. /campaign-additional-settings/webhooks) can override the parent
 * label. Pages that want a dynamic title can still write to the h1
 * imperatively, but the static map covers every SPA route today.
 */
const TITLES = [];

const titleForPath = (path) => {
  const match = TITLES.find(
    ([prefix]) => path === prefix || path.startsWith(prefix + '/')
  );
  return match ? match[1] : '';
};

/**
 * Lives inside <BrowserRouter> so it can read the current location via
 * useLocation (the react-router-dom equivalent of preact-router's onChange).
 */
function RouterInner({ bootstrap }) {
  const location = useLocation();
  const [currentPath, setCurrentPath] = useState(location.pathname);

  // Set the mode at mount and on every route change. Running in both
  // places keeps the first render correct (setMode only queues the
  // header change — the first API call from a Page's useEffect
  // happens AFTER mount).
  useEffect(() => {
    Http.setMode(engineForPath(location.pathname));
  }, []);

  useEffect(() => {
    setCurrentPath(location.pathname);
    Http.setMode(engineForPath(location.pathname));
    if (typeof window !== 'undefined') window.scrollTo(0, 0);
  }, [location.pathname]);

  return (
    <Layout
      currentPath={currentPath}
      pageTitle={titleForPath(currentPath)}
      userName={bootstrap.userName || 'User'}
      bootstrap={bootstrap}
      urlEngine={engineForPath(currentPath)}
    >
      <Routes>
        <Route path="/dashboard" element={<HomePage />} />
        <Route path="*" element={<NotFound />} />
      </Routes>
    </Layout>
  );
}

function UserRouter({ bootstrap = {} }) {
  return (
    <BrowserRouter>
      <RouterInner bootstrap={bootstrap} />
    </BrowserRouter>
  );
}

export { UserRouter };
export default UserRouter;