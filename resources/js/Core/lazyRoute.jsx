import { lazy, Suspense } from 'react';

const Home = lazy(() => import('./Home'));

function App() {
  return (
    <Suspense fallback={null}>
      <Home />
    </Suspense>
  );
}