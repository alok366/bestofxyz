import styles from './Button.module.less';

export const Button = ({ variant = 'default', className = '', children, ...rest }) => {
  const classes = [styles.btn, variant === 'primary' ? styles.primary : '', className]
    .filter(Boolean)
    .join(' ');

  return (
    <button type="button" className={classes} {...rest}>
      {children}
    </button>
  );
};
