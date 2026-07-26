import { describe, it, expect } from 'vitest';
import { Http } from './Http';

describe('Http shared api module', () => {
  it('exposes HTTP helper methods', () => {
    expect(typeof Http.get).toBe('function');
    expect(typeof Http.post).toBe('function');
    expect(typeof Http.put).toBe('function');
    expect(typeof Http.delete).toBe('function');
    expect(typeof Http.setMode).toBe('function');
  });
});
