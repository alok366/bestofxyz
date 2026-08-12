import { describe, it, expect } from 'vitest';
import { THEME_PREFERENCES, THEMES } from '@shared/config/theme';
import themeReducer, {
  setThemePreference,
  systemThemeChanged,
  getInitialThemeState,
} from './themeSlice';

describe('themeSlice pure reducers', () => {
  it('initializes with default state containing preference and resolvedTheme', () => {
    const initialState = getInitialThemeState();
    expect(initialState).toHaveProperty('preference');
    expect(initialState).toHaveProperty('resolvedTheme');
    expect([THEME_PREFERENCES.LIGHT, THEME_PREFERENCES.DARK, THEME_PREFERENCES.SYSTEM]).toContain(
      initialState.preference
    );
    expect([THEMES.LIGHT, THEMES.DARK]).toContain(initialState.resolvedTheme);
  });

  it('updates preference and resolvedTheme on setThemePreference to light', () => {
    const prevState = {
      preference: THEME_PREFERENCES.DARK,
      resolvedTheme: THEMES.DARK,
    };

    const nextState = themeReducer(prevState, setThemePreference(THEME_PREFERENCES.LIGHT));

    expect(nextState.preference).toBe(THEME_PREFERENCES.LIGHT);
    expect(nextState.resolvedTheme).toBe(THEMES.LIGHT);
  });

  it('updates preference and resolvedTheme on setThemePreference to dark', () => {
    const prevState = {
      preference: THEME_PREFERENCES.LIGHT,
      resolvedTheme: THEMES.LIGHT,
    };

    const nextState = themeReducer(prevState, setThemePreference(THEME_PREFERENCES.DARK));

    expect(nextState.preference).toBe(THEME_PREFERENCES.DARK);
    expect(nextState.resolvedTheme).toBe(THEMES.DARK);
  });

  it('updates resolvedTheme on systemThemeChanged when preference is system', () => {
    const prevState = {
      preference: THEME_PREFERENCES.SYSTEM,
      resolvedTheme: THEMES.LIGHT,
    };

    const nextState = themeReducer(prevState, systemThemeChanged(THEMES.DARK));

    expect(nextState.preference).toBe(THEME_PREFERENCES.SYSTEM);
    expect(nextState.resolvedTheme).toBe(THEMES.DARK);
  });

  it('does NOT update resolvedTheme on systemThemeChanged when preference is explicit (light/dark)', () => {
    const prevStateLight = {
      preference: THEME_PREFERENCES.LIGHT,
      resolvedTheme: THEMES.LIGHT,
    };

    const nextStateLight = themeReducer(prevStateLight, systemThemeChanged(THEMES.DARK));
    expect(nextStateLight.resolvedTheme).toBe(THEMES.LIGHT);

    const prevStateDark = {
      preference: THEME_PREFERENCES.DARK,
      resolvedTheme: THEMES.DARK,
    };

    const nextStateDark = themeReducer(prevStateDark, systemThemeChanged(THEMES.LIGHT));
    expect(nextStateDark.resolvedTheme).toBe(THEMES.DARK);
  });
});
