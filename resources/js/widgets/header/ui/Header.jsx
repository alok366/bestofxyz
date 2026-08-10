import { Link } from 'react-router-dom';
import { Logo, Button } from '@shared/ui';
import { CategoryFilter } from '@features/filter-by-category';
import { UserDropdown } from '@features/user-dropdown';
import styles from './Header.module.less';

export const Header = () => (
  <header className={styles.nav}>
    <div className={`${styles.container} ${styles['nav-inner']}`}>
      <Link className={styles.brand} to="/">
        <Logo />
        <div>BestFor.dev</div>
      </Link>

      <div className={styles.search}>
        <span aria-hidden="true">🔎</span>
        <input placeholder="Search topics, books, courses, tools..." />
      </div>

      <nav className={styles['nav-links']}>
        <Link to="/categories">Categories</Link>
        <a href="#">Topics</a>
        <a href="#">Discussions</a>
      </nav>

      <UserDropdown initials="U" />
      <Link to="/submit">
        <Button variant="primary">Submit Resource</Button>
      </Link>
    </div>

    <div className={styles.container}>
      <CategoryFilter />
    </div>
  </header>
);
