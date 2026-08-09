import { describe, it, expect } from 'vitest';
import { AllCategoriesPage } from './AllCategoriesPage';
import { CATEGORY_BOARDS, CATEGORY_DIRECTORY_HEADER } from '../model/mockData';

describe('AllCategoriesPage', () => {
    it('exports AllCategoriesPage component function', () => {
        expect(typeof AllCategoriesPage).toBe('function');
    });

    it('has valid mock data structure', () => {
        expect(CATEGORY_DIRECTORY_HEADER.title).toBeDefined();
        expect(CATEGORY_BOARDS.length).toBeGreaterThan(0);
        CATEGORY_BOARDS.forEach((board) => {
            expect(board.title).toBeDefined();
            expect(board.subcategoriesCount).toBeTypeOf('number');
            expect(board.resourcesCount).toBeTypeOf('number');
            expect(Array.isArray(board.subcategories)).toBe(true);
        });
    });
});
