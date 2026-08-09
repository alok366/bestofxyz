import React from 'react';
import { NavLink } from 'react-router-dom';
import { CATEGORY_NAV_ITEMS } from '../model/categoryFilterSlice';
import styles from './CategoryFilter.module.less';

export const CategoryFilter = () => (
  <nav className={styles.chips} aria-label="Category navigation">
    {CATEGORY_NAV_ITEMS.map(({ label, path }) => (
      <NavLink
        key={label}
        to={path}
        className={({ isActive }) =>
          `${styles.chip} ${isActive ? styles.chipActive : ''}`
        }
      >
        {label}
      </NavLink>
    ))}
  </nav>
);
