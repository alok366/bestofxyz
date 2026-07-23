/**
 * User SPA Router — mounts all migrated Pages & Experiment UI/UX Sandbox pages.
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

// Experiment Pages
import ExperimentIndex from './Pages/Experiment/index';
import CategoryDirectoryMock from './Pages/Experiment/category-directory/CategoryDirectoryMock';
import TopLevelCategoryMock from './Pages/Experiment/top-level-category/TopLevelCategoryMock';
import SubcategoryMock from './Pages/Experiment/subcategory/SubcategoryMock';
import ResourceDetailMock from './Pages/Experiment/resource-detail/ResourceDetailMock';
import SubmitResourceMock from './Pages/Experiment/submit-resource/SubmitResourceMock';
import PendingSubcategoryMock from './Pages/Experiment/pending-subcategory/PendingSubcategoryMock';
import TeamModerationMock from './Pages/Experiment/team-moderation/TeamModerationMock';

const NotFound = () => (
  <StatusMessage
    variant="notfound"
    action={
      <a className="btn btn-primary" href="/experiment">
        Back to Experiment Index
      </a>
    }
  />
);

const engineForPath = (path) => 'broadcasts';

function RouterInner({ bootstrap }) {
  const location = useLocation();
  const [currentPath, setCurrentPath] = useState(location.pathname);

  useEffect(() => {
    Http.setMode(engineForPath(location.pathname));
  }, []);

  useEffect(() => {
    setCurrentPath(location.pathname);
    Http.setMode(engineForPath(location.pathname));
    if (typeof window !== 'undefined') window.scrollTo(0, 0);
  }, [location.pathname]);

  const isExperimentRoute = location.pathname.startsWith('/experiment');

  if (isExperimentRoute) {
    return (
      <Routes>
        <Route path="/experiment" element={<ExperimentIndex />} />
        <Route path="/experiment/category-directory" element={<CategoryDirectoryMock />} />
        <Route path="/experiment/top-level-category" element={<TopLevelCategoryMock />} />
        <Route path="/experiment/subcategory" element={<SubcategoryMock />} />
        <Route path="/experiment/resource-detail" element={<ResourceDetailMock />} />
        <Route path="/experiment/submit-resource" element={<SubmitResourceMock />} />
        <Route path="/experiment/pending-subcategory" element={<PendingSubcategoryMock />} />
        <Route path="/experiment/team-moderation" element={<TeamModerationMock />} />
      </Routes>
    );
  }

  return (
    <Layout
      currentPath={currentPath}
      pageTitle="bestofxyz"
      userName={bootstrap.userName || 'User'}
      bootstrap={bootstrap}
      urlEngine={engineForPath(currentPath)}
    >
      <Routes>
        <Route path="/" element={<Navigate to="/experiment" replace />} />
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