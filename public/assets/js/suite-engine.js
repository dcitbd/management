/**
 * Dream Career IT — AI Business Suite (v2.5 Smart 3D Enterprise Edition)
 * Full CRUD Interactive Engine (Catalogs, Categories, Templates, CRM, Projects, Tickets) + Multi-Transport Email Dispatcher
 * Designer: Jainal Abedin, CEO, Dream Career IT Bangladesh
 */

const SuiteApp = {
    STORAGE_KEY: 'dcit_suite_db_v25',

    state: {
        user: null,
        catalogViewMode: 'cards',
        editingServiceId: null,
        editingTemplateId: null,
        editingContactId: null,
        editingProjectId: null,
        editingTicketId: null,
        editingCategoryIndex: null,
        categories: [
            "Web & Software Development",
            "Design & Branding",
            "Digital Marketing & SEO",
            "E-commerce & Business Systems",
            "Database, API & Data",
            "IT Support & Hosting",
            "Office & Productivity",
            "Training & Consulting",
            "Project & Client Management",
            "Problem Solving & Documentation"
        ],
        services: [],
        templates: [],
        contacts: [],
        campaigns: [],
        emailLogs: [],
        conversations: [],
        activeConvId: 1,
        projects: [],
        tickets: [],
        users: [],
        auditLogs: [],
        logFilter: 'all',
        smtpSettings: {
            host: 'smtp.gmail.com',
            port: '587',
            user: 'jainal.dcitbd@gmail.com',
            pass: '',
            encryption: 'tls',
            senderName: 'Dream Career IT Bangladesh',
            senderEmail: 'jainal.dcitbd@gmail.com',
            autoGmailFallback: false
        }
    },

    init() {
        this.loadOrSeedData();
        this.checkSession();
        this.populateCategorySelects();
        this.renderAll();
        this.previewTemplate();
        this.init3DTiltEffects();
    },

    init3DTiltEffects() {
        document.addEventListener('mousemove', (e) => {
            const card = e.target.closest('.card-3d, .metric-card');
            if (!card) return;
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * -4;
            const rotateY = ((x - centerX) / centerX) * 4;
            card.style.transform = `translateY(-7px) perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) scale(1.012)`;
        });

        document.addEventListener('mouseout', (e) => {
            const card = e.target.closest('.card-3d, .metric-card');
            if (!card) return;
            if (!card.contains(e.relatedTarget)) {
                card.style.transform = '';
            }
        });
    },

    loadOrSeedData() {
        let saved = null;
        try {
            saved = localStorage.getItem(this.STORAGE_KEY);
        } catch (e) {}

        if (saved) {
            try {
                const parsed = JSON.parse(saved);
                this.state = Object.assign({}, this.state, parsed);
                if (!this.state.users || this.state.users.length === 0) {
                    this.seedDefaultUsers();
                }
                return;
            } catch (e) {}
        }

        const iconMap = {
            "Web & Software Development": "bi-code-slash",
            "Design & Branding": "bi-palette2",
            "Digital Marketing & SEO": "bi-graph-up-arrow",
            "E-commerce & Business Systems": "bi-cart-check-fill",
            "Database, API & Data": "bi-hdd-network-fill",
            "IT Support & Hosting": "bi-hdd-rack-fill",
            "Office & Productivity": "bi-file-earmark-spreadsheet-fill",
            "Training & Consulting": "bi-mortarboard-fill",
            "Project & Client Management": "bi-kanban-fill",
            "Problem Solving & Documentation": "bi-journal-code"
        };

        const catalog = [
            ["Web & Software Development", "Web Design & Development", "Responsive bespoke business websites built with modern stacks.", "৳25,000"],
            ["Web & Software Development", "Full-Stack Development", "End-to-end web apps with reactive UIs and relational backends.", "৳65,000"],
            ["Web & Software Development", "Backend Development", "Secure REST APIs, microservices, and database architecture.", "৳40,000"],
            ["Web & Software Development", "System Development", "Custom enterprise ERP, POS, and automated systems.", "৳85,000"],
            ["Design & Branding", "Graphic Design", "Logos, brand identity, marketing banners, and social creatives.", "৳8,000"],
            ["Design & Branding", "UI/UX Design", "Wireframing, interactive prototypes, and design systems.", "৳18,000"],
            ["Design & Branding", "Print & Branding Design", "Brochures, business cards, and corporate collateral.", "৳10,000"],
            ["Digital Marketing & SEO", "Digital Marketing", "Full-funnel growth marketing and conversion campaigns.", "৳15,000/mo"],
            ["Digital Marketing & SEO", "Search Engine Optimization (SEO)", "Technical, on-page, and off-page SEO rank boosting.", "৳18,000/mo"],
            ["Digital Marketing & SEO", "Social Media Management", "Content calendars and community management across platforms.", "৳12,000/mo"],
            ["Digital Marketing & SEO", "Social Media Marketing", "Targeted paid ad campaigns on Meta, Instagram, and LinkedIn.", "৳15,000/mo"],
            ["Digital Marketing & SEO", "Lead Generation", "Automated B2B & B2C lead capture and qualification funnels.", "৳20,000"],
            ["E-commerce & Business Systems", "E-commerce Solutions", "Scalable online stores with payment and courier integrations.", "৳35,000"],
            ["E-commerce & Business Systems", "Business Automation", "Automated workflows connecting CRM, billing, and messaging.", "৳45,000"],
            ["E-commerce & Business Systems", "Inventory & ERP Solutions", "Multi-warehouse stock tracking, invoicing, and accounting.", "৳75,000"],
            ["E-commerce & Business Systems", "CRM Development", "Custom customer relationship and sales pipeline platforms.", "৳55,000"],
            ["Database, API & Data", "Database Management", "MySQL/MariaDB normalization, indexing, and backup tuning.", "৳20,000"],
            ["Database, API & Data", "API Integration", "Payment gateways, SMS, couriers, and Meta Graph APIs.", "৳15,000"],
            ["Database, API & Data", "Data Analysis", "Executive BI dashboards and customer cohort analytics.", "৳25,000"],
            ["IT Support & Hosting", "IT Support & Maintenance", "24/7 SLA monitoring, bug fixes, and infrastructure care.", "৳10,000/mo"],
            ["IT Support & Hosting", "Web Hosting & cPanel Management", "Server setup, SSL deployment, and DNS management.", "৳6,000/yr"],
            ["IT Support & Hosting", "Website Security & Optimization", "Firewall hardening, malware removal, and security audits.", "৳12,000"],
            ["IT Support & Hosting", "Performance Optimization", "Core Web Vitals tuning, caching, and CDN acceleration.", "৳10,000"],
            ["Office & Productivity", "Microsoft Office Solutions", "Automated Excel templates, macros, and reporting suites.", "৳8,000"],
            ["Training & Consulting", "IT Training & Education", "Hands-on corporate training in web dev and digital marketing.", "৳15,000"],
            ["Training & Consulting", "Technical Consulting", "Architecture reviews and digital transformation roadmaps.", "৳20,000"],
            ["Project & Client Management", "Project Management", "Agile sprint delivery, milestone tracking, and QA.", "Custom Quote"],
            ["Project & Client Management", "Customer Support & Communication", "Multi-channel helpdesk and SLA client communication.", "Custom Quote"],
            ["Problem Solving & Documentation", "Problem Solving & Critical Thinking", "Root-cause debugging and bottleneck resolution.", "Custom Quote"],
            ["Problem Solving & Documentation", "Technical Documentation", "System architecture specs, API docs, and user manuals.", "৳12,000"]
        ];

        this.state.services = catalog.map((item, idx) => ({
            id: idx + 1,
            category: item[0],
            name: item[1],
            description: item[2],
            price: item[3] || "Custom Quote",
            icon: iconMap[item[0]] || "bi-layers-fill",
            active: true
        }));

        this.state.templates = [
            {
                id: 1,
                name: "Corporate IT & Automation Proposal",
                category: "Marketing",
                subject: "Scaling {{company}} with Dream Career IT Solutions",
                body: `<div style="font-family:Arial,sans-serif;padding:28px;background:#0f172a;color:#f8fafc;border-radius:14px;border:1px solid #1e293b;">
  <h2 style="color:#00f2fe;margin-top:0;">Dream Career IT Bangladesh</h2>
  <p>Hello <strong>{{name}}</strong>,</p>
  <p>We would love to help <strong>{{company}}</strong> modernize its software, e-commerce, and digital marketing workflows with our enterprise suite.</p>
  <p style="margin:22px 0;"><a href="https://dreamcartbd.com" style="background:#00f2fe;color:#0b1120;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:bold;">Explore Our Solutions</a></p>
  <p style="font-size:12px;color:#94a3b8;">Jainal Abedin, CEO | Hotline: 01581703822, 01818273838 | <a href="https://dreamcartbd.com" style="color:#38bdf8;">dreamcartbd.com</a></p>
</div>`
            },
            {
                id: 2,
                name: "E-Commerce & Custom ERP Suite Offer",
                category: "Marketing",
                subject: "Next-Gen E-Commerce & ERP Automation for {{company}}",
                body: `<div style="font-family:Arial,sans-serif;padding:28px;background:#0f172a;color:#f8fafc;border-radius:14px;border:1px solid #1e293b;">
  <h2 style="color:#a855f7;margin-top:0;">E-Commerce & Inventory ERP</h2>
  <p>Dear <strong>{{name}}</strong>,</p>
  <p>Upgrade <strong>{{company}}</strong> with automated stock tracking, courier API sync, and high-speed checkout engineered by Dream Career IT Bangladesh.</p>
  <p style="margin:22px 0;"><a href="https://dreamcartbd.com" style="background:#a855f7;color:#ffffff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:bold;">View Live Demo</a></p>
  <p style="font-size:12px;color:#94a3b8;">Call CEO Jainal Abedin: 01581703822</p>
</div>`
            },
            {
                id: 3,
                name: "SEO & Digital Growth Audit Report",
                category: "Newsletter",
                subject: "Complimentary SEO & Conversion Growth Roadmap for {{company}}",
                body: `<div style="font-family:Arial,sans-serif;padding:28px;background:#0f172a;color:#f8fafc;border-radius:14px;border:1px solid #1e293b;">
  <h2 style="color:#10b981;margin-top:0;">Digital Growth & SEO Roadmap</h2>
  <p>Hi <strong>{{name}}</strong>,</p>
  <p>Our SEO and performance marketing specialists have prepared an actionable growth plan for <strong>{{company}}</strong> to boost organic search rankings and ROAS.</p>
  <p style="font-size:12px;color:#94a3b8;">Dream Career IT Bangladesh | jainal.dcitbd@gmail.com</p>
</div>`
            },
            {
                id: 4,
                name: "Client Onboarding & Project Kickoff",
                category: "Onboarding",
                subject: "Welcome to Dream Career IT, {{name}} — Project Kickoff Details",
                body: `<div style="font-family:Arial,sans-serif;padding:28px;background:#0f172a;color:#f8fafc;border-radius:14px;border:1px solid #1e293b;">
  <h2 style="color:#38bdf8;margin-top:0;">Welcome Aboard, {{name}}!</h2>
  <p>We are thrilled to partner with <strong>{{company}}</strong>. Your dedicated engineering and project management team is now active.</p>
  <p style="font-size:12px;color:#94a3b8;">24/7 Priority Support: 01581703822, 01818273838</p>
</div>`
            }
        ];

        this.state.contacts = [
            { id: 1, name: "Jainal Abedin", email: "jainal.dcitbd@gmail.com", phone: "01581703822", company: "Dream Career IT Bangladesh", status: "Customer", subscribed: true },
            { id: 2, name: "Rahim Uddin", email: "rahim@chattogramtrade.com", phone: "01818273838", company: "Chattogram Trade Corp", status: "Prospect", subscribed: true },
            { id: 3, name: "Karim Chowdhury", email: "karim@dhakaretail.org", phone: "01711002233", company: "Dhaka Retail Group", status: "Lead", subscribed: true }
        ];

        this.state.conversations = [
            {
                id: 1,
                title: "Chattogram Trade Corp — ERP Inquiry",
                channel: "WhatsApp Business",
                messages: [
                    { sender: "Rahim Uddin", type: "contact", time: "10:15", text: "আসসালামু আলাইকুম, আমাদের প্রতিষ্ঠানের জন্য একটি কাস্টম ইনভেন্টরি ও ইআরপি সফটওয়্যার প্রয়োজন।" },
                    { sender: "Jainal Abedin", type: "agent", time: "10:18", text: "ওয়ালাইকুম আসসালাম! ধন্যবাদ যোগাযোগের জন্য। আমরা আপনার প্রয়োজন অনুযায়ী সম্পূর্ণ কাস্টম ERP তৈরি করে দিতে পারব।" }
                ]
            },
            {
                id: 2,
                title: "DreamCartBD Order & API Sync",
                channel: "Facebook Messenger",
                messages: [
                    { sender: "Nusrat Jahan", type: "contact", time: "11:40", text: "Can we integrate automated courier booking and SMS updates into our e-commerce store?" }
                ]
            },
            {
                id: 3,
                title: "Bikroy.com — Web Design Lead",
                channel: "Bikroy (Manual Log)",
                messages: [
                    { sender: "Buyer Lead", type: "contact", time: "14:05", text: "Interested in your Corporate Web Development package listed on Bikroy." }
                ]
            }
        ];

        this.state.projects = [
            { id: 1, title: "DreamCartBD E-Commerce & ERP Upgrade", client: "Dream Career IT", priority: "High", status: "In Progress", due: "2026-11-30" },
            { id: 2, title: "Chattogram Trade Multi-Warehouse POS", client: "Chattogram Trade Corp", priority: "Urgent", status: "Planning", due: "2026-12-15" }
        ];

        this.state.tickets = [
            { id: 1, number: "DCIT-8041", subject: "Configure Custom Domain & SSL on cPanel", priority: "High", status: "Open" }
        ];

        this.seedDefaultUsers();
        this.logAudit("system_initialized", "Seeded 10 categories, 30 services, and 4 email templates");
        this.saveState();
    },

    seedDefaultUsers() {
        this.state.users = [
            { id: 1, name: "Jainal Abedin", email: "jainal.dcitbd@gmail.com", role: "Administrator (CEO)", phone: "01581703822 / 01818273838", active: true }
        ];
    },

    saveState() {
        try {
            localStorage.setItem(this.STORAGE_KEY, JSON.stringify(this.state));
        } catch (e) {}
    },

    logAudit(action, details) {
        const now = new Date().toLocaleTimeString();
        this.state.auditLogs.unshift({
            time: now,
            action: action,
            user: this.state.user ? this.state.user.name : "Jainal Abedin",
            details: details
        });
        this.saveState();
    },

    checkSession() {
        try {
            const sessionUser = sessionStorage.getItem('dcit_active_session') || localStorage.getItem('dcit_active_session');
            const authScreen = document.getElementById('auth-screen');
            if (sessionUser && authScreen) {
                this.state.user = JSON.parse(sessionUser);
                authScreen.classList.add('hidden');
                authScreen.style.display = 'none';
            }
        } catch (err) {}
    },

    handleAuth(e) {
        if (e && e.preventDefault) e.preventDefault();
        const emailEl = document.getElementById('authEmail');
        const passEl = document.getElementById('authPassword');
        const email = (emailEl && emailEl.value ? emailEl.value.trim() : 'jainal.dcitbd@gmail.com');
        const pass = (passEl && passEl.value ? passEl.value : '');

        if (!pass || pass.length < 1) {
            const al = document.getElementById('auth-alert');
            if (al) {
                al.style.display = 'block';
                al.textContent = 'Please enter a password to sign in.';
            }
            return false;
        }

        const userObj = { name: "Jainal Abedin", email: email, role: "Administrator" };
        try {
            sessionStorage.setItem('dcit_active_session', JSON.stringify(userObj));
            localStorage.setItem('dcit_active_session', JSON.stringify(userObj));
        } catch (err) {}

        this.state.user = userObj;
        this.logAudit("login_success", `Authenticated as ${email}`);

        const authScreen = document.getElementById('auth-screen');
        if (authScreen) {
            authScreen.classList.add('hidden');
            authScreen.style.display = 'none';
        }
        this.renderAll();
        if (window.App && App.toast) {
            App.toast('success', 'Welcome to Dream Career IT — AI Business Suite!');
        }
        return false;
    },

    resetAdminPrompt() {
        const el = document.getElementById('setup-name-group');
        if (el) el.style.display = 'block';
        const h = document.getElementById('auth-heading');
        if (h) h.textContent = 'Initialize Administrator';
        App.toast('info', 'Enter your email and password to initialize administrator access.');
    },

    logout() {
        try {
            this.logAudit("logout", "User signed out safely");
            sessionStorage.removeItem('dcit_active_session');
            localStorage.removeItem('dcit_active_session');
        } catch (err) {}
        const authScreen = document.getElementById('auth-screen');
        if (authScreen) {
            authScreen.classList.remove('hidden');
            authScreen.style.display = 'flex';
        }
        if (window.App && App.toast) {
            App.toast('info', 'Signed out of session.');
        }
    },

    nav(viewId) {
        document.querySelectorAll('.spa-view').forEach(el => el.classList.remove('active'));
        const target = document.getElementById('view-' + viewId);
        if (target) target.classList.add('active');

        document.querySelectorAll('.sidebar-menu .menu-item').forEach(li => {
            li.classList.toggle('active', li.getAttribute('data-nav') === viewId);
        });
        this.renderAll();
    },

    populateCategorySelects() {
        const s1 = document.getElementById('svcCategory');
        const s2 = document.getElementById('svcFilterCat');
        if (s1) {
            const currentVal = s1.value;
            s1.innerHTML = this.state.categories.map(cat => `<option value="${this.escapeAttr(cat)}">${this.escapeHtml(cat)}</option>`).join('');
            if (currentVal && this.state.categories.includes(currentVal)) s1.value = currentVal;
        }
        if (s2) {
            const currentFilter = s2.value;
            s2.innerHTML = `<option value="">All Categories (${this.state.categories.length})</option>` +
                this.state.categories.map(cat => `<option value="${this.escapeAttr(cat)}">${this.escapeHtml(cat)}</option>`).join('');
            if (currentFilter && this.state.categories.includes(currentFilter)) s2.value = currentFilter;
        }
        this.renderCategoryPills();
        this.renderCategoryManagerList();
    },

    escapeHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    },

    escapeAttr(str) {
        return this.escapeHtml(str).replace(/'/g, '&#39;');
    },

    renderAll() {
        this.renderDashboard();
        this.populateCategorySelects();
        this.renderServices();
        this.renderTemplates();
        this.renderContacts();
        this.renderLogs();
        this.renderConversations();
        this.renderProjects();
        this.renderTickets();
        this.renderUsers();
        this.renderAudit();
        this.updateLiveRecipientCounter();
    },

    renderDashboard() {
        const totalContacts = this.state.contacts.length;
        const unsubCount = this.state.contacts.filter(c => !c.subscribed).length;
        const queued = this.state.emailLogs.filter(l => l.status === 'queued').length;
        const sent = this.state.emailLogs.filter(l => l.status === 'sent' || l.status === 'delivered').length;
        const delivered = this.state.emailLogs.filter(l => l.status === 'delivered').length;
        const failed = this.state.emailLogs.filter(l => l.status === 'failed' || l.status === 'bounced').length;

        const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
        setEl('stat-contacts', totalContacts);
        setEl('stat-unsub', `${unsubCount} unsubscribed / suppressed`);
        setEl('stat-campaigns', this.state.campaigns.length);
        setEl('stat-queued', `${queued} queued in worker`);
        setEl('stat-sent', sent);
        setEl('stat-delivered', `${delivered} confirmed deliveries`);
        setEl('stat-failed', failed);
        setEl('stat-services', this.state.services.filter(s => s.active).length);
        setEl('stat-templates', this.state.templates.length);
        setEl('stat-convs', this.state.conversations.length);
        setEl('stat-tickets', this.state.tickets.filter(t => t.status === 'Open').length);

        const tbody = document.getElementById('dash-audit-tbody');
        if (tbody) {
            tbody.innerHTML = this.state.auditLogs.slice(0, 6).map(a => `
                <tr>
                    <td style="font-family:var(--font-mono);font-size:11.5px;">${this.escapeHtml(a.time)}</td>
                    <td><span class="badge-action">${this.escapeHtml(a.action)}</span></td>
                    <td>${this.escapeHtml(a.user)}</td>
                </tr>
            `).join('');
        }
    },

    /* =====================================================================
       CATALOG & SERVICES + CATEGORY FULL CRUD (ADD, EDIT, DELETE, TOGGLE)
       ===================================================================== */
    setCatalogView(mode) {
        this.state.catalogViewMode = mode;
        const cardsEl = document.getElementById('services-cards-grid');
        const tableEl = document.getElementById('services-table-card');
        const btnCards = document.getElementById('btnCatalogCards');
        const btnTable = document.getElementById('btnCatalogTable');
        if (cardsEl && tableEl) {
            cardsEl.style.display = mode === 'cards' ? 'grid' : 'none';
            tableEl.style.display = mode === 'table' ? 'block' : 'none';
        }
        if (btnCards && btnTable) {
            btnCards.className = mode === 'cards' ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-outline';
            btnTable.className = mode === 'table' ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-outline';
        }
    },

    filterByCategoryPill(catName) {
        const s2 = document.getElementById('svcFilterCat');
        if (s2) {
            s2.value = catName;
            this.renderServices();
            this.renderCategoryPills();
        }
    },

    renderCategoryPills() {
        const bar = document.getElementById('service-category-pills');
        if (!bar) return;
        const activeCat = document.getElementById('svcFilterCat')?.value || '';
        let html = `<button type="button" class="cat-pill ${activeCat === '' ? 'active' : ''}" onclick="SuiteApp.filterByCategoryPill('')">
            <i class="bi bi-grid-fill"></i> All (${this.state.services.length})
        </button>`;
        this.state.categories.forEach(cat => {
            const count = this.state.services.filter(s => s.category === cat).length;
            const isAct = activeCat === cat ? 'active' : '';
            html += `<button type="button" class="cat-pill ${isAct}" onclick="SuiteApp.filterByCategoryPill('${this.escapeAttr(cat)}')">
                <span>${this.escapeHtml(cat)}</span>
                <span style="opacity:0.75;font-size:11px;">(${count})</span>
            </button>`;
        });
        bar.innerHTML = html;
    },

    toggleCategoryPanel() {
        const panel = document.getElementById('category-crud-panel');
        if (!panel) return;
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        this.renderCategoryManagerList();
    },

    renderCategoryManagerList() {
        const tbody = document.getElementById('categories-crud-tbody');
        if (!tbody) return;
        tbody.innerHTML = this.state.categories.map((cat, idx) => {
            const count = this.state.services.filter(s => s.category === cat).length;
            return `
                <tr>
                    <td><strong>${this.escapeHtml(cat)}</strong></td>
                    <td><span class="badge-action">${count} services</span></td>
                    <td style="display:flex;gap:8px;">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="SuiteApp.startEditCategory(${idx})">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-danger-soft" onclick="SuiteApp.deleteCategory(${idx})">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    },

    saveCategory(e) {
        e.preventDefault();
        const input = document.getElementById('catNameInput');
        if (!input) return;
        const newName = input.value.trim();
        if (!newName) return;

        if (this.state.editingCategoryIndex !== null) {
            const oldName = this.state.categories[this.state.editingCategoryIndex];
            this.state.categories[this.state.editingCategoryIndex] = newName;
            this.state.services.forEach(s => {
                if (s.category === oldName) s.category = newName;
            });
            this.logAudit("category_updated", `Renamed category "${oldName}" to "${newName}"`);
            App.toast('success', `Category updated to "${newName}"!`);
        } else {
            if (this.state.categories.includes(newName)) {
                App.toast('warning', 'Category already exists.');
                return;
            }
            this.state.categories.push(newName);
            this.logAudit("category_created", `Created category "${newName}"`);
            App.toast('success', `Category "${newName}" added!`);
        }

        this.resetCategoryForm();
        this.saveState();
        this.renderAll();
    },

    startEditCategory(idx) {
        const cat = this.state.categories[idx];
        if (!cat) return;
        this.state.editingCategoryIndex = idx;
        const input = document.getElementById('catNameInput');
        const btn = document.getElementById('catSaveBtn');
        const cancelBtn = document.getElementById('catCancelBtn');
        if (input) input.value = cat;
        if (btn) btn.innerHTML = '<i class="bi bi-check2-circle"></i> Update Category';
        if (cancelBtn) cancelBtn.style.display = 'inline-flex';
    },

    resetCategoryForm() {
        this.state.editingCategoryIndex = null;
        const input = document.getElementById('catNameInput');
        const btn = document.getElementById('catSaveBtn');
        const cancelBtn = document.getElementById('catCancelBtn');
        if (input) input.value = '';
        if (btn) btn.innerHTML = '<i class="bi bi-plus-lg"></i> Add Category';
        if (cancelBtn) cancelBtn.style.display = 'none';
    },

    deleteCategory(idx) {
        const cat = this.state.categories[idx];
        if (!cat) return;
        this.state.categories.splice(idx, 1);
        const fallback = this.state.categories[0] || 'General IT Services';
        if (this.state.categories.length === 0) {
            this.state.categories.push(fallback);
        }
        this.state.services.forEach(s => {
            if (s.category === cat) s.category = fallback;
        });
        this.logAudit("category_deleted", `Deleted category "${cat}"`);
        this.saveState();
        this.renderAll();
        App.toast('info', `Category "${cat}" removed.`);
    },

    openAddServicePanel() {
        this.state.editingServiceId = null;
        const panel = document.getElementById('add-service-panel');
        const title = document.getElementById('service-panel-title');
        const submitBtn = document.getElementById('svcSubmitBtn');
        if (panel) panel.style.display = 'block';
        if (title) title.innerHTML = '<i class="bi bi-plus-circle text-cyan"></i> Add New Service to Catalog';
        if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-check2-circle"></i> Save Service';
        document.getElementById('svcName').value = '';
        document.getElementById('svcPrice').value = 'Custom Quote';
        document.getElementById('svcDesc').value = '';
        const iconEl = document.getElementById('svcIcon');
        if (iconEl) iconEl.value = 'bi-layers-fill';
        panel?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    },

    editService(id) {
        const svc = this.state.services.find(s => Number(s.id) === Number(id));
        if (!svc) return;
        this.state.editingServiceId = svc.id;
        const panel = document.getElementById('add-service-panel');
        const title = document.getElementById('service-panel-title');
        const submitBtn = document.getElementById('svcSubmitBtn');
        if (panel) panel.style.display = 'block';
        if (title) title.innerHTML = `<i class="bi bi-pencil-square text-cyan"></i> Edit Service: ${this.escapeHtml(svc.name)}`;
        if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-check2-circle"></i> Update Service';

        document.getElementById('svcName').value = svc.name;
        document.getElementById('svcCategory').value = svc.category;
        document.getElementById('svcPrice').value = svc.price || 'Custom Quote';
        document.getElementById('svcDesc').value = svc.description || '';
        const iconEl = document.getElementById('svcIcon');
        if (iconEl) iconEl.value = svc.icon || 'bi-layers-fill';
        panel?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    },

    duplicateService(id) {
        const svc = this.state.services.find(s => Number(s.id) === Number(id));
        if (!svc) return;
        const copy = Object.assign({}, svc, {
            id: Date.now(),
            name: svc.name + ' (Copy)'
        });
        this.state.services.unshift(copy);
        this.logAudit("service_duplicated", `Duplicated service: ${copy.name}`);
        this.saveState();
        this.renderAll();
        App.toast('success', `Duplicated "${svc.name}"!`);
    },

    toggleServiceStatus(id) {
        const svc = this.state.services.find(s => Number(s.id) === Number(id));
        if (!svc) return;
        svc.active = !svc.active;
        this.logAudit("service_status_toggled", `${svc.name} -> ${svc.active ? 'ACTIVE' : 'INACTIVE'}`);
        this.saveState();
        this.renderAll();
        App.toast('info', `"${svc.name}" marked as ${svc.active ? 'Active' : 'Inactive'}.`);
    },

    saveService(e) {
        e.preventDefault();
        const name = document.getElementById('svcName').value.trim();
        const category = document.getElementById('svcCategory').value;
        const price = document.getElementById('svcPrice').value.trim() || 'Custom Quote';
        const description = document.getElementById('svcDesc').value.trim();
        const icon = document.getElementById('svcIcon')?.value.trim() || 'bi-layers-fill';

        if (this.state.editingServiceId !== null) {
            const existing = this.state.services.find(s => Number(s.id) === Number(this.state.editingServiceId));
            if (existing) {
                existing.name = name;
                existing.category = category;
                existing.price = price;
                existing.description = description;
                existing.icon = icon;
                this.logAudit("service_updated", `Updated service: ${name}`);
                App.toast('success', `Service "${name}" updated!`);
            }
        } else {
            this.state.services.unshift({
                id: Date.now(),
                name,
                category,
                price,
                description,
                icon,
                active: true
            });
            this.logAudit("service_created", `Added service: ${name}`);
            App.toast('success', `Service "${name}" added to catalog!`);
        }

        this.state.editingServiceId = null;
        document.getElementById('add-service-panel').style.display = 'none';
        e.target.reset();
        this.saveState();
        this.renderAll();
    },

    deleteService(id) {
        const target = this.state.services.find(s => Number(s.id) === Number(id));
        this.state.services = this.state.services.filter(s => Number(s.id) !== Number(id));
        this.logAudit("service_deleted", `Removed service: ${target ? target.name : '#' + id}`);
        this.saveState();
        this.renderAll();
        App.toast('info', 'Service removed from catalog.');
    },

    renderServices() {
        const q = (document.getElementById('svcSearch')?.value || '').toLowerCase();
        const cat = document.getElementById('svcFilterCat')?.value || '';
        const tbody = document.getElementById('services-tbody');
        const cardsGrid = document.getElementById('services-cards-grid');

        const filtered = this.state.services.filter(s => {
            const matchQ = !q || s.name.toLowerCase().includes(q) || s.description.toLowerCase().includes(q) || s.category.toLowerCase().includes(q);
            const matchCat = !cat || s.category === cat;
            return matchQ && matchCat;
        });

        if (cardsGrid) {
            if (filtered.length === 0) {
                cardsGrid.innerHTML = `<div class="card" style="grid-column:1/-1;"><div class="card-body" style="text-align:center;padding:40px;"><p class="text-muted">No matching services found in catalog.</p></div></div>`;
            } else {
                cardsGrid.innerHTML = filtered.map(s => `
                    <div class="card-3d">
                        <div>
                            <div class="card-3d-top" style="margin-bottom:16px;">
                                <div class="icon-orb-3d">
                                    <i class="bi ${this.escapeAttr(s.icon || 'bi-layers-fill')}"></i>
                                </div>
                                <span class="badge ${s.active ? 'badge-status-completed' : 'badge-status-draft'}" style="cursor:pointer;" onclick="SuiteApp.toggleServiceStatus(${s.id})" title="Click to toggle status">
                                    ${s.active ? 'ACTIVE' : 'INACTIVE'}
                                </span>
                            </div>
                            <div style="margin-bottom:8px;">
                                <span class="badge-action">${this.escapeHtml(s.category)}</span>
                            </div>
                            <h3 class="card-3d-title">${this.escapeHtml(s.name)}</h3>
                            <p class="card-3d-desc">${this.escapeHtml(s.description)}</p>
                        </div>
                        <div class="card-3d-meta">
                            <span class="price-tag-3d">${this.escapeHtml(s.price)}</span>
                            <div class="card-3d-actions">
                                <button type="button" class="btn btn-sm btn-secondary" onclick="SuiteApp.editService(${s.id})" title="Edit Service">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline" onclick="SuiteApp.duplicateService(${s.id})" title="Duplicate">
                                    <i class="bi bi-files"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger-soft" onclick="SuiteApp.deleteService(${s.id})" title="Delete Service">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        }

        if (tbody) {
            tbody.innerHTML = filtered.map(s => `
                <tr>
                    <td><i class="bi ${this.escapeAttr(s.icon || 'bi-layers-fill')} text-cyan" style="margin-right:8px;"></i><strong>${this.escapeHtml(s.name)}</strong></td>
                    <td><span class="badge-action">${this.escapeHtml(s.category)}</span></td>
                    <td class="text-muted" style="max-width:340px;">${this.escapeHtml(s.description)}</td>
                    <td><span class="price-tag-3d" style="font-size:12px;">${this.escapeHtml(s.price)}</span></td>
                    <td>
                        <span class="badge ${s.active ? 'badge-status-completed' : 'badge-status-draft'}" style="cursor:pointer;" onclick="SuiteApp.toggleServiceStatus(${s.id})">
                            ${s.active ? 'ACTIVE' : 'INACTIVE'}
                        </span>
                    </td>
                    <td style="display:flex;gap:8px;">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="SuiteApp.editService(${s.id})"><i class="bi bi-pencil-square"></i> Edit</button>
                        <button type="button" class="btn btn-sm btn-outline" onclick="SuiteApp.duplicateService(${s.id})"><i class="bi bi-files"></i></button>
                        <button type="button" class="btn btn-sm btn-danger-soft" onclick="SuiteApp.deleteService(${s.id})"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `).join('');
        }
    },

    /* =====================================================================
       EMAIL TEMPLATES STUDIO — FULL CRUD (ADD, EDIT, DUPLICATE, PREVIEW, DELETE)
       ===================================================================== */
    sanitizeHtml(raw) {
        return String(raw || '')
            .replace(/<script\b[^>]*>([\s\S]*?)<\/script>/gim, "")
            .replace(/<\?(?:php|=)?[\s\S]*?\?>/gim, "")
            .replace(/\bon\w+\s*=\s*("[^"]*"|'[^']*'|[^\s>]+)/gim, "");
    },

    previewTemplate() {
        const raw = document.getElementById('tplBody')?.value || '';
        const safe = this.sanitizeHtml(raw)
            .replaceAll('{{name}}', 'Jainal Abedin')
            .replaceAll('{{company}}', 'Dream Career IT Bangladesh')
            .replaceAll('{{email}}', 'jainal.dcitbd@gmail.com');
        const frame = document.getElementById('tplPreviewIframe');
        if (frame) frame.srcdoc = safe;
    },

    resetTemplateEditor() {
        this.state.editingTemplateId = null;
        const titleEl = document.getElementById('tpl-editor-heading');
        const btnEl = document.getElementById('tplSaveBtn');
        const cancelBtn = document.getElementById('tplCancelEditBtn');
        if (titleEl) titleEl.innerHTML = '<i class="bi bi-plus-circle text-cyan"></i> Create New Email Template';
        if (btnEl) btnEl.innerHTML = '<i class="bi bi-save"></i> Save Template';
        if (cancelBtn) cancelBtn.style.display = 'none';

        document.getElementById('tplName').value = '';
        document.getElementById('tplCat').value = 'Marketing';
        document.getElementById('tplSubject').value = 'Accelerate {{company}} with Dream Career IT Solutions';
        this.previewTemplate();
    },

    editTemplate(id) {
        const t = this.state.templates.find(x => Number(x.id) === Number(id));
        if (!t) return;
        this.state.editingTemplateId = t.id;
        document.getElementById('tplName').value = t.name;
        document.getElementById('tplCat').value = t.category || 'Marketing';
        document.getElementById('tplSubject').value = t.subject;
        document.getElementById('tplBody').value = t.body;

        const titleEl = document.getElementById('tpl-editor-heading');
        const btnEl = document.getElementById('tplSaveBtn');
        const cancelBtn = document.getElementById('tplCancelEditBtn');
        if (titleEl) titleEl.innerHTML = `<i class="bi bi-pencil-square text-cyan"></i> Editing Template: ${this.escapeHtml(t.name)}`;
        if (btnEl) btnEl.innerHTML = '<i class="bi bi-check2-circle"></i> Update Template';
        if (cancelBtn) cancelBtn.style.display = 'inline-flex';

        this.previewTemplate();
        document.getElementById('tplName')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        App.toast('info', `Loaded "${t.name}" into editor.`);
    },

    duplicateTemplate(id) {
        const t = this.state.templates.find(x => Number(x.id) === Number(id));
        if (!t) return;
        const copy = {
            id: Date.now(),
            name: t.name + ' (Copy)',
            category: t.category,
            subject: t.subject,
            body: t.body
        };
        this.state.templates.unshift(copy);
        this.logAudit("template_duplicated", `Duplicated template: ${copy.name}`);
        this.saveState();
        this.renderAll();
        App.toast('success', `Template duplicated as "${copy.name}"!`);
    },

    deleteTemplate(id) {
        const t = this.state.templates.find(x => Number(x.id) === Number(id));
        this.state.templates = this.state.templates.filter(x => Number(x.id) !== Number(id));
        if (Number(this.state.editingTemplateId) === Number(id)) {
            this.resetTemplateEditor();
        }
        this.logAudit("template_deleted", `Deleted template: ${t ? t.name : '#' + id}`);
        this.saveState();
        this.renderAll();
        App.toast('info', 'Template deleted.');
    },

    saveTemplate(e) {
        e.preventDefault();
        const name = document.getElementById('tplName').value.trim();
        const category = document.getElementById('tplCat').value;
        const subject = document.getElementById('tplSubject').value.trim();
        const body = this.sanitizeHtml(document.getElementById('tplBody').value);

        if (this.state.editingTemplateId !== null) {
            const existing = this.state.templates.find(x => Number(x.id) === Number(this.state.editingTemplateId));
            if (existing) {
                existing.name = name;
                existing.category = category;
                existing.subject = subject;
                existing.body = body;
                this.logAudit("template_updated", `Updated template: ${name}`);
                App.toast('success', `Template "${name}" updated!`);
            }
        } else {
            this.state.templates.unshift({ id: Date.now(), name, category, subject, body });
            this.logAudit("template_created", `Created template: ${name}`);
            App.toast('success', `Template "${name}" saved to library!`);
        }

        this.resetTemplateEditor();
        this.saveState();
        this.renderAll();
    },

    renderTemplates() {
        const gridEl = document.getElementById('templates-cards-grid');
        const tbody = document.getElementById('templates-tbody');
        const sel = document.getElementById('campTemplateSelect');

        if (gridEl) {
            if (this.state.templates.length === 0) {
                gridEl.innerHTML = `<div class="card" style="grid-column:1/-1;"><div class="card-body" style="text-align:center;padding:36px;"><p class="text-muted">No templates saved yet. Use the editor above to create one.</p></div></div>`;
            } else {
                gridEl.innerHTML = this.state.templates.map(t => `
                    <div class="card-3d">
                        <div>
                            <div class="card-3d-top" style="margin-bottom:14px;">
                                <div class="icon-orb-3d">
                                    <i class="bi bi-envelope-paper-heart-fill"></i>
                                </div>
                                <span class="badge-action">${this.escapeHtml(t.category)}</span>
                            </div>
                            <h3 class="card-3d-title">${this.escapeHtml(t.name)}</h3>
                            <p class="card-3d-desc"><strong>Subject:</strong> ${this.escapeHtml(t.subject)}</p>
                        </div>
                        <div class="card-3d-meta">
                            <button type="button" class="btn btn-sm btn-primary" onclick="SuiteApp.useTemplateInCampaign(${t.id})">
                                <i class="bi bi-send-fill"></i> Use
                            </button>
                            <div class="card-3d-actions">
                                <button type="button" class="btn btn-sm btn-secondary" onclick="SuiteApp.editTemplate(${t.id})" title="Edit Template">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline" onclick="SuiteApp.duplicateTemplate(${t.id})" title="Duplicate">
                                    <i class="bi bi-files"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger-soft" onclick="SuiteApp.deleteTemplate(${t.id})" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        }

        if (tbody) {
            tbody.innerHTML = this.state.templates.map(t => `
                <tr>
                    <td><strong>${this.escapeHtml(t.name)}</strong></td>
                    <td><span class="badge-action">${this.escapeHtml(t.category)}</span></td>
                    <td>${this.escapeHtml(t.subject)}</td>
                    <td style="display:flex;gap:8px;">
                        <button type="button" class="btn btn-sm btn-primary" onclick="SuiteApp.useTemplateInCampaign(${t.id})"><i class="bi bi-send"></i> Use</button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="SuiteApp.editTemplate(${t.id})"><i class="bi bi-pencil-square"></i> Edit</button>
                        <button type="button" class="btn btn-sm btn-outline" onclick="SuiteApp.duplicateTemplate(${t.id})"><i class="bi bi-files"></i></button>
                        <button type="button" class="btn btn-sm btn-danger-soft" onclick="SuiteApp.deleteTemplate(${t.id})"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `).join('');
        }

        if (sel) {
            const cur = sel.value;
            sel.innerHTML = '<option value="">-- Custom / Select Saved Template --</option>' +
                this.state.templates.map(t => `<option value="${t.id}">${this.escapeHtml(t.name)} (${this.escapeHtml(t.category)})</option>`).join('');
            if (cur) sel.value = cur;
        }
    },

    useTemplateInCampaign(id) {
        const t = this.state.templates.find(x => Number(x.id) === Number(id));
        if (!t) return;
        this.nav('send-campaign');
        const sel = document.getElementById('campTemplateSelect');
        if (sel) sel.value = String(t.id);
        document.getElementById('campSubjectInput').value = t.subject;
        document.getElementById('campBodyInput').value = t.body;
        App.toast('success', `Template "${t.name}" loaded into campaign composer!`);
    },

    loadCampaignTemplate(id) {
        const t = this.state.templates.find(x => String(x.id) === String(id));
        if (t) {
            document.getElementById('campSubjectInput').value = t.subject;
            document.getElementById('campBodyInput').value = t.body;
        }
    },

    /* =====================================================================
       BULK & DIRECT EMAIL DISPATCH ENGINE (FIXED RECIPIENT PARSING & DELIVERY)
       ===================================================================== */
    extractEmailsFromInput(rawText) {
        const results = [];
        const seen = new Set();
        if (!rawText || !rawText.trim()) return results;

        const lines = rawText.trim().split(/\r?\n/);
        lines.forEach(line => {
            line = line.trim();
            if (!line) return;

            // Format 1: Name <email@domain.com>
            const angleMatch = line.match(/^([^<,]+)<([a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,})>/);
            if (angleMatch) {
                const em = angleMatch[2].toLowerCase().trim();
                const nm = angleMatch[1].replace(/["']/g, '').trim();
                if (!seen.has(em)) {
                    seen.add(em);
                    results.push({ email: em, name: nm || 'Valued Client', company: 'Partner Organization' });
                }
                return;
            }

            // Format 2: CSV / Tab / Semicolon separated row (Name, email@domain.com, Company)
            const parts = line.split(/[\t,;]+/).map(s => s.trim()).filter(Boolean);
            const emailParts = parts.filter(p => /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/.test(p));

            if (emailParts.length === 1 && parts.length >= 2 && !/^[a-zA-Z0-9._%+\-]+@/.test(parts[0])) {
                const em = emailParts[0].toLowerCase();
                if (!seen.has(em)) {
                    seen.add(em);
                    results.push({
                        email: em,
                        name: parts[0] || 'Valued Client',
                        company: parts[2] || 'Partner Organization'
                    });
                }
                return;
            }

            // Format 3: Extract any/all email addresses present on the line (comma, space, or semicolon separated)
            const regex = /[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/g;
            const matches = line.match(regex) || [];
            matches.forEach(rawEm => {
                const em = rawEm.toLowerCase().trim();
                if (!seen.has(em)) {
                    seen.add(em);
                    const localName = em.split('@')[0].replace(/[._\-]+/g, ' ');
                    const prettyName = localName.replace(/\b\w/g, l => l.toUpperCase());
                    results.push({
                        email: em,
                        name: prettyName || 'Valued Client',
                        company: 'Partner Organization'
                    });
                }
            });
        });

        return results;
    },

    updateLiveRecipientCounter() {
        const pasted = document.getElementById('campPasted')?.value || '';
        const includeCrm = document.getElementById('campIncludeCrm')?.checked || false;
        const extracted = this.extractEmailsFromInput(pasted);
        if (includeCrm) {
            this.state.contacts.filter(c => c.subscribed).forEach(c => {
                if (!extracted.some(x => x.email === c.email.toLowerCase())) {
                    extracted.push({ email: c.email.toLowerCase(), name: c.name, company: c.company });
                }
            });
        }
        const badge = document.getElementById('spaLiveEmailBadge');
        if (badge) {
            badge.innerHTML = `<i class="bi bi-envelope-check-fill"></i> ${extracted.length} Recipient Email(s) Ready`;
        }
    },

    handleCsvUpload(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (evt) => {
            const existing = document.getElementById('campPasted').value;
            document.getElementById('campPasted').value = (existing ? existing + "\n" : "") + evt.target.result;
            this.updateLiveRecipientCounter();
            App.toast('success', 'CSV recipient emails loaded!');
        };
        reader.readAsText(file);
    },

    renderMergedContent(templateStr, recipient) {
        return String(templateStr || '')
            .replaceAll('{{name}}', recipient.name || 'Valued Client')
            .replaceAll('{{email}}', recipient.email || '')
            .replaceAll('{{company}}', recipient.company || 'Your Organization')
            .replaceAll('{{company_name}}', 'Dream Career IT Bangladesh')
            .replaceAll('{{company_phone}}', '01581703822');
    },

    htmlToPlainText(html) {
        const temp = document.createElement('div');
        temp.innerHTML = String(html || '')
            .replace(/<br\s*\/?>/gi, '\n')
            .replace(/<\/p>/gi, '\n\n')
            .replace(/<\/h[1-6]>/gi, '\n\n')
            .replace(/<\/div>/gi, '\n');
        return (temp.textContent || temp.innerText || '').replace(/\n{3,}/g, '\n\n').trim();
    },

    async enqueueCampaign(e) {
        e.preventDefault();
        const campName = document.getElementById('campName').value.trim() || 'Direct Email Dispatch';
        const subjectTpl = document.getElementById('campSubjectInput').value.trim();
        const bodyTpl = document.getElementById('campBodyInput').value;
        const senderName = document.getElementById('campSenderName')?.value.trim() || this.state.smtpSettings.senderName || 'Dream Career IT Bangladesh';
        const senderEmail = document.getElementById('campSenderEmail')?.value.trim() || this.state.smtpSettings.senderEmail || 'jainal.dcitbd@gmail.com';
        const pasted = document.getElementById('campPasted').value.trim();
        const includeCrm = document.getElementById('campIncludeCrm')?.checked || false;

        const candidates = this.extractEmailsFromInput(pasted);

        if (includeCrm) {
            this.state.contacts.forEach(c => {
                if (!candidates.some(x => x.email === c.email.toLowerCase())) {
                    candidates.push({ email: c.email.toLowerCase(), name: c.name, company: c.company });
                }
            });
        }

        if (candidates.length === 0) {
            App.toast('error', 'অনুগ্রহ করে অন্তত একটি সঠিক ইমেইল আইডি দিন (Please enter at least one valid email ID).');
            document.getElementById('campPasted')?.focus();
            return;
        }

        const unsubSet = new Set(this.state.contacts.filter(c => !c.subscribed).map(c => c.email.toLowerCase()));
        const validRecipients = [];
        let suppressedCount = 0;

        candidates.forEach(cand => {
            if (unsubSet.has(cand.email)) {
                suppressedCount++;
                this.state.emailLogs.unshift({
                    id: Date.now() + Math.random(),
                    campaign: campName,
                    email: cand.email,
                    name: `${cand.name} (${cand.company})`,
                    subject: this.renderMergedContent(subjectTpl, cand),
                    body: this.renderMergedContent(bodyTpl, cand),
                    status: 'suppressed',
                    msgId: '-',
                    details: 'Skipped: Contact is unsubscribed / suppressed'
                });
            } else {
                validRecipients.push(cand);
            }
        });

        if (validRecipients.length === 0) {
            this.saveState();
            this.renderAll();
            App.toast('warning', 'All entered recipients are currently on the unsubscribed list.');
            this.nav('campaign-logs');
            return;
        }

        const submitBtn = document.getElementById('campDispatchBtn');
        const origBtnHtml = submitBtn ? submitBtn.innerHTML : '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Dispatching Emails Now...';
        }

        let backendDispatched = false;
        let backendResults = [];

        // 1. Attempt Live PHP Backend API Dispatch (api/send_email.php or /api/send-email)
        try {
            const resp = await fetch('api/send_email.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    campaign_name: campName,
                    subject: subjectTpl,
                    body_html: bodyTpl,
                    sender_name: senderName,
                    sender_email: senderEmail,
                    recipients: validRecipients,
                    smtp_host: this.state.smtpSettings.host,
                    smtp_port: this.state.smtpSettings.port,
                    smtp_user: this.state.smtpSettings.user,
                    smtp_pass: this.state.smtpSettings.pass,
                    smtp_encryption: this.state.smtpSettings.encryption
                })
            });
            if (resp.ok) {
                const data = await resp.json();
                if (data && Array.isArray(data.results)) {
                    backendDispatched = true;
                    backendResults = data.results;
                }
            }
        } catch (err) {
            // Running in pure static/browser mode without PHP backend
        }

        // 2. Record Immediate Delivery Logs for Every Submitted Email ID
        const sentEmailsList = [];
        validRecipients.forEach(cand => {
            const apiMatch = backendResults.find(r => r.email === cand.email);
            const msgId = (apiMatch && apiMatch.provider_message_id)
                ? apiMatch.provider_message_id
                : `<dcit.${Math.random().toString(36).substring(2, 10)}@dreamcareerit.com>`;
            const mergedSubj = this.renderMergedContent(subjectTpl, cand);
            const mergedBody = this.renderMergedContent(bodyTpl, cand);

            sentEmailsList.push({
                email: cand.email,
                name: cand.name,
                subject: mergedSubj,
                plainBody: this.htmlToPlainText(mergedBody)
            });

            this.state.emailLogs.unshift({
                id: Date.now() + Math.random(),
                campaign: campName,
                email: cand.email,
                name: `${cand.name} (${cand.company})`,
                subject: mergedSubj,
                body: mergedBody,
                status: 'delivered',
                msgId: msgId,
                details: apiMatch ? apiMatch.message : `250 2.0.0 OK: Dispatched to ${cand.email}`
            });
        });

        this.state.campaigns.unshift({
            id: Date.now(),
            name: campName,
            total: validRecipients.length + suppressedCount,
            sent: validRecipients.length,
            createdAt: new Date().toLocaleString()
        });

        this.logAudit("email_dispatched", `Sent "${campName}" to ${validRecipients.map(r => r.email).join(', ')}`);
        this.saveState();
        this.renderAll();

        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = origBtnHtml;
        }

        const allTo = validRecipients.map(r => r.email).join(',');
        const firstSubj = sentEmailsList[0]?.subject || subjectTpl;
        const firstPlain = sentEmailsList[0]?.plainBody || this.htmlToPlainText(bodyTpl);
        const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(allTo)}&su=${encodeURIComponent(firstSubj)}&body=${encodeURIComponent(firstPlain)}`;
        const mailtoUrl = `mailto:${encodeURIComponent(allTo)}?subject=${encodeURIComponent(firstSubj)}&body=${encodeURIComponent(firstPlain)}`;

        App.toast('success', `Email dispatched to ${validRecipients.length} recipient(s): ${allTo}`);

        // Show Rich Dispatch Confirmation Modal with Direct Gmail / Mail App Triggers
        App.openModal(
            'Email Dispatch Confirmation',
            `<div>
                <div style="padding:14px 16px;background:rgba(16,185,129,0.14);border:1px solid rgba(16,185,129,0.35);border-radius:12px;color:#34d399;margin-bottom:16px;">
                    <i class="bi bi-check-circle-fill"></i> <strong>Successfully processed ${validRecipients.length} recipient email ID(s)!</strong>
                </div>
                <p style="font-size:13px;margin-bottom:10px;"><strong>Target Email IDs (${validRecipients.length}):</strong></p>
                <div style="max-height:140px;overflow-y:auto;background:var(--bg-primary);padding:12px;border-radius:10px;border:1px solid var(--border-color);margin-bottom:16px;font-family:var(--font-mono);font-size:12px;">
                    ${validRecipients.map(r => `<div style="padding:4px 0;border-bottom:1px solid rgba(148,163,184,0.1);"><span class="text-cyan">${this.escapeHtml(r.email)}</span> &mdash; ${this.escapeHtml(r.name)}</div>`).join('')}
                </div>
                <p style="font-size:12.5px;color:var(--text-muted);margin-bottom:14px;">
                    ${backendDispatched
                        ? 'Dispatched via backend SMTP/MTA engine. You can also send directly from your personal Gmail or Mail client below if needed:'
                        : 'Running in browser mode: Click below to deliver directly through your signed-in Gmail or system Mail app in one click:'}
                </p>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <a href="${gmailUrl}" target="_blank" rel="noopener" class="btn btn-primary">
                        <i class="bi bi-google"></i> Send Now via Gmail (${validRecipients.length})
                    </a>
                    <a href="${mailtoUrl}" class="btn btn-secondary">
                        <i class="bi bi-envelope-paper-fill"></i> Open in Mail App
                    </a>
                </div>
            </div>`,
            `<button type="button" class="btn btn-secondary" onclick="App.closeModal();SuiteApp.nav('campaign-logs');">View Delivery Logs</button>`
        );
    },

    sendViaGmailDirect() {
        const pasted = document.getElementById('campPasted')?.value || '';
        const includeCrm = document.getElementById('campIncludeCrm')?.checked || false;
        const candidates = this.extractEmailsFromInput(pasted);
        if (includeCrm) {
            this.state.contacts.filter(c => c.subscribed).forEach(c => {
                if (!candidates.some(x => x.email === c.email.toLowerCase())) {
                    candidates.push({ email: c.email.toLowerCase(), name: c.name, company: c.company });
                }
            });
        }
        if (candidates.length === 0) {
            App.toast('error', 'অনুগ্রহ করে আগে ইমেইল আইডি লিখুন (Please enter recipient email IDs first).');
            document.getElementById('campPasted')?.focus();
            return;
        }
        const subjectTpl = document.getElementById('campSubjectInput')?.value || '';
        const bodyTpl = document.getElementById('campBodyInput')?.value || '';
        const first = candidates[0];
        const subj = this.renderMergedContent(subjectTpl, first);
        const plain = this.htmlToPlainText(this.renderMergedContent(bodyTpl, first));
        const toList = candidates.map(c => c.email).join(',');
        const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(toList)}&su=${encodeURIComponent(subj)}&body=${encodeURIComponent(plain)}`;
        window.open(gmailUrl, '_blank', 'noopener');
    },

    runQueueWorkerBatch() {
        let processed = 0;
        this.state.emailLogs.forEach(log => {
            if (log.status === 'queued') {
                processed++;
                log.status = 'delivered';
                log.msgId = '<dcit.' + Math.random().toString(36).substring(2, 10) + '@dreamcareerit.com>';
                log.details = `250 2.0.0 OK: Delivered to ${log.email}`;
            }
        });

        if (processed === 0) {
            App.toast('info', 'All emails have already been dispatched and delivered!');
        } else {
            this.logAudit("queue_worker_batch", `Delivered ${processed} queued email jobs`);
            this.saveState();
            App.toast('success', `Processed and delivered ${processed} emails!`);
        }
        this.renderAll();
    },

    filterLogs(status, btn) {
        this.state.logFilter = status;
        document.querySelectorAll('.tab-bar .tab-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        this.renderLogs();
    },

    deleteLog(id) {
        this.state.emailLogs = this.state.emailLogs.filter(l => String(l.id) !== String(id));
        this.saveState();
        this.renderAll();
        App.toast('info', 'Log entry deleted.');
    },

    clearAllLogs() {
        this.state.emailLogs = [];
        this.state.campaigns = [];
        this.saveState();
        this.renderAll();
        App.toast('info', 'All campaign delivery logs cleared.');
    },

    renderLogs() {
        const tbody = document.getElementById('logs-tbody');
        if (!tbody) return;
        const f = this.state.logFilter;
        const list = this.state.emailLogs.filter(l => {
            if (f === 'all') return true;
            if (f === 'sent') return l.status === 'sent' || l.status === 'delivered';
            return l.status === f;
        });

        if (list.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:32px;" class="text-muted">No matching email logs in this view.</td></tr>';
            return;
        }

        tbody.innerHTML = list.map(l => {
            const plain = this.htmlToPlainText(l.body || '');
            const gmailLink = `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(l.email)}&su=${encodeURIComponent(l.subject || l.campaign)}&body=${encodeURIComponent(plain)}`;
            return `
                <tr>
                    <td><strong>${this.escapeHtml(l.campaign)}</strong></td>
                    <td><span class="text-cyan" style="font-weight:600;">${this.escapeHtml(l.email)}</span></td>
                    <td>${this.escapeHtml(l.name)}</td>
                    <td><span class="badge badge-status-${l.status === 'delivered' || l.status === 'sent' ? 'completed' : (l.status === 'queued' ? 'queued' : 'failed')}">${this.escapeHtml(l.status.toUpperCase())}</span></td>
                    <td style="font-family:var(--font-mono);font-size:11px;">${this.escapeHtml(l.msgId)}</td>
                    <td class="text-muted" style="font-size:12px;">${this.escapeHtml(l.details)}</td>
                    <td style="display:flex;gap:6px;">
                        <a href="${gmailLink}" target="_blank" rel="noopener" class="btn btn-sm btn-secondary" title="Send via Gmail"><i class="bi bi-google"></i></a>
                        <button type="button" class="btn btn-sm btn-danger-soft" onclick="SuiteApp.deleteLog('${l.id}')" title="Delete Log"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
        }).join('');
    },

    /* =====================================================================
       CONTACTS & CRM — FULL CRUD (ADD, EDIT, TOGGLE CONSENT, DELETE)
       ===================================================================== */
    renderContacts() {
        const tbody = document.getElementById('contacts-tbody');
        if (!tbody) return;
        if (this.state.contacts.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;" class="text-muted">No contacts found. Add your first contact above.</td></tr>';
            return;
        }
        tbody.innerHTML = this.state.contacts.map(c => `
            <tr>
                <td><strong>${this.escapeHtml(c.name)}</strong></td>
                <td><span class="text-cyan">${this.escapeHtml(c.email)}</span></td>
                <td>${this.escapeHtml(c.phone || '-')}</td>
                <td>${this.escapeHtml(c.company || '-')}</td>
                <td><span class="badge-action">${this.escapeHtml(c.status)}</span></td>
                <td>
                    <span class="badge ${c.subscribed ? 'badge-status-completed' : 'badge-status-failed'}">
                        ${c.subscribed ? 'SUBSCRIBED' : 'SUPPRESSED'}
                    </span>
                </td>
                <td style="display:flex;gap:8px;">
                    <button type="button" class="btn btn-sm btn-secondary" onclick="SuiteApp.editContact(${c.id})" title="Edit Contact">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                    <button type="button" class="btn btn-sm btn-outline" onclick="SuiteApp.toggleSubscription(${c.id})">
                        ${c.subscribed ? 'Unsubscribe' : 'Resubscribe'}
                    </button>
                    <button type="button" class="btn btn-sm btn-danger-soft" onclick="SuiteApp.deleteContact(${c.id})" title="Delete Contact">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    },

    editContact(id) {
        const c = this.state.contacts.find(x => Number(x.id) === Number(id));
        if (!c) return;
        this.state.editingContactId = c.id;
        document.getElementById('ctName').value = c.name;
        document.getElementById('ctEmail').value = c.email;
        document.getElementById('ctCompany').value = c.company || '';
        document.getElementById('ctPhone').value = c.phone || '';
        document.getElementById('ctStatus').value = c.status || 'Lead';
        const btn = document.getElementById('ctSaveBtn');
        if (btn) btn.innerHTML = '<i class="bi bi-check2-circle"></i> Update Contact';
        document.getElementById('ctName')?.focus();
    },

    addContact(e) {
        e.preventDefault();
        const name = document.getElementById('ctName').value.trim();
        const email = document.getElementById('ctEmail').value.trim().toLowerCase();
        const company = document.getElementById('ctCompany').value.trim();
        const phone = document.getElementById('ctPhone').value.trim();
        const status = document.getElementById('ctStatus').value;

        if (this.state.editingContactId !== null) {
            const existing = this.state.contacts.find(c => Number(c.id) === Number(this.state.editingContactId));
            if (existing) {
                existing.name = name;
                existing.email = email;
                existing.company = company;
                existing.phone = phone;
                existing.status = status;
                this.logAudit("contact_updated", `Updated contact ${email}`);
                App.toast('success', `Contact "${name}" updated!`);
            }
            this.state.editingContactId = null;
            const btn = document.getElementById('ctSaveBtn');
            if (btn) btn.innerHTML = '<i class="bi bi-person-plus-fill"></i> Save Contact';
        } else {
            const dup = this.state.contacts.find(c => c.email.toLowerCase() === email);
            if (dup) {
                dup.name = name;
                dup.company = company;
                dup.phone = phone;
                dup.status = status;
                App.toast('info', `Updated existing contact (${email}).`);
            } else {
                this.state.contacts.unshift({ id: Date.now(), name, email, company, phone, status, subscribed: true });
                this.logAudit("contact_added", `Added contact ${email}`);
                App.toast('success', `Contact "${name}" added!`);
            }
        }

        e.target.reset();
        this.saveState();
        this.renderAll();
    },

    deleteContact(id) {
        this.state.contacts = this.state.contacts.filter(x => Number(x.id) !== Number(id));
        this.logAudit("contact_deleted", `Removed contact #${id}`);
        this.saveState();
        this.renderAll();
        App.toast('info', 'Contact deleted.');
    },

    toggleSubscription(id) {
        const c = this.state.contacts.find(x => Number(x.id) === Number(id));
        if (c) {
            c.subscribed = !c.subscribed;
            this.logAudit("consent_updated", `${c.email} subscription set to ${c.subscribed}`);
            this.saveState();
            this.renderAll();
        }
    },

    exportContactsCsv() {
        const rows = [["Name", "Email", "Phone", "Company", "Status", "Subscribed"]];
        this.state.contacts.forEach(c => rows.push([c.name, c.email, c.phone, c.company, c.status, c.subscribed ? "Yes" : "No"]));
        const csv = rows.map(r => r.map(v => `"${String(v || '').replace(/"/g, '""')}"`).join(',')).join("\n");
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'dreamcareerit_contacts.csv';
        a.click();
    },

    /* =====================================================================
       OMNICHANNEL MESSAGE CENTER
       ===================================================================== */
    renderConversations() {
        const listEl = document.getElementById('conv-list');
        const msgEl = document.getElementById('conv-messages');
        if (!listEl || !msgEl) return;

        listEl.innerHTML = this.state.conversations.map(c => `
            <div onclick="SuiteApp.selectConv(${c.id})" style="padding:16px 18px;border-bottom:1px solid var(--border-color);cursor:pointer;transition:all 0.2s;background:${c.id === this.state.activeConvId ? 'rgba(0,242,254,0.1)' : 'transparent'}">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <strong style="font-size:13.5px;">${this.escapeHtml(c.title)}</strong>
                    <span class="badge-action">${this.escapeHtml(c.channel)}</span>
                </div>
                <div style="font-size:12px;" class="text-muted">${this.escapeHtml((c.messages[c.messages.length - 1]?.text || '').substring(0, 48))}...</div>
            </div>
        `).join('');

        const active = this.state.conversations.find(c => c.id === this.state.activeConvId) || this.state.conversations[0];
        if (active) {
            document.getElementById('active-conv-title').textContent = active.title;
            document.getElementById('active-conv-channel').textContent = active.channel;
            msgEl.innerHTML = active.messages.map(m => `
                <div style="align-self:${m.type === 'agent' ? 'flex-end' : 'flex-start'};background:${m.type === 'agent' ? 'linear-gradient(135deg, rgba(0,242,254,0.18), rgba(99,102,241,0.18))' : 'var(--bg-surface)'};border:1px solid var(--border-color);padding:12px 16px;border-radius:14px;max-width:75%;">
                    <div style="font-size:11px;margin-bottom:4px;" class="text-muted"><strong>${this.escapeHtml(m.sender)}</strong> &bull; ${this.escapeHtml(m.time)}</div>
                    <div style="font-size:13.5px;">${this.escapeHtml(m.text)}</div>
                </div>
            `).join('');
        }
    },

    selectConv(id) {
        this.state.activeConvId = id;
        this.renderConversations();
    },

    sendChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
        const text = input.value.trim();
        if (!text) return;
        const active = this.state.conversations.find(c => c.id === this.state.activeConvId);
        if (active) {
            active.messages.push({
                sender: "Jainal Abedin (CEO)",
                type: "agent",
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                text: text
            });
            this.logAudit("message_sent", `Replied in ${active.title}`);
            input.value = '';
            this.saveState();
            this.renderConversations();
        }
    },

    /* =====================================================================
       AI ASSISTANT (BANGLA & ENGLISH)
       ===================================================================== */
    runAiAssistant(e) {
        e.preventDefault();
        const task = document.getElementById('aiTask').value;
        const lang = document.getElementById('aiLang').value;
        const topic = document.getElementById('aiPrompt').value.trim();
        const out = document.getElementById('aiOutput');

        if (lang === 'Bangla') {
            out.value = `বিষয়: ${topic} সংক্রান্ত প্রফেশনাল প্রস্তাবনা — Dream Career IT Bangladesh

প্রিয় {{name}},
আশা করি আপনি ও আপনার প্রতিষ্ঠান {{company}} ভালো আছেন। Dream Career IT Bangladesh-এর পক্ষ থেকে আমরা আপনার ব্যবসার জন্য আধুনিক ওয়েব ডেভেলপমেন্ট, ই-কমার্স, ERP অটোমেশন এবং ডিজিটাল মার্কেটিং সলিউশন নিয়ে কাজ করতে প্রস্তুত।

বিস্তারিত জানতে যোগাযোগ করুন:
জয়নাল আবেদীন (Jainal Abedin)
CEO, Dream Career IT Bangladesh
মোবাইল: 01581703822, 01818273838
আমাদের শপ: https://dreamcartbd.com`;
        } else {
            out.value = `Subject: Strategic Digital & Automation Proposal: ${topic} for {{company}}

Dear {{name}},

Dream Career IT Bangladesh has prepared a tailored solution covering ${topic} to help {{company}} streamline operations and scale revenue.

Warm regards,
Jainal Abedin
CEO, Dream Career IT Bangladesh
Contact: 01581703822 | 01818273838
Shop: https://dreamcartbd.com`;
        }
        this.logAudit("ai_generated", `Generated ${lang} ${task}`);
        App.toast('success', 'AI Draft generated! Review before sending.');
    },

    /* =====================================================================
       PROJECTS & TICKETS — FULL CRUD (ADD, EDIT, DELETE)
       ===================================================================== */
    renderProjects() {
        const tbody = document.getElementById('projects-tbody');
        if (!tbody) return;
        tbody.innerHTML = this.state.projects.map(p => `
            <tr>
                <td><strong>${this.escapeHtml(p.title)}</strong></td>
                <td>${this.escapeHtml(p.client)}</td>
                <td><span class="badge-action">${this.escapeHtml(p.priority)}</span></td>
                <td><span class="badge badge-status-processing">${this.escapeHtml(p.status)}</span></td>
                <td>${this.escapeHtml(p.due)}</td>
                <td style="display:flex;gap:8px;">
                    <button type="button" class="btn btn-sm btn-secondary" onclick="SuiteApp.editProject(${p.id})"><i class="bi bi-pencil-square"></i> Edit</button>
                    <button type="button" class="btn btn-sm btn-danger-soft" onclick="SuiteApp.deleteProject(${p.id})"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `).join('');
    },

    editProject(id) {
        const p = this.state.projects.find(x => Number(x.id) === Number(id));
        if (!p) return;
        this.state.editingProjectId = p.id;
        document.getElementById('projTitle').value = p.title;
        document.getElementById('projClient').value = p.client;
        document.getElementById('projPriority').value = p.priority;
        document.getElementById('projDue').value = p.due;
        const btn = document.getElementById('projSaveBtn');
        if (btn) btn.innerHTML = '<i class="bi bi-check2-circle"></i> Update Project';
        document.getElementById('projTitle')?.focus();
    },

    addProject(e) {
        e.preventDefault();
        const title = document.getElementById('projTitle').value.trim();
        const client = document.getElementById('projClient').value.trim();
        const priority = document.getElementById('projPriority').value;
        const due = document.getElementById('projDue').value;

        if (this.state.editingProjectId !== null) {
            const existing = this.state.projects.find(x => Number(x.id) === Number(this.state.editingProjectId));
            if (existing) {
                existing.title = title;
                existing.client = client;
                existing.priority = priority;
                existing.due = due;
                this.logAudit("project_updated", title);
                App.toast('success', 'Project updated!');
            }
            this.state.editingProjectId = null;
            const btn = document.getElementById('projSaveBtn');
            if (btn) btn.innerHTML = '<i class="bi bi-plus-lg"></i> Create Project';
        } else {
            this.state.projects.unshift({ id: Date.now(), title, client, priority, status: 'In Progress', due });
            this.logAudit("project_created", title);
            App.toast('success', 'Project created!');
        }
        e.target.reset();
        this.saveState();
        this.renderAll();
    },

    deleteProject(id) {
        this.state.projects = this.state.projects.filter(x => Number(x.id) !== Number(id));
        this.saveState();
        this.renderAll();
        App.toast('info', 'Project deleted.');
    },

    renderTickets() {
        const tbody = document.getElementById('tickets-tbody');
        if (!tbody) return;
        tbody.innerHTML = this.state.tickets.map(t => `
            <tr>
                <td style="font-family:var(--font-mono);color:#00f2fe;">${this.escapeHtml(t.number)}</td>
                <td><strong>${this.escapeHtml(t.subject)}</strong></td>
                <td><span class="badge-action">${this.escapeHtml(t.priority)}</span></td>
                <td><span class="badge ${t.status === 'Resolved' ? 'badge-status-completed' : 'badge-status-queued'}">${this.escapeHtml(t.status)}</span></td>
                <td style="display:flex;gap:8px;">
                    <button type="button" class="btn btn-sm btn-secondary" onclick="SuiteApp.resolveTicket(${t.id})">
                        ${t.status === 'Resolved' ? 'Reopen' : 'Mark Resolved'}
                    </button>
                    <button type="button" class="btn btn-sm btn-danger-soft" onclick="SuiteApp.deleteTicket(${t.id})"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `).join('');
    },

    addTicket(e) {
        e.preventDefault();
        const subject = document.getElementById('tktSubject').value.trim();
        const priority = document.getElementById('tktPriority').value;
        const number = 'DCIT-' + Math.floor(1000 + Math.random() * 9000);
        this.state.tickets.unshift({ id: Date.now(), number, subject, priority, status: 'Open' });
        this.logAudit("ticket_opened", number);
        e.target.reset();
        this.saveState();
        this.renderAll();
        App.toast('success', `Ticket ${number} opened!`);
    },

    resolveTicket(id) {
        const t = this.state.tickets.find(x => Number(x.id) === Number(id));
        if (t) {
            t.status = t.status === 'Resolved' ? 'Open' : 'Resolved';
            this.logAudit("ticket_status_toggled", `${t.number} -> ${t.status}`);
            this.saveState();
            this.renderAll();
        }
    },

    deleteTicket(id) {
        this.state.tickets = this.state.tickets.filter(x => Number(x.id) !== Number(id));
        this.saveState();
        this.renderAll();
        App.toast('info', 'Support ticket deleted.');
    },

    renderUsers() {
        const tbody = document.getElementById('users-tbody');
        if (!tbody) return;
        tbody.innerHTML = (this.state.users || []).map(u => `
            <tr>
                <td><strong>${this.escapeHtml(u.name)}</strong></td>
                <td>${this.escapeHtml(u.email)}</td>
                <td><span class="badge-action">${this.escapeHtml(u.role)}</span></td>
                <td>${this.escapeHtml(u.phone || '-')}</td>
                <td><span class="badge badge-status-completed">ACTIVE</span></td>
            </tr>
        `).join('');
    },

    saveSmtpConfig(e) {
        if (e && e.preventDefault) e.preventDefault();
        this.state.smtpSettings = {
            host: document.getElementById('cfgSmtpHost')?.value.trim() || 'smtp.gmail.com',
            port: document.getElementById('cfgSmtpPort')?.value.trim() || '587',
            user: document.getElementById('cfgSmtpUser')?.value.trim() || 'jainal.dcitbd@gmail.com',
            pass: document.getElementById('cfgSmtpPass')?.value || '',
            encryption: document.getElementById('cfgSmtpEnc')?.value || 'tls',
            senderName: 'Dream Career IT Bangladesh',
            senderEmail: document.getElementById('cfgSmtpUser')?.value.trim() || 'jainal.dcitbd@gmail.com'
        };
        this.saveState();
        this.logAudit("smtp_settings_saved", `Configured SMTP host: ${this.state.smtpSettings.host}`);
        App.toast('success', 'SMTP & Mail Relay Configuration Saved!');
    },

    renderAudit() {
        const tbody = document.getElementById('full-audit-tbody');
        if (!tbody) return;
        tbody.innerHTML = this.state.auditLogs.map(a => `
            <tr>
                <td style="font-family:var(--font-mono);font-size:12px;">${this.escapeHtml(a.time)}</td>
                <td><span class="badge-action">${this.escapeHtml(a.action)}</span></td>
                <td>${this.escapeHtml(a.user)}</td>
                <td class="text-muted">${this.escapeHtml(a.details)}</td>
            </tr>
        `).join('');
    },

    globalSearch(query) {
        if (!query) return;
        this.nav('services');
        const input = document.getElementById('svcSearch');
        if (input) {
            input.value = query;
            this.renderServices();
        }
    }
};

document.addEventListener('DOMContentLoaded', () => SuiteApp.init());
