/**
 * Layout — app shell widget used by SPA routes.
 *
 * Renders:
 *   - Sidebar (section driven by `urlEngine`)
 *   - Header (page title + user menu)
 *   - GlobalFilter (only shown on engines that need it)
 *   - Main content (`children`, i.e. routed page)
 */
import React, { useState } from 'react';
import { NavLink } from 'react-router-dom';

const NAV_SECTIONS = {
  broadcasts: [
    { to: '/dashboard', label: 'Dashboard' },
  ],
};

const ENGINES_WITH_GLOBAL_FILTER = [];

function Sidebar({ urlEngine }) {
  const items = NAV_SECTIONS[urlEngine] || [];

  return (
    <aside className="app-sidebar">
      <div className="app-sidebar__brand">
        <a href="/dashboard">Your App</a>
      </div>
      <nav className="app-sidebar__nav">
        {items.map(({ to, label }) => (
          <NavLink
            key={to}
            to={to}
            className={({ isActive }) =>
              'app-sidebar__link' + (isActive ? ' is-active' : '')
            }
          >
            {label}
          </NavLink>
        ))}
      </nav>
    </aside>
  );
}

function UserMenu({ userName }) {
  const [open, setOpen] = useState(false);

  return (
    <div className="app-usermenu">
      <button
        type="button"
        className="app-usermenu__trigger"
        onClick={() => setOpen((o) => !o)}
        aria-haspopup="true"
        aria-expanded={open}
      >
        {userName}
      </button>
      {open && (
        <div className="app-usermenu__menu" role="menu">
          <a className="app-usermenu__item" href="/account" role="menuitem">
            Account settings
          </a>
          <a className="app-usermenu__item" href="/logout" role="menuitem">
            Log out
          </a>
        </div>
      )}
    </div>
  );
}

function GlobalFilter({ urlEngine }) {
  if (!ENGINES_WITH_GLOBAL_FILTER.includes(urlEngine)) return null;

  return (
    <div className="app-globalfilter" role="region" aria-label="Filters">
      {/* filter controls go here */}
    </div>
  );
}

function Layout({
  children,
  currentPath,
  pageTitle,
  userName,
  urlEngine,
}) {
  return (
    <div className="app-shell" data-path={currentPath}>
      <Sidebar urlEngine={urlEngine} />

      <div className="app-shell__main">
        <header className="app-header">
          <h1 className="app-header__title">{pageTitle}</h1>
          <UserMenu userName={userName} />
        </header>

        <GlobalFilter urlEngine={urlEngine} />

        <main className="app-content">{children}</main>
      </div>
    </div>
  );
}

export { Layout };
export default Layout;
