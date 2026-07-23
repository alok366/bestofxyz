import React, { useState } from 'react';
import { NavLink } from 'react-router-dom';
import ExperimentHeader from '../components/ExperimentHeader';
import { mockPendingProposal } from './mockData';
import './PendingSubcategoryMock.less';

export function PendingSubcategoryMock() {
  const [viewPerspective, setViewPerspective] = useState('proposer'); // 'proposer' or 'public'
  const [proposal, setProposal] = useState(mockPendingProposal);
  const [copiedLink, setCopiedLink] = useState(false);

  // Interactive local simulation of adding a resource to push progress bar
  const handleAddDemoResource = () => {
    if (proposal.currentResources >= 5) return;

    const nextCount = proposal.currentResources + 1;
    const newRes = {
      id: 'p_res_' + (nextCount + 1),
      title: `Demo Resource #${nextCount}: Asynchronous Programming Patterns in Rust`,
      url: `https://example.com/async-pattern-${nextCount}`,
      domain: 'example.com',
      submittedBy: 'you (current user)',
      votes: 1,
      submittedAt: 'Just now',
    };

    setProposal({
      ...proposal,
      currentResources: nextCount,
      currentVotes: proposal.currentVotes + 1,
      submittedResources: [...proposal.submittedResources, newRes],
    });
  };

  const handleUpvoteProposal = () => {
    setProposal({
      ...proposal,
      currentVotes: proposal.currentVotes + 1,
    });
  };

  const progressPercent = Math.min(
    100,
    Math.round((proposal.currentResources / proposal.requiredResources) * 100)
  );

  const isThresholdCleared =
    proposal.currentResources >= proposal.requiredResources &&
    proposal.currentVotes >= proposal.requiredVotes;

  const handleShareClick = () => {
    setCopiedLink(true);
    setTimeout(() => setCopiedLink(false), 2500);
  };

  return (
    <div className="pending-page">
      <ExperimentHeader />

      <main className="pending-main">
        {/* Perspective Switcher Toolbar */}
        <div className="pending-switcher-bar">
          <div className="pending-switcher-bar__label">Select Perspective View:</div>
          <div className="pending-switcher-bar__btns">
            <button
              className={`pending-switcher-btn ${viewPerspective === 'proposer' ? 'is-active' : ''}`}
              onClick={() => setViewPerspective('proposer')}
            >
              👤 Proposer's View (Dashboard & Management)
            </button>
            <button
              className={`pending-switcher-btn ${viewPerspective === 'public' ? 'is-active' : ''}`}
              onClick={() => setViewPerspective('public')}
            >
              🌐 Public Directory View (Card in Category Index)
            </button>
          </div>
        </div>

        {/* PERSPECTIVE 1: PROPOSER'S VIEW */}
        {viewPerspective === 'proposer' && (
          <div className="proposer-view">
            {/* Status Hero */}
            <div className={`pending-status-banner ${isThresholdCleared ? 'is-cleared' : ''}`}>
              <div className="pending-status-banner__icon">
                {isThresholdCleared ? '🎉' : '⏳'}
              </div>
              <div className="pending-status-banner__info">
                <div className="pending-status-banner__badge">
                  {isThresholdCleared
                    ? 'Threshold Reached — Pending Team Check'
                    : `Pending Activation — ${proposal.currentResources}/${proposal.requiredResources} Resources`}
                </div>
                <h1 className="pending-status-banner__title">{proposal.name}</h1>
                <p className="pending-status-banner__desc">
                  Proposed by <strong>@{proposal.proposedBy}</strong> in{' '}
                  <strong>{proposal.parentCategory.name}</strong> • {proposal.createdAt}
                </p>
              </div>
            </div>

            {/* Threshold Progress Section */}
            <section className="threshold-card">
              <div className="threshold-card__header">
                <div>
                  <h2 className="threshold-card__title">Activation Threshold Progress</h2>
                  <p className="threshold-card__sub">
                    Subcategories unlock automatically when community resource and upvote requirements are met.
                  </p>
                </div>

                <button className="threshold-card__btn-upvote" onClick={handleUpvoteProposal}>
                  ▲ Upvote Proposal ({proposal.currentVotes})
                </button>
              </div>

              {/* Progress Bar */}
              <div className="threshold-progress">
                <div className="threshold-progress__bar-bg">
                  <div
                    className="threshold-progress__bar-fill"
                    style={{ width: `${progressPercent}%` }}
                  />
                </div>
                <div className="threshold-progress__stats">
                  <span>
                    <strong>{proposal.currentResources}</strong> of {proposal.requiredResources} resources added ({progressPercent}%)
                  </span>
                  <span>
                    <strong>{proposal.currentVotes}</strong> of {proposal.requiredVotes} upvotes needed
                  </span>
                </div>
              </div>

              {/* Checklist */}
              <div className="threshold-checklist">
                <div className={`checklist-item ${proposal.currentResources >= 5 ? 'is-done' : ''}`}>
                  <span className="checklist-icon">
                    {proposal.currentResources >= 5 ? '✓' : '○'}
                  </span>
                  <span>
                    Submit at least <strong>5 resources</strong> ({proposal.currentResources}/5)
                  </span>
                </div>

                <div className={`checklist-item ${proposal.currentVotes >= 10 ? 'is-done' : ''}`}>
                  <span className="checklist-icon">
                    {proposal.currentVotes >= 10 ? '✓' : '○'}
                  </span>
                  <span>
                    Receive at least <strong>10 upvotes</strong> ({proposal.currentVotes}/10)
                  </span>
                </div>

                <div className="checklist-item is-done">
                  <span className="checklist-icon">✓</span>
                  <span>Naming convention check passed ("Best X")</span>
                </div>
              </div>

              {/* Interactive Simulation Controls */}
              <div className="proposer-actions-box">
                <h4 className="proposer-actions-box__title">⚡ Interactive Demo Action Controls:</h4>
                <div className="proposer-actions-box__btns">
                  <button
                    className="btn-demo-add"
                    onClick={handleAddDemoResource}
                    disabled={proposal.currentResources >= 5}
                  >
                    {proposal.currentResources >= 5
                      ? '✓ 5/5 Resources Submitted'
                      : `+ Add Resource to List (${proposal.currentResources}/5)`}
                  </button>

                  <button className="btn-demo-share" onClick={handleShareClick}>
                    {copiedLink ? '✓ Link Copied to Clipboard!' : '🔗 Share Link to Get Votes'}
                  </button>
                </div>
              </div>
            </section>

            {/* Submitted Resources List in Pending Subcategory */}
            <section className="pending-res-section">
              <h3 className="pending-res-section__title">
                Resources Submitted to this List ({proposal.submittedResources.length})
              </h3>

              <div className="pending-res-list">
                {proposal.submittedResources.map((res, index) => (
                  <div key={res.id} className="pending-res-card">
                    <span className="pending-res-card__num">#{index + 1}</span>
                    <div className="pending-res-card__body">
                      <a
                        href={res.url}
                        target="_blank"
                        rel="noreferrer"
                        className="pending-res-card__title"
                      >
                        {res.title} ↗
                      </a>
                      <span className="pending-res-card__meta">
                        Submitted by @{res.submittedBy} • {res.submittedAt} • ▲ {res.votes} votes
                      </span>
                    </div>
                  </div>
                ))}
              </div>
            </section>
          </div>
        )}

        {/* PERSPECTIVE 2: PUBLIC DIRECTORY VIEW */}
        {viewPerspective === 'public' && (
          <div className="public-view">
            <div className="public-view__hero">
              <h2>Directory Card Preview</h2>
              <p>How this pending subcategory appears to visitors browsing the Programming directory:</p>
            </div>

            <div className="public-card-preview">
              <div className="public-card-preview__header">
                <span className="public-card-preview__badge">
                  ⏳ Pending — {proposal.currentResources}/5 resources
                </span>
                <span className="public-card-preview__by">Proposed by @{proposal.proposedBy}</span>
              </div>

              <h3 className="public-card-preview__title">{proposal.name}</h3>
              <p className="public-card-preview__desc">{proposal.description}</p>

              <div className="public-card-preview__meter">
                <div className="public-card-preview__meter-bar">
                  <div
                    className="public-card-preview__meter-fill"
                    style={{ width: `${progressPercent}%` }}
                  />
                </div>
                <div className="public-card-preview__meter-text">
                  Unlock Progress: {proposal.currentResources}/5 resources • {proposal.currentVotes}/10 votes
                </div>
              </div>

              <div className="public-card-preview__resources-snippet">
                <span className="snippet-title">Contains {proposal.currentResources} resources so far:</span>
                <ul>
                  {proposal.submittedResources.slice(0, 3).map((r) => (
                    <li key={r.id}>• {r.title}</li>
                  ))}
                </ul>
              </div>

              <div className="public-card-preview__footer">
                <button
                  className="public-card-preview__upvote-btn"
                  onClick={handleUpvoteProposal}
                >
                  ▲ Upvote Proposal ({proposal.currentVotes})
                </button>
                <NavLink to="/experiment/submit-resource" className="public-card-preview__add-btn">
                  + Add Resource to this List
                </NavLink>
              </div>
            </div>
          </div>
        )}
      </main>
    </div>
  );
}

export default PendingSubcategoryMock;
