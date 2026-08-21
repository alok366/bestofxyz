import React, { useState } from 'react';
import { Card, Button } from '@shared/ui';
import { bestofxyz } from '@shared/api/bestofxyz';
import styles from './SubmitResourceForm.module.less';

/**
 * SubmitResourceForm — interactive form for submitting a new resource
 * to an existing category or proposing a new category.
 *
 * @param {object} props
 * @param {string[]} [props.domains=[]] - Available parent domains
 * @param {string[]} [props.existingCategories=[]] - List of existing categories
 * @param {string[]} [props.defaultTags=[]] - Initial tag chips
 * @param {function} [props.onSubmitSuccess] - Callback when submitted successfully
 */
export const SubmitResourceForm = ({
    domains = [],
    existingCategories = [],
    defaultTags = ['free', 'official'],
    onSubmitSuccess,
}) => {
    const [url, setUrl] = useState('');
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [domain, setDomain] = useState(domains[0] || 'Programming');
    const [categoryMode, setCategoryMode] = useState('existing'); // 'existing' | 'new'
    const [category, setCategory] = useState(existingCategories[0] || '');
    const [proposedCategory, setProposedCategory] = useState('');
    const [tags, setTags] = useState(defaultTags);
    const [tagInput, setTagInput] = useState('');
    const [submitted, setSubmitted] = useState(false);
    const [submitting, setSubmitting] = useState(false);
    const [submitError, setSubmitError] = useState(null);

    const handleAddTag = (e) => {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            const trimmed = tagInput.trim().replace(/^,+|,+$/g, '');
            if (trimmed && !tags.includes(trimmed)) {
                setTags([...tags, trimmed]);
            }
            setTagInput('');
        } else if (e.key === 'Backspace' && !tagInput && tags.length > 0) {
            setTags(tags.slice(0, -1));
        }
    };

    const handleRemoveTag = (tagToRemove) => {
        setTags(tags.filter((t) => t !== tagToRemove));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!url.trim() || !title.trim()) return;

        setSubmitting(true);
        setSubmitError(null);

        const payload = {
            url: url.trim(),
            title: title.trim(),
            description: description.trim(),
            tags,
        };

        if (categoryMode === 'existing') {
            payload.category_id = category;
        } else {
            payload.parent_id = domain;
            payload.new_category_name = proposedCategory.trim();
        }

        try {
            const res = await bestofxyz.post('/resources', payload);
            if (onSubmitSuccess) {
                onSubmitSuccess(res);
            }
            setSubmitted(true);
        } catch (err) {
            const msg =
                err.detail ||
                err.title ||
                (err.errors ? Object.values(err.errors).flat().join(' ') : null) ||
                'Failed to submit resource. Please verify you are logged in.';
            setSubmitError(msg);
        } finally {
            setSubmitting(false);
        }
    };

    const handleReset = () => {
        setUrl('');
        setTitle('');
        setDescription('');
        setCategoryMode('existing');
        setProposedCategory('');
        setTags(defaultTags);
        setSubmitted(false);
    };

    if (submitted) {
        return (
            <Card as="div" className={styles.successCard}>
                <div className={styles.successIcon}>✓</div>
                <h3 className={styles.successTitle}>Resource submitted!</h3>
                <p className={styles.successMessage}>
                    Thank you for contributing. Your submission has been added to the community queue.
                </p>
                <Button variant="primary" onClick={handleReset}>
                    Submit another resource
                </Button>
            </Card>
        );
    }

    return (
        <Card as="form" className={styles.formCard} onSubmit={handleSubmit}>
            <div className={styles.field}>
                <label htmlFor="resource-url" className={styles.label}>
                    Link
                </label>
                <input
                    id="resource-url"
                    type="url"
                    required
                    className={styles.input}
                    placeholder="https://doc.rust-lang.org"
                    value={url}
                    onChange={(e) => setUrl(e.target.value)}
                />
            </div>

            <div className={styles.field}>
                <label htmlFor="resource-title" className={styles.label}>
                    Title
                </label>
                <input
                    id="resource-title"
                    type="text"
                    required
                    className={styles.input}
                    placeholder="The Rust Book"
                    value={title}
                    onChange={(e) => setTitle(e.target.value)}
                />
            </div>

            <div className={styles.field}>
                <label htmlFor="resource-desc" className={styles.label}>
                    Description
                </label>
                <textarea
                    id="resource-desc"
                    rows={3}
                    className={styles.textarea}
                    placeholder="One or two sentences on why this belongs here."
                    value={description}
                    onChange={(e) => setDescription(e.target.value)}
                />
            </div>

            <div className={styles.field}>
                <label htmlFor="resource-domain" className={styles.label}>
                    Domain
                </label>
                <select
                    id="resource-domain"
                    className={styles.select}
                    value={domain}
                    onChange={(e) => setDomain(e.target.value)}
                >
                    {domains.map((d) => (
                        <option key={d} value={d}>
                            {d}
                        </option>
                    ))}
                </select>
            </div>

            <div className={styles.field}>
                <label className={styles.label}>Category</label>
                <div className={styles.toggleRow}>
                    <button
                        type="button"
                        className={`${styles.toggleBtn} ${
                            categoryMode === 'existing' ? styles.toggleActive : ''
                        }`}
                        onClick={() => setCategoryMode('existing')}
                    >
                        Choose existing
                    </button>
                    <button
                        type="button"
                        className={`${styles.toggleBtn} ${
                            categoryMode === 'new' ? styles.toggleActive : ''
                        }`}
                        onClick={() => setCategoryMode('new')}
                    >
                        Propose new
                    </button>
                </div>

                {categoryMode === 'existing' ? (
                    <select
                        className={styles.select}
                        value={category}
                        onChange={(e) => setCategory(e.target.value)}
                    >
                        {existingCategories.map((c) => (
                            <option key={c} value={c}>
                                {c}
                            </option>
                        ))}
                    </select>
                ) : (
                    <div className={styles.newCategoryPanel}>
                        <input
                            type="text"
                            required={categoryMode === 'new'}
                            className={styles.input}
                            placeholder="e.g. Best Rust Courses for Beginners"
                            value={proposedCategory}
                            onChange={(e) => setProposedCategory(e.target.value)}
                        />
                        <div className={styles.hint}>
                            Name it like "Best X" or "Best X for Y" — specific enough that it won't
                            overlap an existing category.
                        </div>
                        <div className={styles.callout}>
                            New categories start <strong>pending</strong>. This one goes live once it
                            has 3 resources and a few community votes — until then it shows up under{' '}
                            {domain}, marked "New."
                        </div>
                    </div>
                )}
            </div>

            <div className={styles.field}>
                <label className={styles.label}>Tags</label>
                <div className={styles.chipInput}>
                    {tags.map((tag) => (
                        <span key={tag} className={styles.chipRemovable}>
                            {tag}
                            <button
                                type="button"
                                className={styles.removeChipBtn}
                                aria-label={`Remove tag ${tag}`}
                                onClick={() => handleRemoveTag(tag)}
                            >
                                &times;
                            </button>
                        </span>
                    ))}
                    <input
                        type="text"
                        className={styles.tagInputField}
                        placeholder="Add a tag and press enter…"
                        value={tagInput}
                        onChange={(e) => setTagInput(e.target.value)}
                        onKeyDown={handleAddTag}
                    />
                </div>
                <div className={styles.hint}>Optional. Used for filtering, not for ranking.</div>
            </div>

            {submitError && (
                <div style={{ color: '#e53e3e', fontSize: '14px', marginBottom: '16px', lineHeight: 1.4 }}>
                    {submitError}
                </div>
            )}

            <Button variant="primary" type="submit" className={styles.submitBtn} disabled={submitting}>
                {submitting ? 'Submitting…' : 'Submit resource'}
            </Button>
        </Card>
    );
};

export default SubmitResourceForm;
