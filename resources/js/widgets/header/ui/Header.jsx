import { Logo, Button } from '@shared/ui';
import { CategoryFilter } from '@features/filter-by-category';
import { UserDropdown } from '@features/user-dropdown';
import styles from './Header.module.less';

export const Header = () => (
  <header className={styles.nav}>
    <div className={`${styles.container} ${styles['nav-inner']}`}>
      <a className={styles.brand} href="#">
        <Logo />
        <div>BestFor.dev</div>
      </a>

      <div className={styles.search}>
        <span aria-hidden="true">🔎</span>
        <input placeholder="Search topics, books, courses, tools..." />
      </div>

      <nav className={styles['nav-links']}>
        <a href="#">Browse</a>
        <a href="#">Topics</a>
        <a href="#">Discussions</a>
      </nav>

      <UserDropdown initials="U" />
      <Button variant="primary">Submit Resource</Button>
    </div>

    <div className={styles.container}>
      <CategoryFilter />
    </div>
  </header>
);
