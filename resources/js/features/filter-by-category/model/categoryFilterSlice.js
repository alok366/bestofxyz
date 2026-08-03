import { createSlice } from '@reduxjs/toolkit';

export const CATEGORIES = [
  'All Categories',
  'Programming',
  'AI',
  'System Design',
  'React',
  'Linux',
  'Databases',
];

const initialState = {
  selected: CATEGORIES[0],
};

const categoryFilterSlice = createSlice({
  name: 'filterByCategory',
  initialState,
  reducers: {
    categorySelected: (state, action) => {
      state.selected = action.payload;
    },
  },
});

export const { categorySelected } = categoryFilterSlice.actions;
export const selectSelectedCategory = (state) => state.filterByCategory.selected;
export default categoryFilterSlice.reducer;
