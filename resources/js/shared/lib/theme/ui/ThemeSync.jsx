/**
 * Theme Synchronization Component
 * Subscribes to OS color-scheme media queries and cross-tab storage changes.
 * Layer: shared/lib/theme
 */
import { useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { THEMES, THEME_PREFERENCES } from '../../../config/theme/constants.js';
import {
  setThemePreference,
  systemThemeChanged,
} from '../model/themeSlice';
import {
  selectThemePreference,
  selectResolvedTheme,
} from '../model/selectors';
import {
  STORAGE_KEY,
  applyThemeToDocument,
} from '../model/themeUtils';

export const ThemeSync = () => {
  const dispatch = useDispatch();
  const preference = useSelector(selectThemePreference);
  const resolvedTheme = useSelector(selectResolvedTheme);

  // Sync DOM with resolvedTheme on mount / change
  useEffect(() => {
    applyThemeToDocument(resolvedTheme);
  }, [resolvedTheme]);

  // Listen to OS system color-scheme changes
  useEffect(() => {
    if (typeof window === 'undefined' || typeof window.matchMedia !== 'function') {
      return undefined;
    }

    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    const handleMediaChange = (event) => {
      const newSystemTheme = event.matches ? THEMES.DARK : THEMES.LIGHT;
      dispatch(systemThemeChanged(newSystemTheme));
    };

    if (typeof mediaQuery.addEventListener === 'function') {
      mediaQuery.addEventListener('change', handleMediaChange);
    } else if (typeof mediaQuery.addListener === 'function') {
      // Fallback for older Safari/WebKit
      mediaQuery.addListener(handleMediaChange);
    }

    return () => {
      if (typeof mediaQuery.removeEventListener === 'function') {
        mediaQuery.removeEventListener('change', handleMediaChange);
      } else if (typeof mediaQuery.removeListener === 'function') {
        mediaQuery.removeListener(handleMediaChange);
      }
    };
  }, [dispatch]);

  // Listen to cross-tab storage events
  useEffect(() => {
    if (typeof window === 'undefined') {
      return undefined;
    }

    const handleStorage = (event) => {
      if (event.key === STORAGE_KEY && event.newValue) {
        const next = event.newValue;
        if (
          next === THEME_PREFERENCES.LIGHT ||
          next === THEME_PREFERENCES.DARK ||
          next === THEME_PREFERENCES.SYSTEM
        ) {
          if (next !== preference) {
            dispatch(setThemePreference(next));
          }
        }
      }
    };

    window.addEventListener('storage', handleStorage);
    return () => {
      window.removeEventListener('storage', handleStorage);
    };
  }, [dispatch, preference]);

  return null;
};
