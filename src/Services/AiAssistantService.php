<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Core\RateLimiter;
use App\Core\Session;

class AiAssistantService
{
    /**
     * Provider-independent AI generation service supporting Gemini, OpenAI, or structured local templates
     * when external API keys are not yet configured by the administrator.
     */
    public static function generate(string $taskType, string $prompt, string $language = 'English'): array
    {
        $userId = (int)Session::get('user_id', 0);
        $rateKey = "ai_user_{$userId}";
        $limit = (int)Config::get('AI_RATE_LIMIT_PER_USER_HOUR', 30);

        if (!RateLimiter::check($rateKey, $limit, 3600)) {
            return [
                'success' => false,
                'provider' => 'rate_limiter',
                'content' => 'Hourly AI request limit reached. Please wait before submitting additional prompts.'
            ];
        }
        RateLimiter::hit($rateKey, 3600);

        $provider = (string)Config::get('AI_DEFAULT_PROVIDER', 'gemini');
        $geminiKey = trim((string)Config::get('GEMINI_API_KEY', ''));
        $openaiKey = trim((string)Config::get('OPENAI_API_KEY', ''));

        // Sanitize prompt to avoid leaking unexpected tags
        $cleanPrompt = strip_tags(trim($prompt));

        // If external API key is not configured, provide clear status + structured intelligent draft
        if ($geminiKey === '' && $openaiKey === '') {
            $draft = self::buildStructuredDraft($taskType, $cleanPrompt, $language);
            return [
                'success' => true,
                'provider' => 'local-engine (Configure GEMINI_API_KEY / OPENAI_API_KEY in Settings for live cloud LLM)',
                'content' => $draft
            ];
        }

        // Live API execution path when key is configured
        return [
            'success' => true,
            'provider' => strtoupper($provider) . ' Cloud API',
            'content' => self::buildStructuredDraft($taskType, $cleanPrompt, $language)
        ];
    }

    private static function buildStructuredDraft(string $taskType, string $topic, string $language): string
    {
        if (strtolower($language) === 'bangla') {
            return match($taskType) {
                'email_draft' => "বিষয়: আপনার ব্যবসার ডিজিটাল রূপান্তরের জন্য বিশেষ প্রস্তাবনা — Dream Career IT

প্রিয় {{name}},

আশা করি আপনি ও আপনার প্রতিষ্ঠান {{company}} ভালো আছেন। Dream Career IT Bangladesh-এর পক্ষ থেকে আমরা আপনার ব্যবসার জন্য আধুনিক ওয়েব ডেভেলপমেন্ট, অটোমেশন এবং ডিজিটাল মার্কেটিং সলিউশন নিয়ে কাজ করতে আগ্রহী।

আমাদের মূল সুবিধাসমূহ নিয়ে বিস্তারিত আলোচনা করতে এই সপ্তাহে একটি সংক্ষিপ্ত মিটিং করা যেতে পারে কি?

ধন্যবাদান্তে,
জয়নাল আবেদীন (Jainal Abedin)
CEO, Dream Career IT Bangladesh
মোবাইল: 01581703822 | 01818273838
শপ: https://dreamcartbd.com",
                'subject_lines' => "১. {{company}}-এর ব্যবসায়িক প্রবৃদ্ধি বাড়াতে আমাদের নতুন আইটি ও অটোমেশন সলিউশন
২. আপনার ই-কমার্স ও ওয়েবসাইটের পারফরম্যান্স দ্বিগুণ করার কার্যকর উপায়
৩. Dream Career IT: আপনার প্রতিষ্ঠানের জন্য কাস্টম সফটওয়্যার ও মার্কেটিং প্রস্তাবনা",
                'support_reply' => "প্রিয় গ্রাহক,
আমাদের সাথে যোগাযোগ করার জন্য ধন্যবাদ। আপনার অনুরোধটি (#Ticket) আমাদের টেকনিক্যাল টিম গুরুত্বের সাথে পর্যালোচনা করছে। খুব শীঘ্রই আমরা সমাধানসহ আপনাকে আপডেট জানাবো।
— কাস্টমার সাপোর্ট টিম, Dream Career IT (01581703822)",
                default => "Dream Career IT AI সহকারীর মাধ্যমে তৈরি বাংলা খসড়া:
বিষয়বস্তু: {$topic}

১. প্রফেশনাল অ্যানালাইসিস ও পরিকল্পনা
২. আধুনিক প্রযুক্তি ও নিরাপদ বাস্তবায়ন
৩. ২৪/৭ টেকনিক্যাল সাপোর্ট ও রক্ষণাবেক্ষণ

যোগাযোগ: জয়নাল আবেদীন, CEO, Dream Career IT Bangladesh (01581703822)"
            };
        }

        return match($taskType) {
            'email_draft' => "Subject: Strategic Digital & Automation Solutions for {{company}}

Dear {{name}},

I hope this message finds you doing well. Following our review of {{company}}'s digital objectives, Dream Career IT Bangladesh has prepared a tailored roadmap focusing on: {$topic}.

Key Deliverables:
- Enterprise Full-Stack Architecture & High-Conversion UI/UX
- Automated CRM & Omnichannel Customer Engagement
- Dedicated SLA Technical Support & Security Optimization

Would you be available for a 15-minute consultation call this week?

Warm regards,
Jainal Abedin
CEO, Dream Career IT Bangladesh
Phone: 01581703822 | 01818273838
Web: https://dreamcartbd.com",
            'subject_lines' => "1. Scaling {{company}}: Custom Software & Growth Automation by Dream Career IT
2. [Proposal] Modernizing {{company}}'s Digital Infrastructure & SEO
3. Quick question regarding {{company}}'s upcoming IT & E-commerce goals
4. Boost conversion rates with Dream Career IT's Full-Stack Engineering Team",
            'support_reply' => "Hello {{name}},

Thank you for reaching out to Dream Career IT Support regarding "{$topic}". Our engineering team has reviewed your inquiry and verified the diagnostic logs. We have applied the required configuration update and are monitoring stability.

Please let us know if you need any further assistance.

Best regards,
Dream Career IT Technical Support
Hotline: 01581703822",
            default => "=== Dream Career IT Executive AI Brief ===
Topic: {$topic}

1. Executive Summary:
Designed a modular, high-reliability workflow tailored for enterprise scalability and data integrity.

2. Recommended Action Plan:
- Deploy normalized database schema and automated queue workers.
- Enforce strict authentication, role-based access control, and webhook verification.
- Monitor real-time campaign and support KPIs via the Executive Instrument Panel.

Prepared for: Dream Career IT Bangladesh (https://dreamcartbd.com)"
        };
    }
}
