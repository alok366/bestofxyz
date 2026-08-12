import { configureStore } from '@reduxjs/toolkit';
import { filterByCategoryReducer } from '@features/filter-by-category';
import { themeReducer, themeListenerMiddleware } from '@shared/lib/theme';

export const store = configureStore({
  reducer: {
    filterByCategory: filterByCategoryReducer,
    theme: themeReducer,
  },
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware().prepend(themeListenerMiddleware.middleware),
});
