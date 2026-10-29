<?php
declare(strict_types=1);

namespace App\Database\Seeders;

use PDO;
use App\Core\Database;

class DatabaseSeeder
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getConnection();
    }

    public function run(): void
    {
        $this->seedRolesAndPermissions();
        $this->seedServiceCategoriesAndServices();
        $this->seedDefaultSettings();
    }

    public function seedRolesAndPermissions(): void
    {
        $roles = [
            ['name' => 'Administrator', 'slug' => 'admin', 'description' => 'Full root access to all system modules, configurations, and user management.'],
            ['name' => 'Manager', 'slug' => 'manager', 'description' => 'Can manage campaigns, services, contacts, messages, tickets, and team assignments.'],
            ['name' => 'Staff', 'slug' => 'staff', 'description' => 'Standard staff operational access for templates, messaging, contacts, and tickets.'],
            ['name' => 'Read-only', 'slug' => 'read_only', 'description' => 'View-only access to analytics, campaigns, reports, and service catalogs.']
        ];

        $stmt = $this->db->prepare("
            INSERT INTO roles (name, slug, description, is_system)
            VALUES (:name, :slug, :description, 1)
            ON DUPLICATE KEY UPDATE description = VALUES(description)
        ");

        foreach ($roles as $role) {
            $stmt->execute($role);
        }

        // Permissions
        $permissions = [
            ['name' => 'Manage Users', 'slug' => 'manage_users', 'category' => 'Security'],
            ['name' => 'Manage System Settings', 'slug' => 'manage_settings', 'category' => 'System'],
            ['name' => 'View Audit Logs', 'slug' => 'view_audit_logs', 'category' => 'Security'],
            ['name' => 'Manage Services', 'slug' => 'manage_services', 'category' => 'Catalog'],
            ['name' => 'Manage Templates', 'slug' => 'manage_templates', 'category' => 'Marketing'],
            ['name' => 'Send Bulk Campaigns', 'slug' => 'send_campaigns', 'category' => 'Marketing'],
            ['name' => 'Manage Contacts', 'slug' => 'manage_contacts', 'category' => 'CRM'],
            ['name' => 'Manage Messaging', 'slug' => 'manage_messages', 'category' => 'Communication'],
            ['name' => 'Use AI Assistant', 'slug' => 'use_ai', 'category' => 'AI'],
            ['name' => 'Manage Projects', 'slug' => 'manage_projects', 'category' => 'Operations'],
            ['name' => 'Manage Support Tickets', 'slug' => 'manage_tickets', 'category' => 'Support'],
        ];

        $permStmt = $this->db->prepare("
            INSERT INTO permissions (name, slug, category, description)
            VALUES (:name, :slug, :category, :description)
            ON DUPLICATE KEY UPDATE name = VALUES(name)
        ");

        foreach ($permissions as $p) {
            $permStmt->execute([
                'name' => $p['name'],
                'slug' => $p['slug'],
                'category' => $p['category'],
                'description' => 'Permission to ' . strtolower($p['name'])
            ]);
        }

        // Grant all permissions to admin
        $adminId = $this->db->query("SELECT id FROM roles WHERE slug = 'admin'")->fetchColumn();
        if ($adminId) {
            $permIds = $this->db->query("SELECT id FROM permissions")->fetchAll(PDO::FETCH_COLUMN);
            $rpStmt = $this->db->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
            foreach ($permIds as $pid) {
                $rpStmt->execute([$adminId, $pid]);
            }
        }
    }

    public function seedServiceCategoriesAndServices(): void
    {
        $catalog = [
            'Web & Software Development' => [
                'icon' => 'bi-code-slash',
                'description' => 'Enterprise custom web, software, and systems development tailored to business scale.',
                'services' => [
                    'Web Design & Development' => 'Responsive, high-converting bespoke business websites built with modern web technologies.',
                    'Full-Stack Development' => 'End-to-end web apps with robust backends, reactive user interfaces, and automated workflows.',
                    'Backend Development' => 'Secure RESTful APIs, microservices, and database-intensive backends powered by PHP and relational databases.',
                    'System Development' => 'Custom ERP, POS, and automated systems tailored to organizational operational pipelines.'
                ]
            ],
            'Design & Branding' => [
                'icon' => 'bi-palette-fill',
                'description' => 'Brand visual identity, UI/UX prototyping, and print collateral creation.',
                'services' => [
                    'Graphic Design' => 'Impactful brand identity design, logos, marketing banners, and high-resolution digital creative assets.',
                    'UI/UX Design' => 'Human-centered wireframing, UX research, interactive design systems, and responsive Figma prototypes.',
                    'Print & Branding Design' => 'Corporate brochures, business cards, merchandise, and exhibition collateral production files.'
                ]
            ],
            'Digital Marketing & SEO' => [
                'icon' => 'bi-megaphone-fill',
                'description' => 'Growth-driven marketing, organic search rank boosting, and lead acquisition engines.',
                'services' => [
                    'Digital Marketing' => 'Comprehensive multi-channel digital campaigns targeting conversion and brand recognition.',
                    'Search Engine Optimization (SEO)' => 'On-page, technical, and off-page SEO strategies to dominate competitive search keywords.',
                    'Social Media Management' => 'Content calendars, community engagement, and brand reputation management across Meta and LinkedIn.',
                    'Social Media Marketing' => 'High-ROI paid advertising campaigns on Facebook, Instagram, and TikTok targeting buyer personas.',
                    'Lead Generation' => 'B2B/B2C automated inbound and outbound lead funnels with CRM capture.'
                ]
            ],
            'E-commerce & Business Systems' => [
                'icon' => 'bi-shop',
                'description' => 'E-commerce infrastructure, business automation, and enterprise resource planning.',
                'services' => [
                    'E-commerce Solutions' => 'Scalable online stores with automated local payment gateways, courier APIs, and cart optimization.',
                    'Business Automation' => 'Custom scripted workflows connecting web forms, messaging APIs, billing, and task management.',
                    'Inventory & ERP Solutions' => 'Real-time stock tracking, multi-warehouse management, supplier workflows, and financial reports.',
                    'CRM Development' => 'Tailored customer relationship management systems for tracking interactions, sales, and client history.'
                ]
            ],
            'Database, API & Data' => [
                'icon' => 'bi-database-fill-gear',
                'description' => 'Relational database architecture, API engineering, and business data analytics.',
                'services' => [
                    'Database Management' => 'MySQL/MariaDB schema normalization, indexing, query optimization, and automated backup strategies.',
                    'API Integration' => 'Seamless integration of payment gateways, SMS aggregators, courier services, and social platforms.',
                    'Data Analysis' => 'Business intelligence dashboards, customer cohort segmentation, and actionable data extraction.'
                ]
            ],
            'IT Support & Hosting' => [
                'icon' => 'bi-hdd-network-fill',
                'description' => 'Server administration, security hardening, cloud hosting, and technical maintenance.',
                'services' => [
                    'IT Support & Maintenance' => '24/7 SLA infrastructure monitoring, emergency bug fixes, and periodic system health checkups.',
                    'Web Hosting & cPanel Management' => 'DNS configuration, SSL certificate deployment, mail server tuning, and server optimizations.',
                    'Website Security & Optimization' => 'Vulnerability audits, firewall implementation, malware removal, and zero-trust hardening.',
                    'Performance Optimization' => 'Core Web Vitals tuning, database query caching, asset minification, and CDN orchestration.'
                ]
            ],
            'Office & Productivity' => [
                'icon' => 'bi-file-earmark-spreadsheet-fill',
                'description' => 'Business productivity software, cloud office deployment, and custom templates.',
                'services' => [
                    'Microsoft Office Solutions' => 'Advanced Excel macro automation, customized business reporting templates, and collaborative setups.'
                ]
            ],
            'Training & Consulting' => [
                'icon' => 'bi-mortarboard-fill',
                'description' => 'Corporate training, skill development programs, and executive technology advisory.',
                'services' => [
                    'IT Training & Education' => 'Hands-on practical training in web development, digital marketing, and modern software engineering.',
                    'Technical Consulting' => 'Technology stack evaluations, feasibility studies, and digital transformation roadmaps.'
                ]
            ],
            'Project & Client Management' => [
                'icon' => 'bi-kanban-fill',
                'description' => 'Agile project delivery, client onboarding workflows, and SLA customer service.',
                'services' => [
                    'Project Management' => 'Structured milestone management, sprint tracking, stakeholder reporting, and quality assurance.',
                    'Customer Support & Communication' => 'Multi-tier ticketing systems, live chat coverage, and omnichannel client inquiry handling.'
                ]
            ],
            'Problem Solving & Documentation' => [
                'icon' => 'bi-journal-code',
                'description' => 'Architectural troubleshooting, root-cause analysis, and technical knowledge base authoring.',
                'services' => [
                    'Problem Solving & Critical Thinking' => 'Algorithmic debugging, system bottleneck discovery, and incident post-mortems.',
                    'Technical Documentation' => 'Architecture diagrams, developer API references, and end-user operational manuals.'
                ]
            ]
        ];

        $order = 1;
        foreach ($catalog as $catName => $catData) {
            $catSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $catName), '-'));
            
            $catStmt = $this->db->prepare("
                INSERT INTO service_categories (name, slug, description, sort_order, is_active)
                VALUES (:name, :slug, :description, :order, 1)
                ON DUPLICATE KEY UPDATE description = VALUES(description), sort_order = VALUES(sort_order)
            ");
            $catStmt->execute([
                'name' => $catName,
                'slug' => $catSlug,
                'description' => $catData['description'],
                'order' => $order++
            ]);

            $catId = $this->db->query("SELECT id FROM service_categories WHERE slug = " . $this->db->quote($catSlug))->fetchColumn();

            $svcStmt = $this->db->prepare("
                INSERT INTO services (category_id, name, slug, short_description, full_description, icon_class, price_model, is_active)
                VALUES (:category_id, :name, :slug, :short_desc, :full_desc, :icon, 'Custom Quote', 1)
                ON DUPLICATE KEY UPDATE 
                    short_description = VALUES(short_description),
                    full_description = VALUES(full_description),
                    icon_class = VALUES(icon_class)
            ");

            foreach ($catData['services'] as $svcName => $svcDesc) {
                $svcSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $svcName), '-'));
                $svcStmt->execute([
                    'category_id' => $catId,
                    'name' => $svcName,
                    'slug' => $svcSlug,
                    'short_desc' => substr($svcDesc, 0, 250),
                    'full_desc' => $svcDesc,
                    'icon' => $catData['icon']
                ]);
            }
        }
    }

    public function seedDefaultSettings(): void
    {
        $settings = [
            ['general', 'app_name', 'Dream Career IT — AI Business Suite'],
            ['general', 'company_name', 'Dream Career IT Bangladesh'],
            ['general', 'company_ceo', 'Jainal Abedin'],
            ['general', 'company_phone_1', '01581703822'],
            ['general', 'company_phone_2', '01818273838'],
            ['general', 'company_email', 'jainal.dcitbd@gmail.com'],
            ['general', 'company_shop_url', 'https://dreamcartbd.com'],
            ['general', 'default_theme', 'dark'],
            ['email', 'smtp_host', 'smtp.mailtrap.io'],
            ['email', 'smtp_port', '587'],
            ['email', 'smtp_encryption', 'tls'],
            ['email', 'rate_limit_per_minute', '120'],
            ['ai', 'default_provider', 'gemini'],
            ['ai', 'max_tokens', '2048']
        ];

        $stmt = $this->db->prepare("
            INSERT INTO settings (setting_group, setting_key, setting_value)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");

        foreach ($settings as $s) {
            $stmt->execute($s);
        }
    }
}
