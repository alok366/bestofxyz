import React, { useState } from 'react';
import { NavLink } from 'react-router-dom';
import ExperimentHeader from '../components/ExperimentHeader';
import { mockResourceDetail, mockComments } from './mockData';
import './ResourceDetailMock.less';

// Single Comment Component with nested recursion
function CommentItem({ comment, onVote, onReplySubmit }) {
  const [showReplyForm, setShowReplyForm] = useState(false);
  const [replyText, setReplyText] = useState('');

  const handleSubmitReply = (e) => {
    e.preventDefault();
    if (!replyText.trim()) return;

    onReplySubmit(comment.id, replyText);
    setReplyText('');
    setShowReplyForm(false);
  };

  return (
    <div className={`comment-item ${comment.votes < 0 ? 'comment-item--downvoted' : ''}`}>
      <div className="comment-item__side">
        <div className="comment-item__vote-box">
          <button
            className={`comment-item__vote-btn ${comment.userVote === 1 ? 'is-active' : ''}`}
            onClick={() => onVote(comment.id, 1)}
          >
            ▲
          </button>
          <span className="comment-item__votes">{comment.votes}</span>
          <button
            className={`comment-item__vote-btn ${comment.userVote === -1 ? 'is-active' : ''}`}
            onClick={() => onVote(comment.id, -1)}
          >
            ▼
          </button>
        </div>
      </div>

      <div className="comment-item__main">
        <header className="comment-item__header">
          <span className="comment-item__author">@{comment.author}</span>
          <span className="comment-item__time">{comment.timeAgo}</span>
          {comment.votes < 0 && (
            <span className="comment-item__downvoted-flag">Low score comment</span>
          )}
        </header>

        <div className="comment-item__body">{comment.body}</div>

        <footer className="comment-item__footer">
          <button
            className="comment-item__reply-btn"
            onClick={() => setShowReplyForm((prev) => !prev)}
          >
            💬 Reply
          </button>
        </footer>

        {/* Inline Reply Form */}
        {showReplyForm && (
          <form className="comment-reply-form" onSubmit={handleSubmitReply}>
            <textarea
              rows={2}
              placeholder={`Replying to @${comment.author}...`}
              value={replyText}
              onChange={(e) => setReplyText(e.target.value)}
            />
            <div className="comment-reply-form__actions">
              <button
                type="button"
                className="comment-reply-form__btn-cancel"
                onClick={() => setShowReplyForm(false)}
              >
                Cancel
              </button>
              <button type="submit" className="comment-reply-form__btn-send">
                Post Reply
              </button>
            </div>
          </form>
        )}

        {/* Nested Replies */}
        {comment.replies && comment.replies.length > 0 && (
          <div className="comment-item__nested">
            {comment.replies.map((reply) => (
              <CommentItem
                key={reply.id}
                comment={reply}
                onVote={onVote}
                onReplySubmit={onReplySubmit}
              />
            ))}
          </div>
        )}
      </div>
    </div>
  );
}

export function ResourceDetailMock() {
  const [resource, setResource] = useState(mockResourceDetail);
  const [comments, setComments] = useState(mockComments);
  const [newCommentText, setNewCommentText] = useState('');

  // Handle Resource Vote
  const handleResourceVote = (direction) => {
    setResource((prev) => {
      let voteDiff = 0;
      let newVote = 0;
      if (prev.userVote === direction) {
        voteDiff = -direction;
        newVote = 0;
      } else {
        voteDiff = direction - prev.userVote;
        newVote = direction;
      }
      return { ...prev, votes: prev.votes + voteDiff, userVote: newVote };
    });
  };

  // Handle Comment Vote recursively
  const handleCommentVote = (commentId, direction) => {
    const updateRecursive = (list) =>
      list.map((item) => {
        if (item.id === commentId) {
          let voteDiff = 0;
          let newVote = 0;
          if (item.userVote === direction) {
            voteDiff = -direction;
            newVote = 0;
          } else {
            voteDiff = direction - item.userVote;
            newVote = direction;
          }
          return { ...item, votes: item.votes + voteDiff, userVote: newVote };
        }
        if (item.replies && item.replies.length > 0) {
          return { ...item, replies: updateRecursive(item.replies) };
        }
        return item;
      });

    setComments((prev) => updateRecursive(prev));
  };

  // Handle Reply Submit recursively
  const handleReplySubmit = (targetId, replyText) => {
    const newReply = {
      id: 'c_' + Date.now(),
      author: 'you (current user)',
      votes: 1,
      userVote: 1,
      timeAgo: 'Just now',
      body: replyText,
      replies: [],
    };

    const addRecursive = (list) =>
      list.map((item) => {
        if (item.id === targetId) {
          return { ...item, replies: [...(item.replies || []), newReply] };
        }
        if (item.replies && item.replies.length > 0) {
          return { ...item, replies: addRecursive(item.replies) };
        }
        return item;
      });

    setComments((prev) => addRecursive(prev));
  };

  // Handle Top-Level New Comment
  const handleAddTopComment = (e) => {
    e.preventDefault();
    if (!newCommentText.trim()) return;

    const newComment = {
      id: 'c_' + Date.now(),
      author: 'you (current user)',
      votes: 1,
      userVote: 1,
      timeAgo: 'Just now',
      body: newCommentText,
      replies: [],
    };

    setComments([newComment, ...comments]);
    setNewCommentText('');
  };

  return (
    <div className="res-detail-page">
      <ExperimentHeader />

      <main className="res-detail">
        {/* Breadcrumb */}
        <nav className="res-detail__breadcrumb">
          <NavLink to="/experiment/category-directory">Categories</NavLink>
          <span>/</span>
          <NavLink to="/experiment/top-level-category">{resource.category}</NavLink>
          <span>/</span>
          <NavLink to="/experiment/subcategory">{resource.subcategory}</NavLink>
        </nav>

        {/* Main Resource Display Card */}
        <article className="res-hero">
          <div className="res-hero__left">
            <div className="res-hero__vote-stack">
              <button
                className={`res-hero__vote-btn ${resource.userVote === 1 ? 'is-active' : ''}`}
                onClick={() => handleResourceVote(1)}
              >
                ▲
              </button>
              <span className="res-hero__votes">{resource.votes}</span>
              <button
                className={`res-hero__vote-btn ${resource.userVote === -1 ? 'is-active' : ''}`}
                onClick={() => handleResourceVote(-1)}
              >
                ▼
              </button>
            </div>
          </div>

          <div className="res-hero__main">
            <div className="res-hero__header">
              <span className="res-hero__cat-badge">{resource.subcategory}</span>
              <span className="res-hero__author">
                Submitted by @{resource.submittedBy} • {resource.submittedAt}
              </span>
            </div>

            <h1 className="res-hero__title">{resource.title}</h1>
            <p className="res-hero__desc">{resource.description}</p>

            <div className="res-hero__tags">
              {resource.tags.map((t) => (
                <span key={t} className="res-hero__tag">
                  #{t}
                </span>
              ))}
            </div>

            <div className="res-hero__actions">
              <a
                href={resource.url}
                target="_blank"
                rel="noreferrer"
                className="res-hero__btn-visit"
              >
                Visit Resource ↗ ({resource.domain})
              </a>

              <span className="res-hero__share">🔗 Copy Direct Link</span>
            </div>
          </div>
        </article>

        {/* Discussion Section */}
        <section className="res-discussion">
          <div className="res-discussion__header">
            <h2>Discussion & Community Reviews</h2>
            <span className="res-discussion__count">{comments.length} top-level reviews</span>
          </div>

          {/* New Comment Box */}
          <form className="res-comment-box" onSubmit={handleAddTopComment}>
            <h4 className="res-comment-box__title">Add Your Review or Comment</h4>
            <textarea
              rows={3}
              placeholder="What makes this resource great (or not)? Share your experience..."
              value={newCommentText}
              onChange={(e) => setNewCommentText(e.target.value)}
            />
            <div className="res-comment-box__footer">
              <span className="res-comment-box__hint">
                Markdown supported • Keep reviews constructive
              </span>
              <button type="submit" className="res-comment-box__btn">
                Post Review
              </button>
            </div>
          </form>

          {/* Comments List */}
          <div className="res-comments-list">
            {comments.map((comment) => (
              <CommentItem
                key={comment.id}
                comment={comment}
                onVote={handleCommentVote}
                onReplySubmit={handleReplySubmit}
              />
            ))}
          </div>
        </section>
      </main>
    </div>
  );
}

export default ResourceDetailMock;
