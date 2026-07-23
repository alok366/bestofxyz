import React, { useState } from 'react';
import { NavLink } from 'react-router-dom';
import ExperimentHeader from '../components/ExperimentHeader';
import { mockCategoryInfo, mockSubcategories } from './mockData';
import './TopLevelCategoryMock.less';

export function TopLevelCategoryMock() {
  const [activeTab, setActiveTab] = useState('all'); // 'all', 'live', 'pending'
  const [searchTerm, setSearchTerm] = useState('');
  const [subList, setSubList] = useState(mockSubcategories);

  // Local interactive upvote for pending proposals feedback
  const handleVotePending = (id) => {
    setSubList((prev) =>
      prev.map((sub) => {
        if (sub.id === id) {
          const newVotes = sub.upvotes + 1;
          const met = sub.resourceCount >= sub.requiredCount && newVotes >= sub.requiredVotes;
          return { ...sub, upvotes: newVotes, thresholdMet: met };
        }
        return sub;
      })
    );
  };

  const filteredSubs = subList.filter((sub) => {
    const matchesSearch =
      sub.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      sub.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
      sub.topTags.some((t) => t.toLowerCase().includes(searchTerm.toLowerCase()));

    if (!matchesSearch) return false;
    if (activeTab === 'live') return sub.status === 'live';
    if (activeTab === 'pending') return sub.status === 'pending';
    return true;
  });

  const liveCount = subList.filter((s) => s.status === 'live').length;
  const pendingCount = subList.filter((s) => s.status === 'pending').length;

  return (
    <div className="top-cat-page">
      <ExperimentHeader />

      <main className="top-cat">
        {/* Breadcrumb */}
        <nav className="top-cat__breadcrumb">
          <NavLink to="/experiment/category-directory">Categories</NavLink>
          <span>/</span>
          <span className="top-cat__crumb-current">{mockCategoryInfo.name}</span>
        </nav>

        {/* Category Hero */}
        <header className="top-cat__hero">
          <div className="top-cat__hero-header">
            <div className="top-cat__icon">{mockCategoryInfo.icon}</div>
            <div className="top-cat__hero-info">
              <div className="top-cat__hero-meta">
                <span className="top-cat__badge">Top-Level Category</span>
                <span className="top-cat__curator">Curated by {mockCategoryInfo.curator}</span>
              </div>
              <h1 className="top-cat__title">{mockCategoryInfo.name}</h1>
              <p className="top-cat__desc">{mockCategoryInfo.description}</p>
            </div>

            <NavLink to="/experiment/submit-resource" className="top-cat__btn-propose">
              + Propose New "Best X" List
            </NavLink>
          </div>
        </header>

        {/* Navigation & Search Bar */}
        <div className="top-cat__bar">
          <div className="top-cat__tabs">
            <button
              className={`top-cat__tab ${activeTab === 'all' ? 'is-active' : ''}`}
              onClick={() => setActiveTab('all')}
            >
              All Lists ({subList.length})
            </button>
            <button
              className={`top-cat__tab ${activeTab === 'live' ? 'is-active' : ''}`}
              onClick={() => setActiveTab('live')}
            >
              Live Best-Of Lists ({liveCount})
            </button>
            <button
              className={`top-cat__tab top-cat__tab--pending ${activeTab === 'pending' ? 'is-active' : ''}`}
              onClick={() => setActiveTab('pending')}
            >
              Pending Proposals ⏳ ({pendingCount})
            </button>
          </div>

          <div className="top-cat__search">
            <input
              type="text"
              placeholder={`Search within ${mockCategoryInfo.name}...`}
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>
        </div>

        {/* Pending Banner Info */}
        {activeTab === 'pending' && (
          <div className="top-cat__pending-explainer">
            <div className="top-cat__pending-icon">💡</div>
            <div>
              <h4>How Subcategory Proposals Work</h4>
              <p>
                Anyone can propose a subcategory (e.g. "Best Rust Courses"). To go live, a proposal needs at least <strong>5 resources</strong> and <strong>10 community upvotes</strong>. Once reached, it undergoes a quick team check and unlocks automatically!
              </p>
            </div>
          </div>
        )}

        {/* Empty State */}
        {filteredSubs.length === 0 && (
          <div className="top-cat__empty">
            <p>No subcategories match your current filter or search criteria.</p>
            <button
              onClick={() => {
                setActiveTab('all');
                setSearchTerm('');
              }}
            >
              Clear Filter
            </button>
          </div>
        )}

        {/* Subcategory Grid */}
        <div className="top-cat__grid">
          {filteredSubs.map((sub) => {
            const isPending = sub.status === 'pending';

            return (
              <div
                key={sub.id}
                className={`sub-card ${isPending ? 'sub-card--pending' : ''} ${
                  sub.thresholdMet ? 'sub-card--ready' : ''
                }`}
              >
                <header className="sub-card__header">
                  <div className="sub-card__status-row">
                    {!isPending ? (
                      <span className="sub-card__badge-live">✓ Live Subcategory</span>
                    ) : sub.thresholdMet ? (
                      <span className="sub-card__badge-ready">✨ Threshold Met — Ready for Team Check</span>
                    ) : (
                      <span className="sub-card__badge-progress">
                        ⏳ Pending Approval ({sub.resourceCount}/{sub.requiredCount} resources)
                      </span>
                    )}

                    <span className="sub-card__by">Proposed by @{sub.proposedBy}</span>
                  </div>

                  <NavLink
                    to={
                      isPending
                        ? '/experiment/pending-subcategory'
                        : '/experiment/subcategory'
                    }
                    className="sub-card__title"
                  >
                    {sub.name}
                  </NavLink>
                  <p className="sub-card__desc">{sub.description}</p>
                </header>

                {/* Progress bar if pending */}
                {isPending && (
                  <div className="sub-card__progress-box">
                    <div className="sub-card__progress-labels">
                      <span>Threshold Progress</span>
                      <span>
                        <strong>{sub.resourceCount}</strong>/{sub.requiredCount} resources • <strong>{sub.upvotes}</strong>/{sub.requiredVotes} votes
                      </span>
                    </div>
                    <div className="sub-card__bar-bg">
                      <div
                        className="sub-card__bar-fill"
                        style={{
                          width: `${Math.min(100, (sub.resourceCount / sub.requiredCount) * 100)}%`,
                        }}
                      />
                    </div>
                  </div>
                )}

                {/* Tags */}
                <div className="sub-card__tags">
                  {sub.topTags.map((tag) => (
                    <span key={tag} className="sub-card__tag">
                      #{tag}
                    </span>
                  ))}
                </div>

                <footer className="sub-card__footer">
                  {!isPending ? (
                    <div className="sub-card__stats">
                      <span>🏆 <strong>{sub.resourceCount}</strong> resources</span>
                      <span>▲ <strong>{sub.upvotes}</strong> total votes</span>
                    </div>
                  ) : (
                    <button
                      className="sub-card__btn-upvote"
                      onClick={() => handleVotePending(sub.id)}
                    >
                      ▲ Upvote Proposal ({sub.upvotes})
                    </button>
                  )}

                  <NavLink
                    to={
                      isPending
                        ? '/experiment/pending-subcategory'
                        : '/experiment/subcategory'
                    }
                    className="sub-card__action-btn"
                  >
                    {isPending ? 'View Proposal Status →' : 'Explore List →'}
                  </NavLink>
                </footer>
              </div>
            );
          })}
        </div>
      </main>
    </div>
  );
}

export default TopLevelCategoryMock;
