import { test, expect } from '@playwright/test';

test.describe('Lakshya Marketing App', () => {
  test('Login page loads and shows demo credentials', async ({ page }) => {
    await page.goto('/login');
    await expect(page).toHaveTitle(/Login/);
    await expect(page.locator('.logo')).toContainText('L');
    await expect(page.locator('.demo-credentials')).toBeVisible();
  });

  test('Admin login and dashboard loads with layout variables', async ({ page }) => {
    await page.goto('/login');
    await page.fill('#email', 'admin@lakshya.ai');
    await page.fill('#password', 'admin123');
    await page.click('.btn-submit');
    await page.waitForURL('/admin/dashboard');

    await expect(page.locator('.sidebar')).toBeVisible();
    await expect(page.locator('.main-header')).toBeVisible();
    await expect(page.locator('.content-area')).toBeVisible();
  });

  test('Marketing page loads with all tabs', async ({ page }) => {
    await page.goto('/login');
    await page.fill('#email', 'admin@lakshya.ai');
    await page.fill('#password', 'admin123');
    await page.click('.btn-submit');
    await page.waitForURL('/admin/dashboard');
    await page.goto('/admin/marketing');

    await expect(page.locator('.tab-nav-btn')).toHaveCount(3);
    await expect(page.locator('#tab-nav-social')).toBeVisible();
    await expect(page.locator('#tab-nav-growth')).toBeVisible();
    await expect(page.locator('#tab-nav-campaign')).toBeVisible();
  });

  test('Preloader fades out on load', async ({ page }) => {
    await page.goto('/login');
    await page.fill('#email', 'admin@lakshya.ai');
    await page.fill('#password', 'admin123');
    await page.click('.btn-submit');
    await page.waitForURL('/admin/dashboard');

    const preloader = page.locator('#hologram-preloader');
    await expect(preloader).toHaveClass(/fade-out/);
  });

  test('Social media form is accessible', async ({ page }) => {
    await page.goto('/login');
    await page.fill('#email', 'admin@lakshya.ai');
    await page.fill('#password', 'admin123');
    await page.click('.btn-submit');
    await page.waitForURL('/admin/dashboard');
    await page.goto('/admin/marketing');

    await expect(page.locator('#social_business_description')).toBeVisible();
    await expect(page.locator('#social_tone')).toBeVisible();
    await expect(page.locator('#social_target_audience')).toBeVisible();
    await expect(page.locator('#socialGenerateBtn')).toBeVisible();
  });

  test('Sub-tabs are present after generation', async ({ page }) => {
    await page.goto('/login');
    await page.fill('#email', 'admin@lakshya.ai');
    await page.fill('#password', 'admin123');
    await page.click('.btn-submit');
    await page.waitForURL('/admin/dashboard');
    await page.goto('/admin/marketing');

    await page.fill('#social_business_description', 'Test business');
    await page.click('#socialGenerateBtn');

    await page.waitForResponse(response => response.status() === 200);
    await expect(page.locator('#sub-social-linkedin')).toBeVisible();
  });

  test('Growth content pack generation works', async ({ page }) => {
    await page.goto('/login');
    await page.fill('#email', 'admin@lakshya.ai');
    await page.fill('#password', 'admin123');
    await page.click('.btn-submit');
    await page.waitForURL('/admin/dashboard');
    await page.goto('/admin/marketing');

    await page.click('#tab-nav-growth');
    await expect(page.locator('#form-growth')).toBeVisible();

    await page.fill('#growth_business_description', 'Test newsletter business');
    await page.fill('#growth_target_audience', 'Tech startups');
    await page.fill('#growth_campaign_goal', 'Increase signups');

    await page.click('#growthGenerateBtn');
    await expect(page.locator('#growthNewsletterBody')).not.toBeEmpty();
  });

  test('Campaign generation works', async ({ page }) => {
    await page.goto('/login');
    await page.fill('#email', 'admin@lakshya.ai');
    await page.fill('#password', 'admin123');
    await page.click('.btn-submit');
    await page.waitForURL('/admin/dashboard');
    await page.goto('/admin/marketing');

    await page.click('#tab-nav-campaign');
    await expect(page.locator('#form-campaign')).toBeVisible();

    await page.fill('#campaign_product', 'Test Product');
    await page.fill('#campaign_target_audience', 'Test audience');
    await page.fill('#campaign_budget', '$500/month');
    await page.fill('#campaign_goal', 'Drive sales');

    await page.click('#campaignGenerateBtn');
    await expect(page.locator('#campaignTargetingText')).not.toBeEmpty();
  });

  test('Launch modal opens on click', async ({ page }) => {
    await page.goto('/login');
    await page.fill('#email', 'admin@lakshya.ai');
    await page.fill('#password', 'admin123');
    await page.click('.btn-submit');
    await page.waitForURL('/admin/dashboard');
    await page.goto('/admin/marketing');

    await page.fill('#social_business_description', 'Test launch campaign');
    await page.selectOption('#social_tone', 'Creative & Friendly');
    await page.fill('#social_target_audience', 'Designers');
    await page.fill('#social_cta', 'Get started');

    await page.click('#socialGenerateBtn');
    await page.waitForTimeout(500);

    await page.click('.btn-primary >> text=/Digital Market/');
    await expect(page.locator('#launchModal')).toBeVisible();
    await expect(page.locator('.step-item#step1')).toBeVisible();
  });

  test('Tab navigation switches content', async ({ page }) => {
    await page.goto('/login');
    await page.fill('#email', 'admin@lakshya.ai');
    await page.fill('#password', 'admin123');
    await page.click('.btn-submit');
    await page.waitForURL('/admin/dashboard');
    await page.goto('/admin/marketing');

    await expect(page.locator('.tab-nav-btn.active')).toHaveText(/Social Post Engine/);

    await page.click('#tab-nav-growth');
    await expect(page.locator('#tab-nav-growth')).toHaveClass(/active/);
    await expect(page.locator('#preview-growth')).toBeVisible();

    await page.click('#tab-nav-campaign');
    await expect(page.locator('#tab-nav-campaign')).toHaveClass(/active/);
    await expect(page.locator('#preview-campaign')).toBeVisible();
  });

  test('Social platform sub-tabs are visible', async ({ page }) => {
    await page.goto('/login');
    await page.fill('#email', 'admin@lakshya.ai');
    await page.fill('#password', 'admin123');
    await page.click('.btn-submit');
    await page.waitForURL('/admin/dashboard');
    await page.goto('/admin/marketing');

    await expect(page.locator('#tab-nav-social.active')).toBeVisible();
    await expect(page.locator('.sub-tab-pill')).toHaveCount(5);
  });
});