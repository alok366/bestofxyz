import React from 'react';

const VARIANTS = {
  forbidden: {
    title: 'Permission required',
    body: "You don't have permission to access this page.",
    hint: 'Please contact the account owner who invited you to request access.',
  },
  notfound: {
    title: 'Page not found',
    body: "We couldn't find the page you were looking for.",
    hint: '',
  },
  error: {
    title: 'Something went wrong',
    body: 'We hit an unexpected error loading this page.',
    hint: 'Please try again, or contact your account owner if the problem persists.',
  },
};

const StatusMessage = ({ variant = 'forbidden', title, body, hint, action }) => {
  const v = VARIANTS[variant] || VARIANTS.error;
  const resolvedHint = hint !== undefined ? hint : v.hint;

  return (
    <div
      data-status-message={variant}
      className="d-flex align-items-center justify-content-center w-100"
      style={{ minHeight: '60vh' }}
    >
      <div className="text-center p-5">
        <h3 className="mb-3">{title || v.title}</h3>
        <p className="mb-2">{body || v.body}</p>
        {resolvedHint && <p className="text-muted">{resolvedHint}</p>}
        {action && <div className="mt-4">{action}</div>}
      </div>
    </div>
  );
};

export { StatusMessage };
export default StatusMessage;
