/**
 * Layout — app shell used by every SPA route.
 *
 * Renders:
 *   - Sidebar (section driven by `urlEngine`)
 *   - Header (page title + user menu)
 *   - GlobalFilter (only shown on engines that need it — adjust the list
 *     below to match your real engines)
 *   - Main content (`children`, i.e. the routed Page)
 *
 * Props (as passed from router.js):
 *   currentPath  — string, current pathname, used to highlight nav links
 *   pageTitle    — string, shown in the header <h1>
 *   userName     — string, shown in the user menu
 *   bootstrap    — object, whatever server-rendered bootstrap data you pass in
 *   urlEngine    — string, e.g. 'broadcasts' — decides sidebar section + GlobalFilter
 *   children     — the routed page content
 */
import React, { useState } from 'react';
import { NavLink } from 'react-router-dom';

// Adjust to your real navigation. Grouped by engine so the sidebar can
// swap sections when `urlEngine` changes without needing separate Layouts.
const NAV_SECTIONS = {
  broadcasts: [
    { to: '/dashboard', label: 'Dashboard' },
    // add more broadcasts-engine routes here
  ],
};

// Engines that should show the GlobalFilter bar under the header.
// Empty by default — flip this on per engine as you wire it up.
const ENGINES_WITH_GLOBAL_FILTER = [];

function Sidebar({ urlEngine, currentPath }) {
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

  // Placeholder implementation — replace with your real filter controls
  // (date range, list/segment picker, etc.) once wired to actual data.
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
  bootstrap,
  urlEngine,
}) {
  return (
    <div className="app-shell">
      <Sidebar urlEngine={urlEngine} currentPath={currentPath} />

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