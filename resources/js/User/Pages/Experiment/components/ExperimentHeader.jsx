import React from 'react';
import { NavLink } from 'react-router-dom';
import './ExperimentHeader.less';

export function ExperimentHeader({ currentTitle }) {
  return (
    <header className="exp-header">
      <div className="exp-header__inner">
        <div className="exp-header__brand">
          <NavLink to="/experiment" className="exp-header__logo">
            <span className="exp-header__logo-xyz">bestof</span>
            <span className="exp-header__logo-highlight">xyz</span>
          </NavLink>
          <span className="exp-header__badge">UI Sandbox</span>
        </div>

        <nav className="exp-header__nav">
          <NavLink
            to="/experiment/category-directory"
            className={({ isActive }) =>
              'exp-header__link' + (isActive ? ' is-active' : '')
            }
          >
            <span className="exp-header__num">1</span> Directory
          </NavLink>

          <NavLink
            to="/experiment/top-level-category"
            className={({ isActive }) =>
              'exp-header__link' + (isActive ? ' is-active' : '')
            }
          >
            <span className="exp-header__num">2</span> Top Category
          </NavLink>

          <NavLink
            to="/experiment/subcategory"
            className={({ isActive }) =>
              'exp-header__link' + (isActive ? ' is-active' : '')
            }
          >
            <span className="exp-header__num">3</span> Subcategory
          </NavLink>

          <NavLink
            to="/experiment/resource-detail"
            className={({ isActive }) =>
              'exp-header__link' + (isActive ? ' is-active' : '')
            }
          >
            <span className="exp-header__num">4</span> Resource Detail
          </NavLink>

          <NavLink
            to="/experiment/submit-resource"
            className={({ isActive }) =>
              'exp-header__link' + (isActive ? ' is-active' : '')
            }
          >
            <span className="exp-header__num">5</span> Submit Flow
          </NavLink>

          <NavLink
            to="/experiment/pending-subcategory"
            className={({ isActive }) =>
              'exp-header__link' + (isActive ? ' is-active' : '')
            }
          >
            <span className="exp-header__num">6</span> Pending State
          </NavLink>

          <NavLink
            to="/experiment/team-moderation"
            className={({ isActive }) =>
              'exp-header__link exp-header__link--stretch' + (isActive ? ' is-active' : '')
            }
          >
            <span className="exp-header__num">7</span> Team View
          </NavLink>
        </nav>
      </div>
    </header>
  );
}

export default ExperimentHeader;
