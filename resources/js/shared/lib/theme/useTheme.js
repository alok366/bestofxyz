/**
 * React hook for consuming theme state and actions.
 * Layer: shared/lib/theme
 */
import { useCallback } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { THEME_PREFERENCES, THEMES } from '../../config/theme/constants.js';
import { setThemePreference } from './model/themeSlice.js';
import {
  selectThemePreference,
  selectResolvedTheme,
  selectIsSystemTheme,
} from './model/selectors';

export const useTheme = () => {
  const dispatch = useDispatch();
  const preference = useSelector(selectThemePreference);
  const resolvedTheme = useSelector(selectResolvedTheme);
  const isSystem = useSelector(selectIsSystemTheme);

  const setTheme = useCallback(
    (nextPreference) => {
      dispatch(setThemePreference(nextPreference));
    },
    [dispatch]
  );

  return {
    preference,
    resolvedTheme,
    isSystem,
    setTheme,
    THEME_PREFERENCES,
    THEMES,
  };
};
