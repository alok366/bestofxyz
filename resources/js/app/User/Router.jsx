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
import { Header } from '@widgets/header';
import { Footer } from '@widgets/footer';
import { StatusMessage } from '@shared/ui';
import { HomePage } from '@pages/home';
import { AllCategoriesPage } from '@pages/allcategories';
import { CategoryPage } from '@pages/category';
import { ResourceDetailPage } from '@pages/resource';
import { SubmitResourcePage } from '@pages/submit';
import { PendingCategoryPage } from '@pages/pending';
import { ThemeSync } from '@shared/lib/theme';
import { StoreProvider } from './providers/StoreProvider';

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

function RouterInner() {
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
    <Layout currentPath={currentPath} header={<Header />} footer={<Footer />}>
      <Routes>
        <Route path="/" element={<HomePage />} />
        <Route path="/dashboard" element={<HomePage />} />
        <Route path="/categories" element={<AllCategoriesPage />} />
        <Route path="/categories/:slug" element={<CategoryPage />} />
        <Route path="/categories/:catSlug/resources/:resSlug" element={<ResourceDetailPage />} />
        <Route path="/submit" element={<SubmitResourcePage />} />
        <Route path="/pending/:slug" element={<PendingCategoryPage />} />
        <Route path="*" element={<NotFound />} />
      </Routes>
    </Layout>
  );
}

function UserRouter() {
  return (
    <StoreProvider>
      <ThemeSync />
      <BrowserRouter>
        <RouterInner />
      </BrowserRouter>
    </StoreProvider>
  );
}

export { UserRouter };
export default UserRouter;
