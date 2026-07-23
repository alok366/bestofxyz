import React, { useState } from 'react';
import { NavLink } from 'react-router-dom';
import ExperimentHeader from '../components/ExperimentHeader';
import { mockTopCategories, mockStats } from './mockData';
import './CategoryDirectoryMock.less';

export function CategoryDirectoryMock() {
  const [searchTerm, setSearchTerm] = useState('');
  const [filterType, setFilterType] = useState('all'); // 'all', 'featured', 'has-pending'

  // Filter logic
  const filteredCategories = mockTopCategories.filter((cat) => {
    const matchesSearch =
      cat.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      cat.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
      cat.topSubcategories.some((sub) =>
        sub.name.toLowerCase().includes(searchTerm.toLowerCase())
      );

    if (!matchesSearch) return false;

    if (filterType === 'featured') return cat.isFeatured;
    if (filterType === 'has-pending')
      return cat.topSubcategories.some((s) => s.status === 'pending');
    return true;
  });

  return (
    <div className="cat-dir-page">
      <ExperimentHeader />

      <main className="cat-dir">
        {/* Banner */}
        <section className="cat-dir__hero">
          <div className="cat-dir__hero-pill">📁 Page 1 of 7 Prototype Sandbox</div>
          <h1 className="cat-dir__title">Explore Best-Of Directories</h1>
          <p className="cat-dir__subtitle">
            Top-level categories are curated by the bestofxyz team. Subcategories are community-driven lists formed around specific topics.
          </p>

          {/* Stats Bar */}
          <div className="cat-dir__stats">
            <div className="cat-dir__stat-item">
              <span className="cat-dir__stat-num">{mockStats.totalCategories}</span>
              <span className="cat-dir__stat-label">Top Categories</span>
            </div>
            <div className="cat-dir__stat-divider" />
            <div className="cat-dir__stat-item">
              <span className="cat-dir__stat-num">{mockStats.totalSubcategories}</span>
              <span className="cat-dir__stat-label">Active Subcategories</span>
            </div>
            <div className="cat-dir__stat-divider" />
            <div className="cat-dir__stat-item">
              <span className="cat-dir__stat-num">{mockStats.totalResources.toLocaleString()}</span>
              <span className="cat-dir__stat-label">Curated Resources</span>
            </div>
            <div className="cat-dir__stat-divider" />
            <div className="cat-dir__stat-item cat-dir__stat-item--highlight">
              <span className="cat-dir__stat-num">{mockStats.pendingProposals}</span>
              <span className="cat-dir__stat-label">Pending Proposals</span>
            </div>
          </div>
        </section>

        {/* Controls: Search & Filters */}
        <section className="cat-dir__controls">
          <div className="cat-dir__search-wrapper">
            <span className="cat-dir__search-icon">🔍</span>
            <input
              type="text"
              className="cat-dir__search-input"
              placeholder="Search categories, subcategories, or topics..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
            {searchTerm && (
              <button
                className="cat-dir__search-clear"
                onClick={() => setSearchTerm('')}
              >
                ✕
              </button>
            )}
          </div>

          <div className="cat-dir__filter-pills">
            <button
              className={`cat-dir__pill ${filterType === 'all' ? 'is-active' : ''}`}
              onClick={() => setFilterType('all')}
            >
              All Categories
            </button>
            <button
              className={`cat-dir__pill ${filterType === 'featured' ? 'is-active' : ''}`}
              onClick={() => setFilterType('featured')}
            >
              Featured
            </button>
            <button
              className={`cat-dir__pill ${filterType === 'has-pending' ? 'is-active' : ''}`}
              onClick={() => setFilterType('has-pending')}
            >
              Has Pending Proposals ⏳
            </button>
          </div>
        </section>

        {/* Empty State Edge Case */}
        {filteredCategories.length === 0 && (
          <div className="cat-dir__empty">
            <div className="cat-dir__empty-icon">🔎</div>
            <h3>No matching categories found</h3>
            <p>We couldn't find any category matching "{searchTerm}".</p>
            <button
              className="cat-dir__btn-reset"
              onClick={() => {
                setSearchTerm('');
                setFilterType('all');
              }}
            >
              Reset Search & Filters
            </button>
          </div>
        )}

        {/* Category List */}
        <div className="cat-dir__grid">
          {filteredCategories.map((cat) => (
            <div key={cat.id} className="cat-card">
              <header className="cat-card__header">
                <div className="cat-card__icon">{cat.icon}</div>
                <div className="cat-card__meta">
                  <div className="cat-card__top">
                    <NavLink
                      to="/experiment/top-level-category"
                      className="cat-card__title"
                    >
                      {cat.name}
                    </NavLink>
                    {cat.isFeatured && (
                      <span className="cat-card__badge-featured">Featured</span>
                    )}
                  </div>
                  <p className="cat-card__desc">{cat.description}</p>
                </div>
              </header>

              <div className="cat-card__counts">
                <span><strong>{cat.subcategoryCount}</strong> subcategories</span>
                <span>•</span>
                <span><strong>{cat.resourceCount.toLocaleString()}</strong> resources</span>
              </div>

              {/* Subcategories list preview */}
              <div className="cat-card__sub-section">
                <h4 className="cat-card__sub-title">Top & Pending Subcategories:</h4>

                {cat.topSubcategories.length === 0 ? (
                  <div className="cat-card__no-sub">
                    No subcategories proposed yet. Be the first to submit a resource!
                  </div>
                ) : (
                  <ul className="cat-card__sub-list">
                    {cat.topSubcategories.map((sub) => (
                      <li key={sub.id} className="cat-card__sub-item">
                        <NavLink
                          to={
                            sub.status === 'pending'
                              ? '/experiment/pending-subcategory'
                              : '/experiment/subcategory'
                          }
                          className="cat-card__sub-link"
                        >
                          <span className="cat-card__sub-name">{sub.name}</span>
                          {sub.status === 'live' ? (
                            <span className="cat-card__sub-meta">
                              <span className="cat-card__sub-count">{sub.count} resources</span>
                              <span className="cat-card__sub-votes">▲ {sub.votes}</span>
                            </span>
                          ) : (
                            <span className={`cat-card__sub-pending ${sub.thresholdMet ? 'is-met' : ''}`}>
                              {sub.thresholdMet ? 'Ready (5/5)' : `Pending (${sub.progress})`}
                            </span>
                          )}
                        </NavLink>
                      </li>
                    ))}
                  </ul>
                )}
              </div>

              <footer className="cat-card__footer">
                <NavLink
                  to="/experiment/top-level-category"
                  className="cat-card__view-link"
                >
                  View All Subcategories in {cat.name} →
                </NavLink>

                <NavLink
                  to="/experiment/submit-resource"
                  className="cat-card__propose-btn"
                  title="Submit a resource to this category"
                >
                  + Propose Subcategory
                </NavLink>
              </footer>
            </div>
          ))}
        </div>
      </main>
    </div>
  );
}

export default CategoryDirectoryMock;
