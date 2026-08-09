import { describe, it, expect } from 'vitest';
import { CategoryFilter } from './CategoryFilter';
import { CATEGORY_NAV_ITEMS } from '../model/categoryFilterSlice';

describe('CategoryFilter Component', () => {
  it('exports CategoryFilter component function', () => {
    expect(typeof CategoryFilter).toBe('function');
  });

  it('defines valid category navigation items with paths', () => {
    expect(CATEGORY_NAV_ITEMS.length).toBeGreaterThan(0);
    CATEGORY_NAV_ITEMS.forEach((item) => {
      expect(item.label).toBeDefined();
      expect(item.path).toBeDefined();
      expect(item.path.startsWith('/')).toBe(true);
    });
  });
});

