/**
 * Layout — app shell widget used by SPA routes.
 *
 * Renders the site header, main content (`children`, i.e. routed page), and footer.
 * Header and footer are injected via props to avoid sideways imports between
 * sibling widget slices — the app layer handles the composition.
 */
import React from 'react';

function Layout({ header, footer, children, currentPath }) {
  return (
    <div className="app-shell" data-path={currentPath}>
      {header}
      <main className="app-content">{children}</main>
      {footer}
    </div>
  );
}

export { Layout };
export default Layout;
