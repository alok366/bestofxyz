import { describe, it, expect } from 'vitest';
import { PendingCategoryPage } from './PendingCategoryPage';
import { MOCK_PENDING_CATEGORY } from '../model/mockData';

describe('PendingCategoryPage', () => {
    it('exports PendingCategoryPage component function', () => {
        expect(typeof PendingCategoryPage).toBe('function');
    });

    it('defines valid pending category mock data with threshold details', () => {
        expect(MOCK_PENDING_CATEGORY.title).toBeDefined();
        expect(MOCK_PENDING_CATEGORY.badge).toBeDefined();
        expect(MOCK_PENDING_CATEGORY.threshold.currentCount).toBeTypeOf('number');
        expect(MOCK_PENDING_CATEGORY.threshold.targetCount).toBeTypeOf('number');
        expect(MOCK_PENDING_CATEGORY.threshold.percentage).toBeTypeOf('number');
        expect(MOCK_PENDING_CATEGORY.resources.length).toBeGreaterThan(0);
        MOCK_PENDING_CATEGORY.resources.forEach((res) => {
            expect(res.rank).toBeTypeOf('number');
            expect(res.votes).toBeTypeOf('number');
            expect(res.title).toBeDefined();
        });
    });
});
