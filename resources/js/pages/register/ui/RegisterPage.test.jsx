import { describe, it, expect } from 'vitest';
import { RegisterPage } from './RegisterPage';

describe('RegisterPage', () => {
    it('exports RegisterPage component function', () => {
        expect(typeof RegisterPage).toBe('function');
    });
});
