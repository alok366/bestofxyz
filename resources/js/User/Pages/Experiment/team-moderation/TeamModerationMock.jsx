import React, { useState } from 'react';
import ExperimentHeader from '../components/ExperimentHeader';
import { mockPendingQueue, mockLiveTargetSubcategories } from './mockData';
import './TeamModerationMock.less';

export function TeamModerationMock() {
  const [queue, setQueue] = useState(mockPendingQueue);
  const [mergeModalItem, setMergeModalItem] = useState(null);
  const [selectedTargetId, setSelectedTargetId] = useState('sub_rust');
  const [redirectSlug, setRedirectSlug] = useState(true);
  const [actionNotice, setActionNotice] = useState('');

  // Handle Approve Proposal
  const handleApprove = (id, name) => {
    setQueue(queue.filter((q) => q.id !== id));
    showNotice(`✓ "${name}" approved and published to live directory.`);
  };

  // Handle Reject Proposal
  const handleReject = (id, name) => {
    setQueue(queue.filter((q) => q.id !== id));
    showNotice(`❌ "${name}" rejected.`);
  };

  // Open Merge Modal
  const handleOpenMerge = (item) => {
    setMergeModalItem(item);
    if (item.suggestedDuplicateTarget) {
      setSelectedTargetId(item.suggestedDuplicateTarget.id);
    }
  };

  // Execute Merge
  const handleExecuteMerge = () => {
    if (!mergeModalItem) return;

    const targetSub = mockLiveTargetSubcategories.find((t) => t.id === selectedTargetId);
    const sourceName = mergeModalItem.name;
    const targetName = targetSub ? targetSub.name : 'Target List';

    setQueue(queue.filter((q) => q.id !== mergeModalItem.id));
    setMergeModalItem(null);
    showNotice(
      `🔀 Successfully merged "${sourceName}" into "${targetName}". ${mergeModalItem.resources.length} resources transferred with 301 redirect active!`
    );
  };

  const showNotice = (msg) => {
    setActionNotice(msg);
    setTimeout(() => setActionNotice(''), 4000);
  };

  return (
    <div className="mod-page">
      <ExperimentHeader />

      <main className="mod-main">
        {/* Header */}
        <header className="mod-header">
          <div className="mod-header__badge">Team Moderation Console</div>
          <h1 className="mod-header__title">Subcategory Proposals & Deduplication</h1>
          <p className="mod-header__sub">
            Review incoming user-proposed subcategories, resolve duplicate listings, and merge overlapping lists into canonical topics.
          </p>

          {/* Alert Notice */}
          {actionNotice && <div className="mod-notice">{actionNotice}</div>}
        </header>

        {/* Pending Queue List */}
        <div className="mod-queue-container">
          <div className="mod-queue-header">
            <h3>Pending Queue ({queue.length} items awaiting review)</h3>
            <span className="mod-queue-tip">
              💡 Proposals clearing 5 resources + 10 votes are highlighted
            </span>
          </div>

          {queue.length === 0 ? (
            <div className="mod-empty">
              <div className="mod-empty-icon">🎉</div>
              <h3>Queue is clear!</h3>
              <p>All pending subcategories have been reviewed or merged.</p>
            </div>
          ) : (
            <div className="mod-queue-list">
              {queue.map((item) => (
                <div
                  key={item.id}
                  className={`mod-card ${item.hasPotentialDuplicate ? 'mod-card--duplicate' : ''}`}
                >
                  <div className="mod-card__main">
                    <div className="mod-card__tags-row">
                      <span className="mod-card__cat-badge">{item.parentCategory}</span>
                      {item.hasPotentialDuplicate && (
                        <span className="mod-card__warn-badge">
                          ⚠️ Potential Duplicate of "{item.suggestedDuplicateTarget.name}"
                        </span>
                      )}
                      {item.resourceCount >= 5 && item.votes >= 10 && (
                        <span className="mod-card__ready-badge">
                          ✨ Threshold Met ({item.resourceCount}/5 res • {item.votes}/10 votes)
                        </span>
                      )}
                    </div>

                    <h2 className="mod-card__title">{item.name}</h2>
                    <p className="mod-card__meta">
                      Proposed by @{item.proposedBy} • {item.createdAt} • Slug:{' '}
                      <code>/{item.slug}</code>
                    </p>

                    {/* Resources list snippet */}
                    <div className="mod-card__resources-box">
                      <span className="mod-card__res-head">
                        Submitted Resources ({item.resourceCount}):
                      </span>
                      <ul className="mod-card__res-ul">
                        {item.resources.map((r) => (
                          <li key={r.id}>
                            • {r.title} <span className="votes">(▲ {r.votes})</span>
                          </li>
                        ))}
                      </ul>
                    </div>
                  </div>

                  {/* Actions Column */}
                  <div className="mod-card__actions">
                    <button
                      className="mod-btn mod-btn--approve"
                      onClick={() => handleApprove(item.id, item.name)}
                    >
                      ✓ Approve & Publish
                    </button>

                    <button
                      className="mod-btn mod-btn--merge"
                      onClick={() => handleOpenMerge(item)}
                    >
                      🔀 Merge into Existing...
                    </button>

                    <button
                      className="mod-btn mod-btn--reject"
                      onClick={() => handleReject(item.id, item.name)}
                    >
                      ✕ Reject
                    </button>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </main>

      {/* MERGE MODAL */}
      {mergeModalItem && (
        <div className="mod-modal-overlay">
          <div className="mod-modal">
            <header className="mod-modal__header">
              <h3>🔀 Merge Duplicate Subcategory</h3>
              <button className="mod-modal__close" onClick={() => setMergeModalItem(null)}>
                ✕
              </button>
            </header>

            <div className="mod-modal__body">
              <div className="mod-merge-step">
                <span className="step-label">Source Subcategory (Being Merged Away):</span>
                <div className="source-box">
                  <strong>{mergeModalItem.name}</strong>
                  <span className="source-meta">
                    ({mergeModalItem.resourceCount} resources, {mergeModalItem.votes} upvotes)
                  </span>
                </div>
              </div>

              <div className="mod-merge-arrow">↓ Merge into ↓</div>

              <div className="mod-merge-step">
                <span className="step-label">Target Canonical Subcategory:</span>
                <select
                  className="mod-modal__select"
                  value={selectedTargetId}
                  onChange={(e) => setSelectedTargetId(e.target.value)}
                >
                  {mockLiveTargetSubcategories.map((target) => (
                    <option key={target.id} value={target.id}>
                      {target.name} ({target.parent})
                    </option>
                  ))}
                </select>
              </div>

              <div className="mod-merge-options">
                <label className="mod-checkbox-label">
                  <input
                    type="checkbox"
                    checked={redirectSlug}
                    onChange={(e) => setRedirectSlug(e.target.checked)}
                  />
                  <span>
                    Create permanent 301 redirect from <code>/{mergeModalItem.slug}</code> to target URL
                  </span>
                </label>

                <label className="mod-checkbox-label">
                  <input type="checkbox" defaultChecked />
                  <span>
                    Transfer all {mergeModalItem.resourceCount} submitted resources & append tag metadata
                  </span>
                </label>
              </div>

              {/* Preview of transferred resources */}
              <div className="mod-transfer-preview">
                <h5>Preview of Transferred Resources:</h5>
                <ul>
                  {mergeModalItem.resources.map((r) => (
                    <li key={r.id}>
                      → {r.title} <em>(Will be appended to target)</em>
                    </li>
                  ))}
                </ul>
              </div>
            </div>

            <footer className="mod-modal__footer">
              <button
                className="mod-btn-cancel"
                onClick={() => setMergeModalItem(null)}
              >
                Cancel
              </button>
              <button
                className="mod-btn-execute"
                onClick={handleExecuteMerge}
              >
                Confirm Merge & Redirect →
              </button>
            </footer>
          </div>
        </div>
      )}
    </div>
  );
}

export default TeamModerationMock;
