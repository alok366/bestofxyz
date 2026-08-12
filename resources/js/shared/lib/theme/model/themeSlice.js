/**
 * Pure Theme Redux Slice
 * Layer: shared/lib/theme
 */
import { createSlice } from '@reduxjs/toolkit';
import { THEME_PREFERENCES } from '../../../config/theme/constants.js';
import { getStoredThemePreference, resolveTheme } from './themeUtils.js';

export const getInitialThemeState = () => {
  const preference = getStoredThemePreference();
  return {
    preference,
    resolvedTheme: resolveTheme(preference),
  };
};

export const themeSlice = createSlice({
  name: 'theme',
  initialState: getInitialThemeState,
  reducers: {
    setThemePreference: (state, action) => {
      const nextPreference = action.payload;
      state.preference = nextPreference;
      state.resolvedTheme = resolveTheme(nextPreference);
    },
    systemThemeChanged: (state, action) => {
      if (state.preference === THEME_PREFERENCES.SYSTEM) {
        state.resolvedTheme = action.payload;
      }
    },
  },
});

export const { setThemePreference, systemThemeChanged } = themeSlice.actions;
export default themeSlice.reducer;
