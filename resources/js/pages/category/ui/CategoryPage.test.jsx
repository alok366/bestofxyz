import { describe, it, expect } from 'vitest';
import { CategoryPage } from './CategoryPage';
import { MOCK_CATEGORY_DETAIL } from '../model/mockData';

describe('CategoryPage', () => {
    it('exports CategoryPage component function', () => {
        expect(typeof CategoryPage).toBe('function');
    });

    it('defines valid category detail mock data', () => {
        expect(MOCK_CATEGORY_DETAIL.title).toBe('Programming');
        expect(MOCK_CATEGORY_DETAIL.breadcrumb.length).toBeGreaterThan(0);
        expect(MOCK_CATEGORY_DETAIL.subcategories.length).toBeGreaterThan(0);
        MOCK_CATEGORY_DETAIL.subcategories.forEach((sub) => {
            expect(sub.title).toBeDefined();
            expect(sub.description).toBeDefined();
        });
    });
});
