import { chromium } from 'playwright';
import path from 'path';
import fs from 'fs';

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    viewport: { width: 1280, height: 800 }
  });
  const page = await context.newPage();

  const screenshotDir = 'C:/Users/HP/.gemini/antigravity-ide/brain/15e9d4c8-38f4-4409-9960-1a78f0af72cd/scratch/screenshots';
  if (!fs.existsSync(screenshotDir)) {
    fs.mkdirSync(screenshotDir, { recursive: true });
  }

  const takeScreenshot = async (name) => {
    await page.waitForTimeout(500); // Allow UI transitions
    await page.screenshot({ path: path.join(screenshotDir, `${name}.png`) });
    console.log(`[SCREENSHOT] Saved ${name}.png`);
  };

  // Register Dialog Listener (Auto-accept confirm alerts like Delete Keyword)
  page.on('dialog', async dialog => {
    console.log(`[DIALOG] Encountered dialog: "${dialog.message()}" of type ${dialog.type()}`);
    await dialog.accept();
    console.log(`[DIALOG] Accepted dialog successfully.`);
  });

  try {
    // 1. LOGIN
    console.log("1. Navigating to login page...");
    await page.goto('http://localhost:8000/login');
    await takeScreenshot('1_login_page');

    console.log("Entering admin credentials...");
    await page.fill('#email', 'admin@lakshya.ai');
    await page.fill('#password', 'admin123');
    await takeScreenshot('2_login_form_filled');

    console.log("Submitting login...");
    await page.click('button[type="submit"]');
    await page.waitForURL('**/admin/dashboard');
    console.log("Logged in successfully! Landed on dashboard.");
    await takeScreenshot('3_dashboard');

    // 2. CHECK NOTIFICATIONS & PROJECT SWITCHER
    console.log("Testing notification bell click...");
    const bell = page.locator('.bell-container');
    if (await bell.count() > 0) {
      await bell.click();
      await page.waitForTimeout(1000);
      await takeScreenshot('4_notifications_read');
    }

    console.log("Testing project switcher dropdown...");
    const projSelector = page.locator('#headerProjectSelector');
    if (await projSelector.count() > 0) {
      await projSelector.selectOption({ index: 0 }); // Select first project
      await page.waitForTimeout(500);
      await takeScreenshot('5_project_selected');
    }

    // 3. CRM KANBAN BOARD & LEAD DETAILS
    console.log("3. Navigating to CRM Target Board...");
    await page.goto('http://localhost:8000/admin/crm');
    await page.waitForSelector('.kanban-column, .bg-white\\/5');
    await takeScreenshot('6_crm_board');

    console.log("Opening lead details modal...");
    // Find first lead card click handler
    const leadCard = page.locator('[onclick*="openLeadModal"]').first();
    if (await leadCard.count() > 0) {
      await leadCard.click();
      await page.waitForSelector('#leadDetailsModal:not(.hidden)');
      await takeScreenshot('7_lead_details_modal');

      // Click Re-Generate/Generate AI Reply
      console.log("Testing AI Reply generation...");
      const generateReplyBtn = page.locator('button:has-text("Generate AI Reply"), button:has-text("Re-Generate")').first();
      if (await generateReplyBtn.count() > 0) {
        await generateReplyBtn.click();
        await page.waitForTimeout(2000); // Wait for mock generation response
        await takeScreenshot('8_ai_reply_generated');
      }

      // Fill custom reply
      console.log("Modifying generated reply...");
      const replyTextArea = page.locator('#replyTextarea, textarea[name="reply"]').first();
      if (await replyTextArea.count() > 0) {
        await replyTextArea.fill("Hi Josh! We reviewed your budget and stack. We can construct native iOS/Android apps for your startup. Let's talk this Friday.");
        await takeScreenshot('9_reply_modified');
      }

      // Save Reply
      console.log("Saving reply draft...");
      const saveReplyBtn = page.locator('button:has-text("Save Draft"), button:has-text("Save Reply")').first();
      if (await saveReplyBtn.count() > 0) {
        await saveReplyBtn.click();
        await page.waitForTimeout(1000);
        await takeScreenshot('10_reply_saved');
      }

      // Schedule meeting
      console.log("Testing schedule meeting form...");
      const meetLinkInput = page.locator('input[name="meeting_link"]');
      const meetTimeInput = page.locator('input[name="scheduled_at"]');
      const scheduleBtn = page.locator('button:has-text("Schedule Google Meet")');
      if (await meetLinkInput.count() > 0 && await meetTimeInput.count() > 0) {
        await meetLinkInput.fill('https://meet.google.com/test-playwright-meet');
        await meetTimeInput.fill('2026-06-30T14:00');
        await takeScreenshot('11_meeting_form_filled');
        await scheduleBtn.click();
        await page.waitForTimeout(1000);
        await takeScreenshot('12_meeting_scheduled');
      }

      // Close modal
      console.log("Closing lead details modal...");
      const closeModalBtn = page.locator('button:has-text("Close"), #leadDetailsModal button:has-text("✕")').first();
      if (await closeModalBtn.count() > 0) {
        await closeModalBtn.click();
        await page.waitForTimeout(500);
      }
    }

    // 4. CAMPAIGNS (PROJECTS) PAGE
    console.log("4. Navigating to Projects page...");
    await page.goto('http://localhost:8000/admin/projects');
    await page.waitForSelector('.projects-container, button:has-text("New Project")');
    await takeScreenshot('13_projects_page');

    console.log("Opening new project modal...");
    await page.click('button:has-text("New Project")');
    await page.waitForSelector('#projectModal.active');
    await takeScreenshot('13_project_modal_opened');

    console.log("Creating a new project...");
    await page.fill('#projectName', 'Premium Enterprise Outbound');
    await page.fill('#projectDesc', 'Targeting VP of Engineering at mid-sized SaaS platforms.');
    await takeScreenshot('14_project_form_filled');
    await page.click('#projectModal button[type="submit"]');
    await page.waitForTimeout(1000);
    await takeScreenshot('15_project_created');

    console.log("Toggling project status...");
    const toggleProjLink = page.locator('a[href*="projects/toggle"]').first();
    if (await toggleProjLink.count() > 0) {
      await toggleProjLink.click();
      await page.waitForTimeout(1000);
      await takeScreenshot('16_project_status_toggled');
    }

    // 5. KEYWORDS PAGE
    console.log("5. Navigating to Keywords page...");
    await page.goto('http://localhost:8000/admin/keywords');
    await page.waitForSelector('form, table');
    await takeScreenshot('17_keywords_page');

    console.log("Adding new keyword...");
    await page.fill('#keywordInput', 'looking for nextjs engineer');
    await takeScreenshot('18_keyword_input_filled');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(1000);
    await takeScreenshot('19_keyword_added');

    console.log("Toggling keyword active/paused status...");
    const kwStatusBadge = page.locator('.badge[onclick*="toggleKeywordStatus"]').last();
    if (await kwStatusBadge.count() > 0) {
      await kwStatusBadge.click();
      await page.waitForTimeout(1000);
      await takeScreenshot('20_keyword_status_toggled');
    }

    console.log("Deleting keyword (this triggers dialog accept)...");
    const deleteKwBtn = page.locator('button:has-text("Delete")').last();
    if (await deleteKwBtn.count() > 0) {
      await deleteKwBtn.click();
      await page.waitForTimeout(1500); // Allow DB reload and page refresh
      await takeScreenshot('21_keyword_deleted');
      console.log("Keyword successfully deleted and dialog processed!");
    }

    // 6. AI MARKETER (Unified wrapper with Social, Growth, and Ads tabs)
    console.log("6. Navigating to AI Marketer page...");
    await page.goto('http://localhost:8000/admin/marketing');
    await page.waitForSelector('.tabs-nav-container, #socialGeneratorForm');
    await takeScreenshot('22_ai_marketer_page');

    console.log("Filling Social Post Engine form...");
    await page.fill('#social_business_description', 'An automated WhatsApp CRM and AI email sequence builder');
    await page.fill('#social_target_audience', 'SaaS founders and B2B marketers');
    await page.fill('#social_cta', 'Get a 14-day free trial at https://lakshya.ai');
    await takeScreenshot('23_social_form_filled');

    console.log("Generating social suite...");
    await page.click('#socialGenerateBtn');
    await page.waitForTimeout(4000); // Wait for mock generation
    await takeScreenshot('24_social_suite_generated');

    console.log("Switching platform pills...");
    await page.click('#sub-social-twitter');
    await page.waitForTimeout(1000);
    await takeScreenshot('25_social_twitter_preview');

    console.log("Launching campaign post...");
    const launchSocialBtn = page.locator('button:has-text("Digital Market It!")').first();
    if (await launchSocialBtn.count() > 0) {
      await launchSocialBtn.click();
      await page.waitForSelector('#launchModal.active');
      await takeScreenshot('26_launch_progress_modal');
      await page.waitForTimeout(4500); // Allow pipeline animation steps (Step 1-4)
      await takeScreenshot('27_launch_success_view');
      const closeConsoleBtn = page.locator('#modalCloseBtn');
      await closeConsoleBtn.click();
      await page.waitForTimeout(500);
    }

    console.log("Switching to Weekly Growth Pack tab...");
    await page.click('#tab-nav-growth');
    await page.fill('#growth_business_description', 'An automated WhatsApp CRM and AI email sequence builder');
    await page.fill('#growth_target_audience', 'SaaS founders and B2B marketers');
    await page.fill('#growth_campaign_goal', 'Drive signups for the free beta trial');
    await takeScreenshot('28_growth_form_filled');
    await page.click('#growthGenerateBtn');
    await page.waitForTimeout(4000);
    await takeScreenshot('29_growth_pack_generated');

    console.log("Switching growth pack component sub-tabs...");
    await page.click('#sub-growth-blog');
    await page.waitForTimeout(500);
    await takeScreenshot('30_growth_blog_outline');

    console.log("Switching to Ad Campaign Suite tab...");
    await page.click('#tab-nav-campaign');
    await page.fill('#campaign_product', 'Lakshya AI Outbound Agent');
    await page.fill('#campaign_target_audience', 'B2B sales managers');
    await page.fill('#campaign_budget', '$100/week');
    await page.fill('#campaign_goal', 'Book 15-minute product demos');
    await takeScreenshot('31_campaign_form_filled');
    await page.click('#campaignGenerateBtn');
    await page.waitForTimeout(4000);
    await takeScreenshot('32_ad_campaign_planned');

    // 7. ECONOMICS & STATS
    console.log("7. Navigating to Economics & Stats page...");
    await page.goto('http://localhost:8000/admin/statistics');
    await page.waitForSelector('.stats-wrapper');
    await takeScreenshot('33_economics_stats');

    console.log("Navigating to Dashboard to trigger Python VM Scraping Crawler...");
    await page.goto('http://localhost:8000/admin/dashboard');
    await page.waitForSelector('#btn-trigger-scraper');
    await page.click('#btn-trigger-scraper');
    await page.waitForTimeout(3000); // Allow toast notification to render
    await takeScreenshot('34_vm_triggered');

    // 8. CLIENT MANAGER & IMPERSONATION
    console.log("8. Navigating to Client Directory...");
    await page.goto('http://localhost:8000/admin/clients');
    await page.waitForSelector('table, a:has-text("Test Client Access")');
    await takeScreenshot('35_client_directory');

    console.log("Impersonating Sarah Miller (Client ID 2)...");
    await page.click('a[href*="clients/impersonate/2"]');
    await page.waitForURL('**/client/dashboard');
    console.log("Landed on Client Dashboard under simulation mode!");
    await takeScreenshot('36_client_impersonated_dashboard');

    console.log("Navigating to Client Creative Builder...");
    await page.goto('http://localhost:8000/client/marketing');
    await page.waitForSelector('#socialGeneratorForm');
    await takeScreenshot('37_client_marketing_builder');

    console.log("Exiting Impersonation simulation mode...");
    await page.click('a:has-text("Exit Simulation Mode"), a:has-text("Exit Client Mode")');
    await page.waitForURL('**/admin/clients');
    console.log("Returned successfully to Admin Client Directory.");
    await takeScreenshot('38_returned_to_admin');

    // 9. AI AGENTS CONTROL PORTAL
    console.log("9. Navigating to AI Agents Portal...");
    await page.goto('http://localhost:8000/admin/agents');
    await page.waitForSelector('.tab-nav, .agent-card');
    await takeScreenshot('39_ai_agents_main');

    console.log("Toggling Agent Status switch (LeadHunterAgent)...");
    const agentSwitch = page.locator('#agent-card-LeadHunterAgent .slider');
    if (await agentSwitch.count() > 0) {
      await agentSwitch.click();
      await page.waitForTimeout(1000);
      await takeScreenshot('40_agent_status_toggled');
    }

    console.log("Submitting custom task dispatch to queue...");
    await page.selectOption('#dispatch-agent', 'EmailAgent');
    await page.fill('#dispatch-title', 'Verify SMTP outbox sequence for u/saas_bootstrapper');
    await page.selectOption('#dispatch-lead', { index: 1 });
    await takeScreenshot('41_task_dispatcher_filled');
    await page.click('button:has-text("Dispatch to Queue")');
    await page.waitForTimeout(1500);
    await takeScreenshot('42_task_dispatched');

    console.log("Opening Website Visitor Intelligence tab...");
    await page.click('#tab-btn-visitor');
    await page.waitForTimeout(1000);
    await takeScreenshot('43_visitor_tab_active');

    console.log("Opening WhatsApp tab and composer...");
    await page.click('#tab-btn-whatsapp');
    await page.waitForTimeout(1000);
    await page.fill('#wa-phone', '+919876543210');
    await page.fill('#wa-message', 'Hello Josh! Checked your SaaS iOS/Android app requirements. Ready to sync up: https://lakshya.ai/consult');
    await takeScreenshot('44_wa_composer_filled');
    await page.click('button:has-text("Send via WhatsApp API")');
    await page.waitForTimeout(1500);
    await takeScreenshot('45_wa_sent');

    console.log("Opening LinkedIn logs tab...");
    await page.click('#tab-btn-linkedin');
    await page.waitForTimeout(1000);
    await takeScreenshot('46_linkedin_tab_active');

    console.log("Opening Queue Console tab...");
    await page.click('#tab-btn-queue');
    await page.waitForTimeout(1000);
    await takeScreenshot('47_queue_console_active');

    console.log("Selecting a task in the queue to view logs...");
    const firstQueueItem = page.locator('.queue-task-item').first();
    if (await firstQueueItem.count() > 0) {
      await firstQueueItem.click();
      await page.waitForTimeout(1000);
      await takeScreenshot('48_queue_logs_inspected');
    }

    // 10. SETTINGS & BILLING
    console.log("10. Navigating to Settings page...");
    await page.goto('http://localhost:8000/admin/settings');
    await page.waitForSelector('form');
    await takeScreenshot('49_settings_page');

    console.log("Navigating to Billing & SaaS page...");
    await page.goto('http://localhost:8000/admin/billing');
    await page.waitForSelector('table, .card');
    await takeScreenshot('50_billing_page');

    // 11. LOGOUT
    console.log("11. Logging out...");
    // Fallback since there is no logout form button in layout, we can trigger direct logout request or clean context
    await page.goto('http://localhost:8000/login');
    console.log("Verification completed successfully!");

  } catch (err) {
    console.error("Test execution failed with error:", err);
    await takeScreenshot('51_error_failure');
  } finally {
    await browser.close();
    console.log("Browser context closed.");
  }
})();
