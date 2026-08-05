import React from 'react';
import styles from './Card.module.less';

/**
 * Composable Card component — shared glass-panel wrapper.
 *
 * Sub-components: Card.Header, Card.Body, Card.Footer, Card.Image
 *
 * @param {string}  [as='div']     - HTML element or React component to render as
 * @param {boolean} [lift=false]   - Enable translateY lift on hover
 * @param {string}  [className]    - Extra CSS classes
 * @param {object}  [style]        - Inline styles
 */
const Card = ({ as, lift = false, className = '', children, ...rest }) => {
    const Tag = as || 'div';
    const classes = [
        styles.card,
        lift ? styles.lift : '',
        className,
    ].filter(Boolean).join(' ');

    return <Tag className={classes} {...rest}>{children}</Tag>;
};

/** Card.Header — rendered inside the card as a title / heading area */
const CardHeader = ({ className = '', children, ...rest }) => (
    <div className={`${styles.header} ${className}`.trim()} {...rest}>
        {children}
    </div>
);

/** Card.Body — main content area of the card */
const CardBody = ({ className = '', children, ...rest }) => (
    <div className={`${styles.body} ${className}`.trim()} {...rest}>
        {children}
    </div>
);

/** Card.Footer — bottom area, typically for metadata or actions */
const CardFooter = ({ className = '', children, ...rest }) => (
    <div className={`${styles.footer} ${className}`.trim()} {...rest}>
        {children}
    </div>
);

/** Card.Image — cover image, typically placed at the top of a card */
const CardImage = ({ src, alt = '', className = '', ...rest }) => (
    <img
        className={`${styles.image} ${className}`.trim()}
        src={src}
        alt={alt}
        {...rest}
    />
);

Card.Header = CardHeader;
Card.Body = CardBody;
Card.Footer = CardFooter;
Card.Image = CardImage;

export { Card };
export default Card;
