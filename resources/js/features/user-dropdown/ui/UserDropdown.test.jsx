import React, { act } from 'react';
import { createRoot } from 'react-dom/client';
import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import { Provider } from 'react-redux';
import { MemoryRouter } from 'react-router-dom';
import { configureStore } from '@reduxjs/toolkit';
import { THEME_PREFERENCES } from '@shared/config/theme';
import { themeReducer, themeListenerMiddleware } from '@shared/lib/theme';
import { authReducer } from '@shared/lib/auth';
import { UserDropdown } from './UserDropdown';

describe('UserDropdown Component', () => {
  let container;
  let root;
  let store;

  beforeEach(() => {
    container = document.createElement('div');
    document.body.appendChild(container);
    root = createRoot(container);

    store = configureStore({
      reducer: {
        auth: authReducer,
        theme: themeReducer,
      },
      middleware: (getDefaultMiddleware) =>
        getDefaultMiddleware().prepend(themeListenerMiddleware.middleware),
    });
  });

  afterEach(() => {
    act(() => {
      root.unmount();
    });
    container.remove();
  });

  it('exports UserDropdown component function', () => {
    expect(typeof UserDropdown).toBe('function');
  });

  it('renders trigger button with avatar initials', () => {
    act(() => {
      root.render(
        <Provider store={store}>
          <MemoryRouter>
            <UserDropdown initials="AB" />
          </MemoryRouter>
        </Provider>
      );
    });

    const button = container.querySelector('button[aria-label="User menu"]');
    expect(button).not.toBeNull();
    expect(button.textContent).toContain('AB');
  });

  it('shows Login and Sign Up links when unauthenticated', () => {
    act(() => {
      root.render(
        <Provider store={store}>
          <MemoryRouter>
            <UserDropdown initials="U" />
          </MemoryRouter>
        </Provider>
      );
    });

    const triggerBtn = container.querySelector('button[aria-label="User menu"]');

    act(() => {
      triggerBtn.click();
    });

    const links = container.querySelectorAll('a[role="menuitem"]');
    const linkTexts = Array.from(links).map((l) => l.textContent.trim());
    expect(linkTexts).toContain('Login');
    expect(linkTexts).toContain('Sign Up');
  });

  it('toggles theme submenu on click and selects a theme option', () => {
    act(() => {
      root.render(
        <Provider store={store}>
          <MemoryRouter>
            <UserDropdown initials="U" />
          </MemoryRouter>
        </Provider>
      );
    });

    const triggerBtn = container.querySelector('button[aria-label="User menu"]');

    // Click to open main menu
    act(() => {
      triggerBtn.click();
    });

    // Theme menu item exists
    const themeBtn = Array.from(container.querySelectorAll('button')).find(
      (b) => b.textContent.includes('Theme')
    );
    expect(themeBtn).toBeDefined();
    expect(themeBtn.getAttribute('aria-expanded')).toBe('false');

    // Submenu is not open initially
    expect(container.querySelectorAll('button[role="menuitemradio"]').length).toBe(0);

    // 1st Click on Theme: opens submenu
    act(() => {
      themeBtn.click();
    });
    expect(themeBtn.getAttribute('aria-expanded')).toBe('true');
    expect(container.querySelectorAll('button[role="menuitemradio"]').length).toBe(3);

    // 2nd Click on Theme: closes submenu
    act(() => {
      themeBtn.click();
    });
    expect(themeBtn.getAttribute('aria-expanded')).toBe('false');
    expect(container.querySelectorAll('button[role="menuitemradio"]').length).toBe(0);

    // 3rd Click on Theme: re-opens submenu
    act(() => {
      themeBtn.click();
    });
    expect(themeBtn.getAttribute('aria-expanded')).toBe('true');

    // Find all theme radio buttons
    const radioButtons = container.querySelectorAll('button[role="menuitemradio"]');
    expect(radioButtons.length).toBe(3);

    // Select dark theme
    const darkRadio = Array.from(radioButtons).find((b) => b.textContent.includes('Dark'));
    act(() => {
      darkRadio.click();
    });

    // Verify Redux state updated to dark
    expect(store.getState().theme.preference).toBe(THEME_PREFERENCES.DARK);
    expect(darkRadio.getAttribute('aria-checked')).toBe('true');
  });
});
