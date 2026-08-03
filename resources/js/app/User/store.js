import { configureStore } from '@reduxjs/toolkit';
import { filterByCategoryReducer } from '@features/filter-by-category';

export const store = configureStore({
  reducer: {
    filterByCategory: filterByCategoryReducer,
  },
});
