/**
 * Theme state selectors
 * Layer: shared/lib/theme
 */
import { THEME_PREFERENCES, THEMES } from '../../../config/theme/constants.js';

export const selectThemeState = (state) => state.theme;

export const selectThemePreference = (state) =>
  state.theme?.preference ?? THEME_PREFERENCES.SYSTEM;

export const selectResolvedTheme = (state) =>
  state.theme?.resolvedTheme ?? THEMES.DARK;

export const selectIsSystemTheme = (state) =>
  state.theme?.preference === THEME_PREFERENCES.SYSTEM;
