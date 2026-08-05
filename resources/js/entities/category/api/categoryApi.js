/**
 * Category entity — API calls.
 *
 * Placeholder — will use Http from @shared/api when the real API is ready.
 */
import { CATEGORIES } from '../model/fixtures';

/**
 * Fetch all browsing categories.
 * Currently returns the dummy fixtures; swap for Http.get() call later.
 */
export const fetchCategories = async () => CATEGORIES;
