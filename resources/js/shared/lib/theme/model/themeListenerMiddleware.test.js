import { describe, it, expect, beforeEach } from 'vitest';
import { configureStore } from '@reduxjs/toolkit';
import { THEME_PREFERENCES, THEMES } from '@shared/config/theme';
import themeReducer, {
  setThemePreference,
  systemThemeChanged,
} from './themeSlice';
import { themeListenerMiddleware } from './themeListenerMiddleware';
import { STORAGE_KEY } from './themeUtils';

describe('themeListenerMiddleware', () => {
  let store;

  beforeEach(() => {
    localStorage.clear();
    document.documentElement.removeAttribute('data-theme');
    document.documentElement.style.colorScheme = '';

    store = configureStore({
      reducer: {
        theme: themeReducer,
      },
      middleware: (getDefaultMiddleware) =>
        getDefaultMiddleware().prepend(themeListenerMiddleware.middleware),
    });
  });

  it('persists preference and sets data-theme on setThemePreference', () => {
    store.dispatch(setThemePreference(THEME_PREFERENCES.LIGHT));

    expect(localStorage.getItem(STORAGE_KEY)).toBe(THEME_PREFERENCES.LIGHT);
    expect(document.documentElement.getAttribute('data-theme')).toBe(THEMES.LIGHT);
    expect(document.documentElement.style.colorScheme).toBe(THEMES.LIGHT);

    store.dispatch(setThemePreference(THEME_PREFERENCES.DARK));

    expect(localStorage.getItem(STORAGE_KEY)).toBe(THEME_PREFERENCES.DARK);
    expect(document.documentElement.getAttribute('data-theme')).toBe(THEMES.DARK);
    expect(document.documentElement.style.colorScheme).toBe(THEMES.DARK);
  });

  it('updates document data-theme on systemThemeChanged when preference is system', () => {
    store.dispatch(setThemePreference(THEME_PREFERENCES.SYSTEM));
    expect(localStorage.getItem(STORAGE_KEY)).toBe(THEME_PREFERENCES.SYSTEM);

    store.dispatch(systemThemeChanged(THEMES.DARK));
    expect(document.documentElement.getAttribute('data-theme')).toBe(THEMES.DARK);

    store.dispatch(systemThemeChanged(THEMES.LIGHT));
    expect(document.documentElement.getAttribute('data-theme')).toBe(THEMES.LIGHT);
  });
});
