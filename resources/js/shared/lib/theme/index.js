/**
 * Theme public API
 * Layer: shared/lib/theme
 */

export { useTheme } from './useTheme';
export { ThemeSync } from './ui/ThemeSync';
export {
  themeSlice,
  default as themeReducer,
  setThemePreference,
  systemThemeChanged,
} from './model/themeSlice';
export { themeListenerMiddleware } from './model/themeListenerMiddleware';
export {
  selectThemeState,
  selectThemePreference,
  selectResolvedTheme,
  selectIsSystemTheme,
} from './model/selectors';
export {
  STORAGE_KEY,
  getStoredThemePreference,
  setStoredThemePreference,
  getSystemTheme,
  resolveTheme,
  applyThemeToDocument,
} from './model/themeUtils';
export {
  THEME_PREFERENCES,
  THEMES,
  DEFAULT_THEME_PREFERENCE,
} from '../../config/theme/constants.js';
