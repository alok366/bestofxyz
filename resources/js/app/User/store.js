import { configureStore } from '@reduxjs/toolkit';
import { filterByCategoryReducer } from '@features/filter-by-category';
import { themeReducer, themeListenerMiddleware } from '@shared/lib/theme';
import { authReducer, authListenerMiddleware } from '@shared/lib/auth';

export const store = configureStore({
  reducer: {
    auth: authReducer,
    filterByCategory: filterByCategoryReducer,
    theme: themeReducer,
  },
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware()
      .prepend(themeListenerMiddleware.middleware)
      .prepend(authListenerMiddleware.middleware),
});
