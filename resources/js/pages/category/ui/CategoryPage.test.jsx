import { describe, it, expect } from 'vitest';
import { CategoryPage } from './CategoryPage';
import { MOCK_CATEGORY_DETAIL } from '../model/mockData';

describe('CategoryPage', () => {
    it('exports CategoryPage component function', () => {
        expect(typeof CategoryPage).toBe('function');
    });

    it('defines valid category detail mock data with ranked resources', () => {
        expect(MOCK_CATEGORY_DETAIL.title).toBeDefined();
        expect(MOCK_CATEGORY_DETAIL.breadcrumb.length).toBeGreaterThan(0);
        expect(MOCK_CATEGORY_DETAIL.resources.length).toBeGreaterThan(0);
        MOCK_CATEGORY_DETAIL.resources.forEach((res) => {
            expect(res.rank).toBeTypeOf('number');
            expect(res.votes).toBeTypeOf('number');
            expect(res.title).toBeDefined();
            expect(Array.isArray(res.tags)).toBe(true);
        });
    });
});
