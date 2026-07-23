import React from 'react';
import { NavLink } from 'react-router-dom';
import ExperimentHeader from './components/ExperimentHeader';
import './index.less';

export function ExperimentIndex() {
  const pages = [
    {
      id: 'category-directory',
      num: '1',
      title: 'Category Directory',
      path: '/experiment/category-directory',
      desc: 'Browse top-level categories owned by bestofxyz team, each featuring top subcategories, resource metrics, and pending threshold indicators.',
      badge: 'Tier 1 & Tier 2 Overview',
      edgeCases: ['Sparse category with 0 live subcategories', 'Search empty state', 'Unusually long subcategory names'],
    },
    {
      id: 'top-level-category',
      num: '2',
      title: 'Top-Level Category Page',
      path: '/experiment/top-level-category',
      desc: 'Top category view (e.g. "Programming") showcasing active subcategories alongside newly proposed pending subcategories with progress meters.',
      badge: 'Category Hub',
      edgeCases: ['Subcategory sitting right at threshold (5/5 resources)', 'Pending 2/5 progress state', 'Propose new subcategory trigger'],
    },
    {
      id: 'subcategory',
      num: '3',
      title: 'Subcategory Page ("Best of" List)',
      path: '/experiment/subcategory',
      desc: 'Ranked resource catalog (e.g. "Best Rust Courses") featuring upvote/downvote counters, sorting, tag chips, and submit action.',
      badge: 'Core Ranking View',
      edgeCases: ['Unusually long resource title', '0-vote fresh submission', 'Downvoted resource (-4 votes)', 'Tag search filter'],
    },
    {
      id: 'resource-detail',
      num: '4',
      title: 'Resource Detail Page',
      path: '/experiment/resource-detail',
      desc: 'Single resource view with deep review stats, metadata, external link button, and nested threaded discussion comments.',
      badge: 'Review & Discussion',
      edgeCases: ['Nested reply trees (3 levels deep)', 'Interactive upvote & reply submission', 'Downvoted comment state'],
    },
    {
      id: 'submit-resource',
      num: '5',
      title: 'Submit-a-Resource Flow',
      path: '/experiment/submit-resource',
      desc: 'Submission form with category selection, existing subcategory picker OR "Propose New Subcategory" mode with live naming hints.',
      badge: 'Contribution Flow',
      edgeCases: ['Inline naming hint check: "Best [Topic] [Format]"', 'Live preview card', 'Interactive submission result modal'],
    },
    {
      id: 'pending-subcategory',
      num: '6',
      title: 'Pending Subcategory State',
      path: '/experiment/pending-subcategory',
      desc: 'Dual-perspective visualization: Proposer dashboard with threshold progress (2/5 resources) vs Directory public preview card.',
      badge: 'Lifecycle & Threshold',
      edgeCases: ['Proposer progress tracker', 'Public vs Proposer toggle', 'Threshold requirements breakdown'],
    },
    {
      id: 'team-moderation',
      num: '7',
      title: 'Team Moderation & Merging (Stretch)',
      path: '/experiment/team-moderation',
      desc: 'Team admin console for reviewing pending subcategories, approving valid proposals, and merging duplicate subcategories with redirects.',
      badge: 'Admin & Deduplication',
      edgeCases: ['Duplicate detection alert ("Best Ways to Learn Rust")', 'Interactive Merge Modal & redirect simulator'],
    },
  ];

  return (
    <div className="exp-index-page">
      <ExperimentHeader />

      <main className="exp-index">
        <header className="exp-index__hero">
          <div className="exp-index__tag">bestofxyz UI/UX Prototypes</div>
          <h1 className="exp-index__title">
            Two-Tier Hybrid Category & Resource Sandbox
          </h1>
          <p className="exp-index__subtitle">
            Explore and review interactive UI/UX mockups for category navigation, pending subcategory thresholds, resource rankings, discussion threads, and moderation workflows.
          </p>

          <div className="exp-index__model-cards">
            <div className="exp-index__model-card exp-index__model-card--top">
              <div className="exp-index__model-icon">🏛️</div>
              <div>
                <h3 className="exp-index__model-title">Top-Level Categories</h3>
                <p className="exp-index__model-text">
                  Team-owned & stable (e.g. Programming, Design Tools). Form the main site navigation.
                </p>
              </div>
            </div>

            <div className="exp-index__model-card exp-index__model-card--sub">
              <div className="exp-index__model-icon">🚀</div>
              <div>
                <h3 className="exp-index__model-title">User-Proposed Subcategories</h3>
                <p className="exp-index__model-text">
                  User-submitted lists (e.g. "Best Rust Courses"). Stay pending until threshold (5 resources) is cleared.
                </p>
              </div>
            </div>

            <div className="exp-index__model-card exp-index__model-card--tag">
              <div className="exp-index__model-icon">🏷️</div>
              <div>
                <h3 className="exp-index__model-title">Free-Form Tags</h3>
                <p className="exp-index__model-text">
                  Unmoderated metadata (e.g. #beginner-friendly, #free) for cross-cutting filtering.
                </p>
              </div>
            </div>
          </div>
        </header>

        <section className="exp-index__grid">
          {pages.map((p) => (
            <NavLink key={p.id} to={p.path} className="exp-card">
              <div className="exp-card__header">
                <span className="exp-card__num">{p.num}</span>
                <span className="exp-card__badge">{p.badge}</span>
              </div>
              <h2 className="exp-card__title">{p.title}</h2>
              <p className="exp-card__desc">{p.desc}</p>

              <div className="exp-card__edges">
                <span className="exp-card__edges-label">Edge Cases Tested:</span>
                <ul className="exp-card__edges-list">
                  {p.edgeCases.map((ec, idx) => (
                    <li key={idx} className="exp-card__edge-item">
                      ✓ {ec}
                    </li>
                  ))}
                </ul>
              </div>

              <div className="exp-card__cta">
                <span>View Prototype</span>
                <span className="exp-card__arrow">→</span>
              </div>
            </NavLink>
          ))}
        </section>
      </main>
    </div>
  );
}

export default ExperimentIndex;
