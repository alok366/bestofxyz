import { createSlice } from '@reduxjs/toolkit';

export const CATEGORY_NAV_ITEMS = [
  { label: 'All Categories', path: '/categories' },
  { label: 'Programming', path: '/categories/programming' },
  { label: 'AI', path: '/categories/ai' },
  { label: 'System Design', path: '/categories/system-design' },
  { label: 'React', path: '/categories/react' },
  { label: 'Linux', path: '/categories/linux' },
  { label: 'Databases', path: '/categories/databases' },
];

export const CATEGORIES = CATEGORY_NAV_ITEMS.map((item) => item.label);

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
