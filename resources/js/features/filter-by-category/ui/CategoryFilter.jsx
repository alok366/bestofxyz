import { useDispatch, useSelector } from 'react-redux';
import { CATEGORIES, categorySelected, selectSelectedCategory } from '../model/categoryFilterSlice';
import styles from './CategoryFilter.module.less';

export const CategoryFilter = () => {
  const dispatch = useDispatch();
  const selected = useSelector(selectSelectedCategory);

  return (
    <div className={styles.chips}>
      {CATEGORIES.map((category) => (
        <button
          key={category}
          type="button"
          className={`${styles.chip} ${category === selected ? styles.chipActive : ''}`}
          aria-pressed={category === selected}
          onClick={() => dispatch(categorySelected(category))}
        >
          {category}
        </button>
      ))}
    </div>
  );
};
