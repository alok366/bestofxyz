/**
 * User SPA Router — mounts all migrated Pages under a single react-router.
 */
import React, { useState, useEffect } from 'react';
import {
  BrowserRouter,
  Routes,
  Route,
  useLocation,
} from 'react-router-dom';
import { Http } from '@shared/api';
import { Layout } from '@widgets/layout';
import { StatusMessage } from '@shared/ui';
import { HomePage } from '@pages/home';

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
 * Which engine owns a URL.
 */
const engineForPath = () => 'broadcasts';

/**
 * Route → header title map.
 */
const TITLES = [];

const titleForPath = (path) => {
  const match = TITLES.find(
    ([prefix]) => path === prefix || path.startsWith(prefix + '/')
  );
  return match ? match[1] : '';
};

function RouterInner({ bootstrap }) {
  const location = useLocation();
  const [currentPath, setCurrentPath] = useState(location.pathname);

  useEffect(() => {
    Http.setMode(engineForPath());
  }, []);

  useEffect(() => {
    setCurrentPath(location.pathname);
    Http.setMode(engineForPath());
    if (typeof window !== 'undefined') window.scrollTo(0, 0);
  }, [location.pathname]);

  return (
    <Layout
      currentPath={currentPath}
      pageTitle={titleForPath(currentPath)}
      userName={bootstrap.userName || 'User'}
      bootstrap={bootstrap}
      urlEngine={engineForPath()}
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
