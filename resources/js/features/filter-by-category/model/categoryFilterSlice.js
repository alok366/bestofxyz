import { createSlice } from '@reduxjs/toolkit';

export const CATEGORY_NAV_ITEMS = [
  { label: 'All Categories', path: '/categories' },
  { label: 'Programming', path: '/category/programming' },
  { label: 'AI', path: '/category/ai' },
  { label: 'System Design', path: '/category/system-design' },
  { label: 'React', path: '/category/react' },
  { label: 'Linux', path: '/category/linux' },
  { label: 'Databases', path: '/category/databases' },
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
