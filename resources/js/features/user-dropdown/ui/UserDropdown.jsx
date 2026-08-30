import React, { useState, useRef, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Avatar } from '@shared/ui';
import { useTheme, THEME_PREFERENCES } from '@shared/lib/theme';
import { useAuth } from '@shared/lib/auth';
import styles from './UserDropdown.module.less';

export const UserDropdown = ({ initials: initialsProp }) => {
  const [isOpen, setIsOpen] = useState(false);
  const [isSubmenuOpen, setIsSubmenuOpen] = useState(false);
  const { preference: selectedTheme, setTheme } = useTheme();
  const { user, isAuthenticated, initials: authInitials, logout } = useAuth();

  // Use auth-derived initials when available, fall back to prop
  const initials = isAuthenticated ? authInitials : (initialsProp || 'U');

  const containerRef = useRef(null);
  const triggerRef = useRef(null);
  const themeItemRef = useRef(null);

  // Close dropdown when clicking outside
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (containerRef.current && !containerRef.current.contains(event.target)) {
        setIsOpen(false);
        setIsSubmenuOpen(false);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, []);

  const handleToggleMenu = () => {
    setIsOpen((prev) => {
      const next = !prev;
      if (!next) setIsSubmenuOpen(false);
      return next;
    });
  };

  const handleKeyDownContainer = (e) => {
    if (e.key === 'Escape') {
      if (isSubmenuOpen) {
        setIsSubmenuOpen(false);
        if (themeItemRef.current) themeItemRef.current.focus();
        e.stopPropagation();
      } else if (isOpen) {
        setIsOpen(false);
        if (triggerRef.current) triggerRef.current.focus();
        e.stopPropagation();
      }
    }
  };

  const handleThemeSelect = (theme) => {
    setTheme(theme);
  };

  const handleLogout = () => {
    setIsOpen(false);
    logout();
  };

  return (
    <div
      ref={containerRef}
      className={styles.userDropdownContainer}
      onKeyDown={handleKeyDownContainer}
    >
      <button
        ref={triggerRef}
        type="button"
        className={styles.avatarButton}
        aria-expanded={isOpen}
        aria-haspopup="true"
        aria-label="User menu"
        onClick={handleToggleMenu}
      >
        <Avatar initials={initials} />
      </button>

      {isOpen && (
        <ul className={styles.menu} role="menu" aria-label="User dropdown menu">
          {isAuthenticated ? (
            <>
              <li className={styles.menuItem} role="none">
                <span className={styles.menuItemLabel}>
                  {user?.username || 'User'}
                </span>
              </li>
              <li className={styles.menuDivider} role="separator" />
            </>
          ) : (
            <>
              <li className={styles.menuItem} role="none">
                <Link
                  to="/login"
                  className={styles.menuItemLink}
                  role="menuitem"
                  onClick={() => setIsOpen(false)}
                >
                  Login
                </Link>
              </li>

              <li className={styles.menuItem} role="none">
                <Link
                  to="/register"
                  className={styles.menuItemLink}
                  role="menuitem"
                  onClick={() => setIsOpen(false)}
                >
                  Sign Up
                </Link>
              </li>
            </>
          )}

          <li className={styles.menuItem} role="none">
            <button
              ref={themeItemRef}
              type="button"
              className={`${styles.menuItemButton} ${isSubmenuOpen ? styles.active : ''}`}
              role="menuitem"
              aria-haspopup="true"
              aria-expanded={isSubmenuOpen}
              onClick={() => setIsSubmenuOpen((prev) => !prev)}
            >
              <span className={styles.itemLabel}>Theme</span>
              <span className={styles.chevronIcon} aria-hidden="true">
                ▶
              </span>
            </button>

            {isSubmenuOpen && (
              <ul
                className={styles.submenu}
                role="menu"
                aria-label="Theme options"
              >
                <li className={styles.menuItem} role="none">
                  <button
                    type="button"
                    className={styles.menuItemButton}
                    role="menuitemradio"
                    aria-checked={selectedTheme === THEME_PREFERENCES.LIGHT}
                    onClick={() => handleThemeSelect(THEME_PREFERENCES.LIGHT)}
                  >
                    <span className={styles.itemLabel}>
                      <span
                        className={`${styles.radioIndicator} ${
                          selectedTheme === THEME_PREFERENCES.LIGHT ? styles.selected : ''
                        }`}
                        aria-hidden="true"
                      />
                      Light
                    </span>
                  </button>
                </li>

                <li className={styles.menuItem} role="none">
                  <button
                    type="button"
                    className={styles.menuItemButton}
                    role="menuitemradio"
                    aria-checked={selectedTheme === THEME_PREFERENCES.DARK}
                    onClick={() => handleThemeSelect(THEME_PREFERENCES.DARK)}
                  >
                    <span className={styles.itemLabel}>
                      <span
                        className={`${styles.radioIndicator} ${
                          selectedTheme === THEME_PREFERENCES.DARK ? styles.selected : ''
                        }`}
                        aria-hidden="true"
                      />
                      Dark
                    </span>
                  </button>
                </li>

                <li className={styles.menuItem} role="none">
                  <button
                    type="button"
                    className={styles.menuItemButton}
                    role="menuitemradio"
                    aria-checked={selectedTheme === THEME_PREFERENCES.SYSTEM}
                    onClick={() => handleThemeSelect(THEME_PREFERENCES.SYSTEM)}
                  >
                    <span className={styles.itemLabel}>
                      <span
                        className={`${styles.radioIndicator} ${
                          selectedTheme === THEME_PREFERENCES.SYSTEM ? styles.selected : ''
                        }`}
                        aria-hidden="true"
                      />
                      Match System
                    </span>
                  </button>
                </li>
              </ul>
            )}
          </li>

          {isAuthenticated && (
            <>
              <li className={styles.menuDivider} role="separator" />
              <li className={styles.menuItem} role="none">
                <button
                  type="button"
                  className={styles.menuItemButton}
                  role="menuitem"
                  onClick={handleLogout}
                >
                  <span className={styles.itemLabel}>Log out</span>
                </button>
              </li>
            </>
          )}
        </ul>
      )}
    </div>
  );
};

