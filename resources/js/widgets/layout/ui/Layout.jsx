/**
 * Layout — app shell widget used by SPA routes.
 *
 * Renders the site header, main content (`children`, i.e. routed page), and footer.
 */
import React from 'react';
import { Header } from '@widgets/header';
import { Footer } from '@widgets/footer';

function Layout({ children, currentPath }) {
  return (
    <div className="app-shell" data-path={currentPath}>
      <Header />
      <main className="app-content">{children}</main>
      <Footer />
    </div>
  );
}

export { Layout };
export default Layout;
