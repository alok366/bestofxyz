import { describe, it, expect } from 'vitest';
import { ResourceDetailPage } from './ResourceDetailPage';
import { MOCK_RESOURCE_DETAIL } from '../model/mockData';

describe('ResourceDetailPage', () => {
    it('exports ResourceDetailPage component function', () => {
        expect(typeof ResourceDetailPage).toBe('function');
    });

    it('defines valid resource detail mock data', () => {
        expect(MOCK_RESOURCE_DETAIL.title).toBeDefined();
        expect(MOCK_RESOURCE_DETAIL.votes).toBeTypeOf('number');
        expect(MOCK_RESOURCE_DETAIL.breadcrumb.length).toBeGreaterThan(0);
        expect(Array.isArray(MOCK_RESOURCE_DETAIL.comments)).toBe(true);
        MOCK_RESOURCE_DETAIL.comments.forEach((comment) => {
            expect(comment.id).toBeDefined();
            expect(comment.author).toBeDefined();
            expect(comment.body).toBeDefined();
            expect(comment.votes).toBeTypeOf('number');
        });
    });
});
