import { describe, it, expect } from 'vitest';
import { SubmitResourcePage } from './SubmitResourcePage';
import { MOCK_SUBMIT_DATA } from '../model/mockData';

describe('SubmitResourcePage', () => {
    it('exports SubmitResourcePage component function', () => {
        expect(typeof SubmitResourcePage).toBe('function');
    });

    it('defines valid submit mock data with domains and categories', () => {
        expect(MOCK_SUBMIT_DATA.title).toBeDefined();
        expect(MOCK_SUBMIT_DATA.domains.length).toBeGreaterThan(0);
        expect(MOCK_SUBMIT_DATA.existingCategories.length).toBeGreaterThan(0);
        expect(MOCK_SUBMIT_DATA.defaultTags.length).toBeGreaterThan(0);
    });
});
