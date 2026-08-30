import { describe, it, expect } from 'vitest';
import { LoginPage } from './LoginPage';

describe('LoginPage', () => {
    it('exports LoginPage component function', () => {
        expect(typeof LoginPage).toBe('function');
    });
});
