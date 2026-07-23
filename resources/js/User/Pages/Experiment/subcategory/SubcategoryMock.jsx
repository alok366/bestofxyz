import React, { useState } from 'react';
import { NavLink } from 'react-router-dom';
import ExperimentHeader from '../components/ExperimentHeader';
import { mockSubcategoryMeta, mockResourcesList } from './mockData';
import './SubcategoryMock.less';

export function SubcategoryMock() {
  const [resources, setResources] = useState(mockResourcesList);
  const [activeTag, setActiveTag] = useState('all');
  const [sortBy, setSortBy] = useState('top'); // 'top', 'newest', 'comments'
  const [searchTerm, setSearchTerm] = useState('');

  // Interactive Vote handler
  const handleVote = (id, direction) => {
    setResources((prev) =>
      prev.map((item) => {
        if (item.id === id) {
          let newVoteState = 0;
          let voteDiff = 0;

          if (item.userVote === direction) {
            // Undo vote
            newVoteState = 0;
            voteDiff = -direction;
          } else {
            // Apply vote (or flip vote from +1 to -1)
            voteDiff = direction - item.userVote;
            newVoteState = direction;
          }

          return {
            ...item,
            votes: item.votes + voteDiff,
            userVote: newVoteState,
          };
        }
        return item;
      })
    );
  };

  // Filter & Sort
  const filteredResources = resources
    .filter((res) => {
      const matchesTag = activeTag === 'all' || res.tags.includes(activeTag);
      const matchesSearch =
        res.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
        res.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
        res.tags.some((t) => t.toLowerCase().includes(searchTerm.toLowerCase()));

      return matchesTag && matchesSearch;
    })
    .sort((a, b) => {
      if (sortBy === 'top') return b.votes - a.votes;
      if (sortBy === 'newest') return a.id.localeCompare(b.id);
      if (sortBy === 'comments') return b.commentCount - a.commentCount;
      return 0;
    });

  return (
    <div className="subcat-page">
      <ExperimentHeader />

      <main className="subcat">
        {/* Breadcrumbs */}
        <nav className="subcat__breadcrumb">
          <NavLink to="/experiment/category-directory">Categories</NavLink>
          <span>/</span>
          <NavLink to="/experiment/top-level-category">
            {mockSubcategoryMeta.parentCategory.name}
          </NavLink>
          <span>/</span>
          <span className="subcat__crumb-current">{mockSubcategoryMeta.title}</span>
        </nav>

        {/* Subcategory Hero Header */}
        <header className="subcat__hero">
          <div className="subcat__hero-header">
            <div>
              <div className="subcat__meta-badges">
                <span className="subcat__badge-live">✓ Live Subcategory</span>
                <span className="subcat__meta-text">
                  Proposed by @{mockSubcategoryMeta.submittedBy} • {mockSubcategoryMeta.createdAt}
                </span>
              </div>

              <h1 className="subcat__title">{mockSubcategoryMeta.title}</h1>
              <p className="subcat__desc">{mockSubcategoryMeta.description}</p>
            </div>

            <NavLink to="/experiment/submit-resource" className="subcat__btn-submit">
              + Submit Resource
            </NavLink>
          </div>

          {/* Quick Metrics */}
          <div className="subcat__metrics">
            <div className="subcat__metric">
              <span className="subcat__metric-val">{mockSubcategoryMeta.resourceCount}</span>
              <span className="subcat__metric-lbl">Curated Resources</span>
            </div>
            <div className="subcat__metric">
              <span className="subcat__metric-val">▲ {mockSubcategoryMeta.totalVotes}</span>
              <span className="subcat__metric-lbl">Community Votes</span>
            </div>
          </div>
        </header>

        {/* Toolbar: Tags & Sorting */}
        <section className="subcat__toolbar">
          <div className="subcat__tags-bar">
            <span className="subcat__tags-label">Filter Tags:</span>
            {mockSubcategoryMeta.tags.map((tag) => (
              <button
                key={tag}
                className={`subcat__tag-chip ${activeTag === tag ? 'is-active' : ''}`}
                onClick={() => setActiveTag(tag)}
              >
                #{tag}
              </button>
            ))}
          </div>

          <div className="subcat__controls-row">
            <div className="subcat__search-box">
              <input
                type="text"
                placeholder="Search resources..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
            </div>

            <div className="subcat__sort-box">
              <label>Sort by:</label>
              <select value={sortBy} onChange={(e) => setSortBy(e.target.value)}>
                <option value="top">Top Voted</option>
                <option value="newest">Newest Submissions</option>
                <option value="comments">Most Discussed</option>
              </select>
            </div>
          </div>
        </section>

        {/* Empty Search Result */}
        {filteredResources.length === 0 && (
          <div className="subcat__empty">
            <div className="subcat__empty-icon">🔍</div>
            <h3>No resources found matching your filter</h3>
            <p>Try resetting tag filters or search term.</p>
            <button
              onClick={() => {
                setActiveTag('all');
                setSearchTerm('');
              }}
            >
              Reset Filters
            </button>
          </div>
        )}

        {/* Resource Items List */}
        <div className="subcat__list">
          {filteredResources.map((res, index) => (
            <article
              key={res.id}
              className={`res-card ${res.userVote !== 0 ? 'res-card--voted' : ''}`}
            >
              {/* Vote Stack */}
              <div className="res-card__vote-stack">
                <button
                  className={`res-card__vote-btn res-card__vote-btn--up ${
                    res.userVote === 1 ? 'is-active' : ''
                  }`}
                  onClick={() => handleVote(res.id, 1)}
                  title="Upvote"
                >
                  ▲
                </button>

                <span
                  className={`res-card__vote-count ${
                    res.votes < 0 ? 'is-negative' : res.votes === 0 ? 'is-zero' : ''
                  }`}
                >
                  {res.votes}
                </span>

                <button
                  className={`res-card__vote-btn res-card__vote-btn--down ${
                    res.userVote === -1 ? 'is-active' : ''
                  }`}
                  onClick={() => handleVote(res.id, -1)}
                  title="Downvote"
                >
                  ▼
                </button>
              </div>

              {/* Rank Number */}
              <div className="res-card__rank">#{index + 1}</div>

              {/* Content */}
              <div className="res-card__content">
                <div className="res-card__header">
                  <NavLink
                    to="/experiment/resource-detail"
                    className="res-card__title"
                  >
                    {res.title}
                  </NavLink>
                  <a
                    href={res.url}
                    target="_blank"
                    rel="noreferrer"
                    className="res-card__domain-link"
                  >
                    {res.domain} ↗
                  </a>
                </div>

                <p className="res-card__desc">{res.description}</p>

                <div className="res-card__footer">
                  <div className="res-card__chips">
                    {res.tags.map((t) => (
                      <button
                        key={t}
                        className="res-card__tag-chip"
                        onClick={() => setActiveTag(t)}
                      >
                        #{t}
                      </button>
                    ))}
                  </div>

                  <div className="res-card__meta">
                    <span className="res-card__author">
                      Submitted by <strong>@{res.submittedBy}</strong> • {res.submittedAt}
                    </span>

                    <NavLink
                      to="/experiment/resource-detail"
                      className="res-card__comments-link"
                    >
                      💬 {res.commentCount} {res.commentCount === 1 ? 'comment' : 'comments'}
                    </NavLink>
                  </div>
                </div>
              </div>
            </article>
          ))}
        </div>
      </main>
    </div>
  );
}

export default SubcategoryMock;
