/**
 * Theme utility functions and storage helpers
 * Layer: shared/lib/theme
 */
import {
  THEME_PREFERENCES,
  THEMES,
  DEFAULT_THEME_PREFERENCE,
} from '../../../config/theme/constants.js';

export const STORAGE_KEY = 'xyz_theme';

/**
 * Safely retrieve theme preference from localStorage.
 * @returns {'light' | 'dark' | 'system'}
 */
export const getStoredThemePreference = () => {
  if (typeof window === 'undefined' || !window.localStorage) {
    return DEFAULT_THEME_PREFERENCE;
  }

  try {
    const stored = window.localStorage.getItem(STORAGE_KEY);
    if (
      stored === THEME_PREFERENCES.LIGHT ||
      stored === THEME_PREFERENCES.DARK ||
      stored === THEME_PREFERENCES.SYSTEM
    ) {
      return stored;
    }
  } catch {
    // Storage access may throw in restricted/sandboxed environments
  }

  return DEFAULT_THEME_PREFERENCE;
};

/**
 * Safely persist theme preference to localStorage.
 * @param {'light' | 'dark' | 'system'} preference
 */
export const setStoredThemePreference = (preference) => {
  if (typeof window === 'undefined' || !window.localStorage) {
    return;
  }

  try {
    window.localStorage.setItem(STORAGE_KEY, preference);
  } catch {
    // Storage access may throw in restricted/sandboxed environments
  }
};

/**
 * Detect current system color scheme preference.
 * @returns {'light' | 'dark'}
 */
export const getSystemTheme = () => {
  if (
    typeof window !== 'undefined' &&
    typeof window.matchMedia === 'function' &&
    window.matchMedia('(prefers-color-scheme: dark)').matches
  ) {
    return THEMES.DARK;
  }
  return THEMES.LIGHT;
};

/**
 * Resolve effective theme ('light' or 'dark') based on preference.
 * @param {'light' | 'dark' | 'system'} preference
 * @returns {'light' | 'dark'}
 */
export const resolveTheme = (preference) => {
  if (preference === THEME_PREFERENCES.LIGHT) {
    return THEMES.LIGHT;
  }
  if (preference === THEME_PREFERENCES.DARK) {
    return THEMES.DARK;
  }
  return getSystemTheme();
};

/**
 * Apply resolved theme to HTML root element and meta theme-color tag.
 * @param {'light' | 'dark'} resolvedTheme
 */
export const applyThemeToDocument = (resolvedTheme) => {
  if (typeof document === 'undefined' || !document.documentElement) {
    return;
  }

  document.documentElement.setAttribute('data-theme', resolvedTheme);
  document.documentElement.style.colorScheme = resolvedTheme;

  const themeColorMeta = document.querySelector('meta[name="theme-color"]');
  if (themeColorMeta) {
    themeColorMeta.setAttribute(
      'content',
      resolvedTheme === THEMES.DARK ? '#0d1117' : '#ffffff'
    );
  }
};
