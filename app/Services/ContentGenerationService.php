<?php

namespace App\Services;

class ContentGenerationService
{
    /**
     * Generate realistic mock fallback data matching a schema for offline/testing.
     *
     * @param array $schema
     * @param string $prompt
     * @return array
     */
    public function generateMockDataForSchema(array $schema, string $prompt): array
    {
        $business = $this->extractFromPrompt($prompt, '/Business Description:\s*([^\n]+)/i', '/Product\/Service:\s*([^\n]+)/i', 'our product');
        $audience = $this->extractFromPrompt($prompt, '/Target Audience:\s*([^\n]+)/i', null, 'our target users');
        $cta = $this->extractFromPrompt($prompt, '/Call to Action[^\n]*:\s*([^\n]+)/i', null, 'visit our website');
        $goal = $this->extractFromPrompt($prompt, '/Campaign Goal:\s*([^\n]+)/i', '/Growth Goal:\s*([^\n]+)/i', 'grow business');

        $data = [];

        foreach ($schema['properties'] as $field => $fieldSchema) {
            $type = $fieldSchema['type'] ?? 'STRING';

            if ($type === 'ARRAY') {
                $data[$field] = match ($field) {
                    'content_calendar' => $this->mockContentCalendar($business, $audience, $goal, $cta),
                    'keywords' => $this->mockKeywords($business, $audience, $goal),
                    'headlines' => $this->mockHeadlines(),
                    'ad_copy' => $this->mockAdCopy($business, $cta),
                    'creative_ideas' => $this->mockCreativeIdeas(),
                    default => ["Sample item 1 for {$field}", "Sample item 2 for {$field}"],
                };
            } else {
                $data[$field] = $this->mockStringField($field, $business, $audience, $cta, $goal);
            }
        }

        return $data;
    }

    protected function extractFromPrompt(string $prompt, string $primaryPattern, ?string $secondaryPattern, string $default): string
    {
        if (preg_match($primaryPattern, $prompt, $matches)) {
            return trim($matches[1]);
        }
        if ($secondaryPattern && preg_match($secondaryPattern, $prompt, $matches)) {
            return trim($matches[1]);
        }
        return $default;
    }

    protected function mockContentCalendar(string $business, string $audience, string $goal, string $cta): array
    {
        return [
            "📅 Monday: Kick off the week with an educational post sharing 3 tips about how {$business} solves key problems for {$audience}.",
            "📅 Tuesday: Share a client success case study highlighting how they achieved {$goal} using our solutions.",
            "📅 Wednesday: Host a live Q&A session focused on {$audience} pain points. CTA: {$cta}",
            "📅 Thursday: Publish a blog outline breakdown and newsletter outreach. Check out our latest guide!",
            "📅 Friday: Highlight a feature spotlight - show how easy it is to automate tasks with {$business}.",
            "📅 Saturday: Share a fun team behind-the-scenes or client appreciation post.",
            "📅 Sunday: Weekly reflection: ask a thought-provoking question to {$audience} about their upcoming week's goals.",
        ];
    }

    protected function mockKeywords(string $business, string $audience, string $goal): array
    {
        return [
            "[High-Intent] {$business} demo",
            "[High-Intent] best software for {$audience}",
            "[High-Intent] how to achieve {$goal}",
            "[Competitor-Related] alternative to competitor",
            "[Competitor-Related] cheaper competitor options",
            "[Broad-Match] {$business} reviews",
            "[Broad-Match] {$audience} marketing tips",
            "[Broad-Match] outreach automation",
            "[Broad-Match] customer relationship management",
            "[Broad-Match] boost sales conversion",
        ];
    }

    protected function mockHeadlines(): array
    {
        return [
            "Supercharge Your CRM Today",
            "Automate WhatsApp & Save Time",
            "The #1 Tool for Shopify Stores",
            "14-Day Free Trial - Try Now",
            "Boost Conversions by 15%+",
        ];
    }

    protected function mockAdCopy(string $business, string $cta): array
    {
        return [
            "🚀 Stop losing sales. Our WhatsApp CRM automates customer outreach, qualifications, and reminders. CTA: Try for Free!",
            "Tired of manual follow-ups? Let AI handle your lead qualifying and scheduling. Start your 14-day trial now.",
            "Store owners are saving 20+ hours a week using our integrated plugin. Book your demo call today!",
        ];
    }

    protected function mockCreativeIdeas(): array
    {
        return [
            "💡 Idea 1: A split-screen comparison video showing a busy merchant manually typing messages vs. using our automated plugin.",
            "💡 Idea 2: A carousel graphic featuring testimonials from shop owners who increased subscriptions and qualified leads.",
            "💡 Idea 3: A minimalist product screenshot with a clean dashboard illustrating a spike in conversion metrics.",
        ];
    }

    protected function mockStringField(string $field, string $business, string $audience, string $cta, string $goal): string
    {
        return match ($field) {
            'linkedin' => "💼 **Grow Your Business Smarter**\n\nAre you looking to scale your marketing and connect with the right audience? {$business} helps {$audience} automate outreach and achieve {$goal} effortlessly.\n\nKey Benefits:\n• Time-saving automation\n• Personalized message templates\n• Real-time lead qualifying\n\n👉 Let's talk! CTA: {$cta}\n\n#BusinessGrowth #CRM #Automation",

            'twitter' => "Struggling to scale your customer outreach? 🚀\n\n{$business} helps {$audience} automate qualify and engage leads in real time. Reach your goals faster without the manual grind.\n\nTry it free today! 👇\n{$cta}\n\n#Shopify #SaaS",

            'telegram' => "📢 **New Update for {$audience}!**\n\nWe just launched automated WhatsApp CRM workflows designed specially to boost conversions.\n\nWhat you get:\n✅ Automatic follow-ups\n✅ Dynamic customer tags\n✅ Integrated scheduling\n\n🔗 Click here to get started: {$cta}",

            'facebook' => "Hey Shopify store owners! 🛍️ Are you tired of leaving money on the table due to slow follow-ups? With {$business}, you can automate chat campaigns and qualify leads in real-time. Join thousands of merchants who trust us to grow their brand. \n\nClick below to start your 14-day free trial:\n👉 {$cta}",

            'reddit' => "Title: How we automated our customer qualifying workflow and saved 15+ hours a week\n\nHey everyone, wanted to share a quick value post. If you're running a storefront or SaaS product, manual lead follow-up is a huge bottleneck. We realized that by setting up an automated WhatsApp CRM flow, we could filter high-intent leads instantly. Here's our exact framework:\n1. Auto-tag users on initial inquiry.\n2. Send a personalized calendar link if they score above 70.\n3. Send automated reminders to minimize no-shows.\n\nHope this helps! Let me know if you have any questions.",

            'image_prompt' => 'modern tech office workspace neon light workspace illustration simple graphic',

            'blog_outline' => "# The Ultimate Guide to Sales Outreach for Shopify Stores\n\n## Introduction\nIn today's fast-paced e-commerce environment, reaching out to customers manually is no longer viable. Here is how automation can help.\n\n## Section 1: The WhatsApp CRM Advantage\n- Why WhatsApp has a 98% open rate compared to email.\n- How to integrate your customer database for instant messaging.\n\n## Section 2: Qualifying Leads on Autopilot\n- Setting up triggers based on customer action.\n- Scraping and analyzing intent signals.\n\n## Section 3: Reaching {$goal}\n- Crafting the perfect offer.\n- Best practices for follow-ups.\n\n## Conclusion & Next Steps\nSupercharge your store today. CTA: {$cta}",

            'newsletter_copy' => "Subject: 🚀 3 Secrets to Scale Your E-commerce Store\n\nHello Friend,\n\nRunning a business is hard, especially when you are doing everything manually. If you are struggling to follow up with leads, we have good news.\n\nWith {$business}, you can qualify prospects and schedule meetings while you sleep.\n\nHere is what our clients are saying:\n'We saw a 15% increase in subscriptions in just one week!'\n\nReady to scale?\n\n[Try Lakshya.ai for Free]({$cta})\n\nBest,\nThe Lakshya Team",

            'target_audience' => "🎯 **Primary Persona**: Shopify merchants, D2C store owners, and SaaS founders looking to streamline their messaging.\n🎯 **Demographics**: Age 25-50, interest in e-commerce, digital marketing, and automation tools.\n🎯 **Behaviors**: Active on LinkedIn, Twitter, and Shopify community forums.",

            'landing_page' => "🌐 **Value Proposition Headline**: Turn Browsers Into Buyers with Automated WhatsApp CRM\n\n🌐 **Core Benefits to Display**:\n- **Instant Messaging**: Connect on the channels your customers use.\n- **Automated Qualifying**: AI filters high-intent leads for you.\n- **Calendar Sync**: Automatically book demos.\n\n🌐 **Call to Action**: [Start Your 14-Day Free Trial]",

            'title' => "Automate Your Sales Outreach Today with {$business}",

            'description' => "Are you tired of manual customer follow-ups? 🚀\n\n{$business} helps {$audience} qualify leads and schedule sales demos automatically on WhatsApp. Save hours of manual typing and boost your Shopify conversion rates by 15%+.\n\nStart your 14-day free trial now!\n👉 {$cta}\n\n#ShopifyCRM #SalesAutomation",

            default => "Realistic content generated for field '{$field}' matching business '{$business}'.",
        };
    }
}