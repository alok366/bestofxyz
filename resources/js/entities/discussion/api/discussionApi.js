/**
 * Discussion entity — API calls.
 *
 * Placeholder — will use Http from @shared/api when the real API is ready.
 */
import { DISCUSSIONS } from '../model/fixtures';

/**
 * Fetch discussions for a given topic.
 * Currently returns the dummy fixtures; swap for Http.get() call later.
 */
export const fetchDiscussions = async () => DISCUSSIONS;
