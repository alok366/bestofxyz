import React, { useState } from 'react';
import { NavLink } from 'react-router-dom';
import ExperimentHeader from '../components/ExperimentHeader';
import {
  mockCategoriesList,
  mockExistingSubcategories,
  namingConventionPattern
} from './mockData';
import './SubmitResourceMock.less';

export function SubmitResourceMock() {
  const [url, setUrl] = useState('');
  const [title, setTitle] = useState('');
  const [description, setDescription] = useState('');
  const [selectedCat, setSelectedCat] = useState('cat_programming');
  const [subMode, setSubMode] = useState('existing'); // 'existing' or 'propose'
  const [selectedSub, setSelectedSub] = useState('sub_rust');
  const [proposedSubName, setProposedSubName] = useState('');
  const [proposedSubDesc, setProposedSubDesc] = useState('');
  const [tagInput, setTagInput] = useState('');
  const [tags, setTags] = useState(['beginner-friendly', 'interactive']);
  const [showSuccessModal, setShowSuccessModal] = useState(false);

  // Validate subcategory naming convention inline
  const isNamingConventionValid =
    proposedSubName.trim().length > 0 &&
    (proposedSubName.trim().startsWith('Best ') || proposedSubName.trim().startsWith('best '));

  // Add tag chip
  const handleAddTag = (e) => {
    if (e.key === 'Enter' || e.key === ',') {
      e.preventDefault();
      const newTag = tagInput.trim().toLowerCase().replace(/[^a-z0-9-]/g, '');
      if (newTag && !tags.includes(newTag)) {
        setTags([...tags, newTag]);
      }
      setTagInput('');
    }
  };

  const handleRemoveTag = (tagToRemove) => {
    setTags(tags.filter((t) => t !== tagToRemove));
  };

  // Simulate Auto-Fill Metadata
  const handleAutoFill = () => {
    if (!url) {
      setUrl('https://rust-by-example.github.io');
    }
    setTitle('Rust By Example — Official Interactive Guide');
    setDescription(
      'Rust by Example (RBE) is a collection of runnable examples that demonstrate various Rust concepts and standard libraries.'
    );
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    setShowSuccessModal(true);
  };

  const currentExistingSubs = mockExistingSubcategories[selectedCat] || [];

  return (
    <div className="submit-page">
      <ExperimentHeader />

      <main className="submit-main">
        <header className="submit-main__header">
          <span className="submit-main__pill">Submission & Proposal Flow</span>
          <h1 className="submit-main__title">Submit a Resource or Propose a Best-Of List</h1>
          <p className="submit-main__subtitle">
            Share great tutorials, tools, or courses. If no matching subcategory exists, propose a new "Best X" subcategory list!
          </p>
        </header>

        <div className="submit-layout">
          {/* Main Form */}
          <form className="submit-form" onSubmit={handleSubmit}>
            {/* Step 1: Resource Link */}
            <div className="submit-form__group">
              <label className="submit-form__label">
                Resource URL <span className="req">*</span>
              </label>
              <div className="submit-form__url-row">
                <input
                  type="url"
                  className="submit-form__input"
                  placeholder="https://example.com/rust-course"
                  value={url}
                  onChange={(e) => setUrl(e.target.value)}
                  required
                />
                <button
                  type="button"
                  className="submit-form__btn-autofill"
                  onClick={handleAutoFill}
                >
                  ⚡ Auto-fetch Details
                </button>
              </div>
            </div>

            {/* Title */}
            <div className="submit-form__group">
              <label className="submit-form__label">
                Resource Title <span className="req">*</span>
              </label>
              <input
                type="text"
                className="submit-form__input"
                placeholder="e.g. The Comprehensive Rust Masterclass 2026"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                required
              />
            </div>

            {/* Description */}
            <div className="submit-form__group">
              <label className="submit-form__label">Brief Summary / Overview</label>
              <textarea
                className="submit-form__textarea"
                rows={3}
                placeholder="What makes this resource helpful? Who is it for?"
                value={description}
                onChange={(e) => setDescription(e.target.value)}
              />
            </div>

            {/* Step 2: Pick Top-Level Category */}
            <div className="submit-form__group">
              <label className="submit-form__label">
                Top-Level Category <span className="req">*</span>
              </label>
              <select
                className="submit-form__select"
                value={selectedCat}
                onChange={(e) => {
                  setSelectedCat(e.target.value);
                  const firstSub = mockExistingSubcategories[e.target.value]?.[0];
                  if (firstSub) setSelectedSub(firstSub.id);
                }}
              >
                {mockCategoriesList.map((cat) => (
                  <option key={cat.id} value={cat.id}>
                    {cat.name}
                  </option>
                ))}
              </select>
            </div>

            {/* Step 3: Subcategory Selection Mode */}
            <div className="submit-form__group">
              <label className="submit-form__label">Subcategory List</label>

              <div className="submit-form__radio-group">
                <label className="submit-form__radio-label">
                  <input
                    type="radio"
                    name="subMode"
                    value="existing"
                    checked={subMode === 'existing'}
                    onChange={() => setSubMode('existing')}
                  />
                  <span>Pick from existing subcategories</span>
                </label>

                <label className="submit-form__radio-label">
                  <input
                    type="radio"
                    name="subMode"
                    value="propose"
                    checked={subMode === 'propose'}
                    onChange={() => setSubMode('propose')}
                  />
                  <span>✨ Propose a NEW subcategory list</span>
                </label>
              </div>

              {/* Mode A: Select existing */}
              {subMode === 'existing' && (
                <div className="submit-form__sub-existing">
                  {currentExistingSubs.length > 0 ? (
                    <select
                      className="submit-form__select"
                      value={selectedSub}
                      onChange={(e) => setSelectedSub(e.target.value)}
                    >
                      {currentExistingSubs.map((sub) => (
                        <option key={sub.id} value={sub.id}>
                          {sub.name}
                        </option>
                      ))}
                    </select>
                  ) : (
                    <div className="submit-form__no-sub-alert">
                      No existing subcategories in this top category yet. Please switch to "Propose a NEW subcategory".
                    </div>
                  )}
                </div>
              )}

              {/* Mode B: Propose new subcategory */}
              {subMode === 'propose' && (
                <div className="submit-form__propose-box">
                  {/* Inline Naming Convention Hint Box */}
                  <div className="submit-form__hint-box">
                    <div className="submit-form__hint-header">
                      <span>💡 Subcategory Naming Convention</span>
                    </div>
                    <p className="submit-form__hint-text">
                      Subcategory lists are community-curated "Best Of" rankings. Names should follow the convention:
                      <br />
                      <strong>"Best [Topic] [Format/Target]"</strong> — e.g. <em>"Best Rust Courses"</em>, <em>"Best Figma Plugins for Beginners"</em>, or <em>"Best Ways to Learn React"</em>.
                    </p>
                  </div>

                  <div className="submit-form__group">
                    <label className="submit-form__label">
                      Proposed Subcategory Name <span className="req">*</span>
                    </label>
                    <div className="submit-form__input-wrapper">
                      <input
                        type="text"
                        className={`submit-form__input ${
                          proposedSubName ? (isNamingConventionValid ? 'is-valid' : 'is-invalid') : ''
                        }`}
                        placeholder="e.g. Best Async Rust Guides"
                        value={proposedSubName}
                        onChange={(e) => setProposedSubName(e.target.value)}
                        required={subMode === 'propose'}
                      />
                      {proposedSubName && (
                        <span className="submit-form__validation-icon">
                          {isNamingConventionValid ? '✓ Follows "Best X" convention' : '⚠️ Must start with "Best..."'}
                        </span>
                      )}
                    </div>
                  </div>

                  <div className="submit-form__group">
                    <label className="submit-form__label">Subcategory Scope / Description</label>
                    <input
                      type="text"
                      className="submit-form__input"
                      placeholder="e.g. Guides focused on Tokio, async/await, and async IO pattern in Rust"
                      value={proposedSubDesc}
                      onChange={(e) => setProposedSubDesc(e.target.value)}
                    />
                  </div>
                </div>
              )}
            </div>

            {/* Step 4: Free-Form Tags */}
            <div className="submit-form__group">
              <label className="submit-form__label">
                Free-Form Tags <span className="opt">(Unmoderated metadata)</span>
              </label>
              <div className="submit-form__tags-input-box">
                <div className="submit-form__tags-chips">
                  {tags.map((t) => (
                    <span key={t} className="submit-form__tag-chip">
                      #{t}
                      <button
                        type="button"
                        onClick={() => handleRemoveTag(t)}
                        className="submit-form__tag-remove"
                      >
                        ✕
                      </button>
                    </span>
                  ))}
                </div>
                <input
                  type="text"
                  className="submit-form__tag-field"
                  placeholder="Type tag & press Enter or comma (e.g. free, video)..."
                  value={tagInput}
                  onChange={(e) => setTagInput(e.target.value)}
                  onKeyDown={handleAddTag}
                />
              </div>
            </div>

            <button type="submit" className="submit-form__submit-btn">
              {subMode === 'propose' ? 'Submit Resource & Propose Subcategory' : 'Submit Resource'}
            </button>
          </form>

          {/* Sidebar Live Preview Card */}
          <aside className="submit-preview">
            <h3 className="submit-preview__title">Live Listing Preview</h3>
            <p className="submit-preview__sub">How this submission will look in the catalog:</p>

            <div className="res-card-preview">
              <div className="res-card-preview__vote">
                <span>▲</span>
                <span>1</span>
                <span>▼</span>
              </div>
              <div className="res-card-preview__body">
                <div className="res-card-preview__cat">
                  {subMode === 'propose'
                    ? proposedSubName || 'Best [Proposed Subcategory]'
                    : currentExistingSubs.find((s) => s.id === selectedSub)?.name || 'Best Rust Courses'}
                  {subMode === 'propose' && (
                    <span className="res-card-preview__pending-badge">Pending 1/5</span>
                  )}
                </div>
                <h4 className="res-card-preview__title">
                  {title || 'Resource Title Placeholder'}
                </h4>
                <p className="res-card-preview__desc">
                  {description || 'Summary of the resource will appear here after entry...'}
                </p>
                <div className="res-card-preview__tags">
                  {tags.map((t) => (
                    <span key={t} className="res-card-preview__tag">
                      #{t}
                    </span>
                  ))}
                </div>
              </div>
            </div>

            {subMode === 'propose' && (
              <div className="submit-preview__threshold-info">
                <h4>⏳ Threshold Info</h4>
                <p>
                  Newly proposed subcategories start in <strong>Pending State</strong>. Once 5 resources and 10 upvotes are collected, it moves to the main directory automatically.
                </p>
              </div>
            )}
          </aside>
        </div>
      </main>

      {/* Confirmation Modal */}
      {showSuccessModal && (
        <div className="submit-modal-overlay">
          <div className="submit-modal">
            <div className="submit-modal__icon">🎉</div>
            <h2 className="submit-modal__title">Submission Received!</h2>
            <p className="submit-modal__text">
              {subMode === 'propose' ? (
                <>
                  Your submission <strong>"{title || 'Resource'}"</strong> has been created, and your proposed subcategory <strong>"{proposedSubName || 'New Subcategory'}"</strong> is now live in <strong>Pending State</strong> (1/5 resources required).
                </>
              ) : (
                <>
                  Your submission <strong>"{title || 'Resource'}"</strong> has been successfully added to <strong>"{currentExistingSubs.find((s) => s.id === selectedSub)?.name}"</strong>!
                </>
              )}
            </p>

            <div className="submit-modal__actions">
              <NavLink
                to={subMode === 'propose' ? '/experiment/pending-subcategory' : '/experiment/subcategory'}
                className="submit-modal__btn-primary"
              >
                {subMode === 'propose' ? 'View Pending Subcategory Page →' : 'View Subcategory List →'}
              </NavLink>

              <button
                className="submit-modal__btn-secondary"
                onClick={() => setShowSuccessModal(false)}
              >
                Close & Submit Another
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default SubmitResourceMock;
