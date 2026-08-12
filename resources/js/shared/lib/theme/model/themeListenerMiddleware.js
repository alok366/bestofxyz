/**
 * Theme Listener Middleware for DOM and Storage Side-Effects
 * Layer: shared/lib/theme
 */
import { createListenerMiddleware, isAnyOf } from '@reduxjs/toolkit';
import { setThemePreference, systemThemeChanged } from './themeSlice';
import { selectThemeState } from './selectors';
import { setStoredThemePreference, applyThemeToDocument } from './themeUtils';

export const themeListenerMiddleware = createListenerMiddleware();

themeListenerMiddleware.startListening({
  matcher: isAnyOf(setThemePreference, systemThemeChanged),
  effect: (_action, listenerApi) => {
    const themeState = selectThemeState(listenerApi.getState());
    if (themeState) {
      setStoredThemePreference(themeState.preference);
      applyThemeToDocument(themeState.resolvedTheme);
    }
  },
});
