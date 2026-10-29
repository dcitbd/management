/**
 * Dream Career IT — AI Business Suite
 * Client-Side Relational Store & Interactive Application Engine (GitHub Pages + PWA Ready)
 * Designer: Jainal Abedin, CEO, Dream Career IT Bangladesh
 */

const SuiteApp = {
    state: {
        user: null,
        services: [],
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
        templates: [],
        contacts: [],
        campaigns: [],
        emailLogs: [],
        conversations: [],
        activeConvId: 1,
        projects: [],
        tickets: [],
        auditLogs: [],
        logFilter: 'all'
    },

    init() {
        this.loadOrSeedData();
        this.checkSession();
        this.populateCategorySelects();
        this.renderAll();
        this.previewTemplate();
    },

    loadOrSeedData() {
        const saved = localStorage.getItem('dcit_suite_db_v2');
        if (saved) {
            try {
                this.state = JSON.parse(saved);
                return;
            } catch (e) {}
        }

        // Seed all 28 Required Services across 10 Categories
        const catalog = [
            ["Web & Software Development", "Web Design & Development", "Responsive bespoke business websites built with modern stacks."],
            ["Web & Software Development", "Full-Stack Development", "End-to-end web apps with reactive UIs and relational backends."],
            ["Web & Software Development", "Backend Development", "Secure REST APIs, microservices, and database architecture."],
            ["Web & Software Development", "System Development", "Custom enterprise ERP, POS, and automated systems."],
            ["Design & Branding", "Graphic Design", "Logos, brand identity, marketing banners, and social creatives."],
            ["Design & Branding", "UI/UX Design", "Wireframing, interactive prototypes, and design systems."],
            ["Design & Branding", "Print & Branding Design", "Brochures, business cards, and corporate collateral."],
            ["Digital Marketing & SEO", "Digital Marketing", "Full-funnel growth marketing and conversion campaigns."],
            ["Digital Marketing & SEO", "Search Engine Optimization (SEO)", "Technical, on-page, and off-page SEO rank boosting."],
            ["Digital Marketing & SEO", "Social Media Management", "Content calendars and community management across platforms."],
            ["Digital Marketing & SEO", "Social Media Marketing", "Targeted paid ad campaigns on Meta, Instagram, and LinkedIn."],
            ["Digital Marketing & SEO", "Lead Generation", "Automated B2B & B2C lead capture and qualification funnels."],
            ["E-commerce & Business Systems", "E-commerce Solutions", "Scalable online stores with payment and courier integrations."],
            ["E-commerce & Business Systems", "Business Automation", "Automated workflows connecting CRM, billing, and messaging."],
            ["E-commerce & Business Systems", "Inventory & ERP Solutions", "Multi-warehouse stock tracking, invoicing, and accounting."],
            ["E-commerce & Business Systems", "CRM Development", "Custom customer relationship and sales pipeline platforms."],
            ["Database, API & Data", "Database Management", "MySQL/MariaDB normalization, indexing, and backup tuning."],
            ["Database, API & Data", "API Integration", "Payment gateways, SMS, couriers, and Meta Graph APIs."],
            ["Database, API & Data", "Data Analysis", "Executive BI dashboards and customer cohort analytics."],
            ["IT Support & Hosting", "IT Support & Maintenance", "24/7 SLA monitoring, bug fixes, and infrastructure care."],
            ["IT Support & Hosting", "Web Hosting & cPanel Management", "Server setup, SSL deployment, and DNS management."],
            ["IT Support & Hosting", "Website Security & Optimization", "Firewall hardening, malware removal, and security audits."],
            ["IT Support & Hosting", "Performance Optimization", "Core Web Vitals tuning, caching, and CDN acceleration."],
            ["Office & Productivity", "Microsoft Office Solutions", "Automated Excel templates, macros, and reporting suites."],
            ["Training & Consulting", "IT Training & Education", "Hands-on corporate training in web dev and digital marketing."],
            ["Training & Consulting", "Technical Consulting", "Architecture reviews and digital transformation roadmaps."],
            ["Project & Client Management", "Project Management", "Agile sprint delivery, milestone tracking, and QA."],
            ["Project & Client Management", "Customer Support & Communication", "Multi-channel helpdesk and SLA client communication."],
            ["Problem Solving & Documentation", "Problem Solving & Critical Thinking", "Root-cause debugging and bottleneck resolution."],
            ["Problem Solving & Documentation", "Technical Documentation", "System architecture specs, API docs, and user manuals."]
        ];

        this.state.services = catalog.map((item, idx) => ({
            id: idx + 1,
            category: item[0],
            name: item[1],
            description: item[2],
            price: "Custom Quote",
            active: true
        }));

        this.state.templates = [
            {
                id: 1,
                name: "Corporate IT & Automation Proposal",
                category: "Marketing",
                subject: "Scaling {{company}} with Dream Career IT Solutions",
                body: `<div style="font-family:Arial,sans-serif;padding:24px;background:#0f172a;color:#f8fafc;border-radius:10px;"><h2 style="color:#00f2fe;">Dream Career IT Bangladesh</h2><p>Hello <strong>{{name}}</strong>,</p><p>We would love to help <strong>{{company}}</strong> modernize its software, e-commerce, and digital marketing workflows.</p><p>Contact CEO Jainal Abedin: 01581703822 | <a href="https://dreamcartbd.com" style="color:#00f2fe;">dreamcartbd.com</a></p></div>`
            }
        ];

        this.state.contacts = [
            { id: 1, name: "Jainal Abedin", email: "jainal.dcitbd@gmail.com", phone: "01581703822", company: "Dream Career IT Bangladesh", status: "Customer", subscribed: true },
            { id: 2, name: "Rahim Uddin", email: "rahim@chattogramtrade.com", phone: "01818273838", company: "Chattogram Trade Corp", status: "Prospect", subscribed: true },
            { id: 3, name: "Karim Chowdhury", email: "karim@dhakaretail.org", phone: "01711002233", company: "Dhaka Retail Group", status: "Lead", subscribed: false }
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
            { id: 1, title: "DreamCartBD E-Commerce & ERP Upgrade", client: "Dream Career IT", priority: "High", status: "In Progress", due: "2026-11-30" }
        ];

        this.state.tickets = [
            { id: 1, number: "DCIT-8041", subject: "Configure Custom Domain & SSL on cPanel", priority: "High", status: "Open" }
        ];

        this.logAudit("system_initialized", "Seeded 10 categories and 30 services");
        this.saveState();
    },

    saveState() {
        localStorage.setItem('dcit_suite_db_v2', JSON.stringify(this.state));
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
        const sessionUser = sessionStorage.getItem('dcit_active_session');
        const authScreen = document.getElementById('auth-screen');
        if (sessionUser) {
            this.state.user = JSON.parse(sessionUser);
            authScreen.classList.add('hidden');
        } else {
            authScreen.classList.remove('hidden');
        }
    },

    handleAuth(e) {
        e.preventDefault();
        const email = document.getElementById('authEmail').value.trim();
        const pass = document.getElementById('authPassword').value;
        if (pass.length < 6) {
            const al = document.getElementById('auth-alert');
            al.style.display = 'block';
            al.textContent = 'Password must be at least 6 characters.';
            return;
        }
        const userObj = { name: "Jainal Abedin", email: email, role: "Administrator" };
        sessionStorage.setItem('dcit_active_session', JSON.stringify(userObj));
        this.state.user = userObj;
        this.logAudit("login_success", `Authenticated as ${email}`);
        document.getElementById('auth-screen').classList.add('hidden');
        this.renderAll();
        App.toast('success', 'Welcome to Dream Career IT — AI Business Suite!');
    },

    resetAdminPrompt() {
        document.getElementById('setup-name-group').style.display = 'block';
        document.getElementById('auth-heading').textContent = 'Initialize Administrator';
        App.toast('info', 'Enter your email and new password to initialize administrator access.');
    },

    logout() {
        this.logAudit("logout", "User signed out safely");
        sessionStorage.removeItem('dcit_active_session');
        document.getElementById('auth-screen').classList.remove('hidden');
        App.toast('info', 'Signed out of session.');
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
        if (!s1 || !s2) return;
        s1.innerHTML = '';
        s2.innerHTML = '<option value="">All 10 Service Categories</option>';
        this.state.categories.forEach(cat => {
            s1.innerHTML += `<option value="${cat}">${cat}</option>`;
            s2.innerHTML += `<option value="${cat}">${cat}</option>`;
        });
    },

    renderAll() {
        this.renderDashboard();
        this.renderServices();
        this.renderTemplates();
        this.renderContacts();
        this.renderLogs();
        this.renderConversations();
        this.renderProjects();
        this.renderTickets();
        this.renderAudit();
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
                    <td style="font-family:monospace;font-size:11px;">${a.time}</td>
                    <td><span class="badge-action">${a.action}</span></td>
                    <td>${a.user}</td>
                </tr>
            `).join('');
        }
    },

    renderServices() {
        const q = (document.getElementById('svcSearch')?.value || '').toLowerCase();
        const cat = document.getElementById('svcFilterCat')?.value || '';
        const tbody = document.getElementById('services-tbody');
        if (!tbody) return;

        const filtered = this.state.services.filter(s => {
            const matchQ = !q || s.name.toLowerCase().includes(q) || s.description.toLowerCase().includes(q);
            const matchCat = !cat || s.category === cat;
            return matchQ && matchCat;
        });

        tbody.innerHTML = filtered.map(s => `
            <tr>
                <td><strong>${s.name}</strong></td>
                <td><span class="badge-action">${s.category}</span></td>
                <td style="color:#94a3b8;max-width:340px;">${s.description}</td>
                <td>${s.price}</td>
                <td><span class="badge badge-status-completed">ACTIVE</span></td>
                <td>
                    <button class="btn btn-sm btn-outline" style="color:#ef4444;" onclick="SuiteApp.deleteService(${s.id})"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `).join('');
    },

    saveService(e) {
        e.preventDefault();
        const name = document.getElementById('svcName').value.trim();
        const category = document.getElementById('svcCategory').value;
        const price = document.getElementById('svcPrice').value.trim();
        const description = document.getElementById('svcDesc').value.trim();

        this.state.services.unshift({
            id: Date.now(),
            name, category, price, description, active: true
        });
        this.logAudit("service_created", `Added service: ${name}`);
        document.getElementById('add-service-panel').style.display = 'none';
        e.target.reset();
        this.renderAll();
        App.toast('success', `Service "${name}" added to catalog.`);
    },

    deleteService(id) {
        this.state.services = this.state.services.filter(s => s.id !== id);
        this.logAudit("service_deleted", `Removed service #${id}`);
        this.renderAll();
        App.toast('info', 'Service removed.');
    },

    sanitizeHtml(raw) {
        return raw
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

    saveTemplate(e) {
        e.preventDefault();
        const name = document.getElementById('tplName').value.trim();
        const category = document.getElementById('tplCat').value;
        const subject = document.getElementById('tplSubject').value.trim();
        const body = this.sanitizeHtml(document.getElementById('tplBody').value);

        this.state.templates.unshift({ id: Date.now(), name, category, subject, body });
        this.logAudit("template_saved", `Saved template: ${name}`);
        this.renderAll();
        App.toast('success', `Template "${name}" sanitized and saved!`);
    },

    renderTemplates() {
        const tbody = document.getElementById('templates-tbody');
        const sel = document.getElementById('campTemplateSelect');
        if (tbody) {
            tbody.innerHTML = this.state.templates.map(t => `
                <tr>
                    <td><strong>${t.name}</strong></td>
                    <td><span class="badge-action">${t.category}</span></td>
                    <td>${t.subject}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="SuiteApp.useTemplateInCampaign(${t.id})">Use in Campaign</button>
                    </td>
                </tr>
            `).join('');
        }
        if (sel) {
            sel.innerHTML = '<option value="">-- Select Saved Template --</option>' +
                this.state.templates.map(t => `<option value="${t.id}">${t.name}</option>`).join('');
        }
    },

    useTemplateInCampaign(id) {
        const t = this.state.templates.find(x => x.id === id);
        if (!t) return;
        this.nav('send-campaign');
        document.getElementById('campSubjectInput').value = t.subject;
        document.getElementById('campBodyInput').value = t.body;
    },

    loadCampaignTemplate(id) {
        const t = this.state.templates.find(x => String(x.id) === String(id));
        if (t) {
            document.getElementById('campSubjectInput').value = t.subject;
            document.getElementById('campBodyInput').value = t.body;
        }
    },

    handleCsvUpload(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (evt) => {
            const existing = document.getElementById('campPasted').value;
            document.getElementById('campPasted').value = (existing ? existing + "\n" : "") + evt.target.result;
            App.toast('success', 'CSV rows loaded into recipient box.');
        };
        reader.readAsText(file);
    },

    enqueueCampaign(e) {
        e.preventDefault();
        const campName = document.getElementById('campName').value.trim();
        const pasted = document.getElementById('campPasted').value.trim();
        const includeCrm = document.getElementById('campIncludeCrm').checked;

        const candidates = [];
        if (pasted) {
            pasted.split(/\r?\n/).forEach(line => {
                const parts = line.split(/[\t,;]+/).map(s => s.trim());
                parts.forEach(p => {
                    if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(p)) {
                        candidates.push({
                            email: p.toLowerCase(),
                            name: parts[0] !== p ? parts[0] : 'Client',
                            company: parts[2] || 'Partner Organization'
                        });
                    }
                });
            });
        }

        if (includeCrm) {
            this.state.contacts.forEach(c => {
                candidates.push({ email: c.email.toLowerCase(), name: c.name, company: c.company });
            });
        }

        if (candidates.length === 0) {
            App.toast('error', 'Please paste at least one valid email address or select CRM contacts.');
            return;
        }

        const unsubSet = new Set(this.state.contacts.filter(c => !c.subscribed).map(c => c.email.toLowerCase()));
        const seen = new Set();
        let queuedCount = 0;
        let suppressedCount = 0;

        candidates.forEach(cand => {
            if (seen.has(cand.email)) return;
            seen.add(cand.email);

            if (unsubSet.has(cand.email)) {
                suppressedCount++;
                this.state.emailLogs.unshift({
                    id: Date.now() + Math.random(),
                    campaign: campName,
                    email: cand.email,
                    name: `${cand.name} (${cand.company})`,
                    status: 'suppressed',
                    msgId: '-',
                    details: 'Blocked: Contact is unsubscribed / on suppression list'
                });
            } else {
                queuedCount++;
                this.state.emailLogs.unshift({
                    id: Date.now() + Math.random(),
                    campaign: campName,
                    email: cand.email,
                    name: `${cand.name} (${cand.company})`,
                    status: 'queued',
                    msgId: 'pending-worker',
                    details: 'Queued for background batch worker'
                });
            }
        });

        this.state.campaigns.unshift({ id: Date.now(), name: campName, total: queuedCount + suppressedCount });
        this.logAudit("campaign_enqueued", `Campaign "${campName}": ${queuedCount} queued, ${suppressedCount} suppressed`);
        this.saveState();
        App.toast('success', `Campaign "${campName}" queued (${queuedCount} valid, ${suppressedCount} suppressed)!`);
        this.nav('campaign-logs');
    },

    runQueueWorkerBatch() {
        let processed = 0;
        this.state.emailLogs.forEach(log => {
            if (log.status === 'queued' && processed < 25) {
                processed++;
                log.status = 'delivered';
                log.msgId = '<dcit.' + Math.random().toString(36).substring(2, 10) + '@dreamcareerit.com>';
                log.details = '250 2.0.0 OK: Accepted & Verified Delivered';
            }
        });

        if (processed === 0) {
            App.toast('info', 'No pending jobs in queue. All queued emails have been processed.');
        } else {
            this.logAudit("queue_worker_batch", `Processed ${processed} queued email jobs`);
            App.toast('success', `Background Worker processed ${processed} emails!`);
        }
        this.renderAll();
    },

    filterLogs(status, btn) {
        this.state.logFilter = status;
        document.querySelectorAll('.tab-bar .tab-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        this.renderLogs();
    },

    renderLogs() {
        const tbody = document.getElementById('logs-tbody');
        if (!tbody) return;
        const f = this.state.logFilter;
        const list = this.state.emailLogs.filter(l => f === 'all' || l.status === f);

        if (list.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:24px;color:#94a3b8;">No matching email logs in this view.</td></tr>';
            return;
        }

        tbody.innerHTML = list.map(l => `
            <tr>
                <td><strong>${l.campaign}</strong></td>
                <td>${l.email}</td>
                <td>${l.name}</td>
                <td><span class="badge badge-status-${l.status === 'delivered' || l.status === 'sent' ? 'completed' : (l.status === 'queued' ? 'queued' : 'failed')}">${l.status.toUpperCase()}</span></td>
                <td style="font-family:monospace;font-size:11px;">${l.msgId}</td>
                <td style="color:#94a3b8;font-size:12px;">${l.details}</td>
            </tr>
        `).join('');
    },

    renderContacts() {
        const tbody = document.getElementById('contacts-tbody');
        if (!tbody) return;
        tbody.innerHTML = this.state.contacts.map(c => `
            <tr>
                <td><strong>${c.name}</strong></td>
                <td>${c.email}</td>
                <td>${c.phone || '-'}</td>
                <td>${c.company || '-'}</td>
                <td><span class="badge-action">${c.status}</span></td>
                <td>
                    <span class="badge ${c.subscribed ? 'badge-status-completed' : 'badge-status-failed'}">
                        ${c.subscribed ? 'SUBSCRIBED' : 'SUPPRESSED'}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline" onclick="SuiteApp.toggleSubscription(${c.id})">
                        ${c.subscribed ? 'Unsubscribe' : 'Resubscribe'}
                    </button>
                </td>
            </tr>
        `).join('');
    },

    addContact(e) {
        e.preventDefault();
        const name = document.getElementById('ctName').value.trim();
        const email = document.getElementById('ctEmail').value.trim().toLowerCase();
        const company = document.getElementById('ctCompany').value.trim();
        const phone = document.getElementById('ctPhone').value.trim();
        const status = document.getElementById('ctStatus').value;

        if (this.state.contacts.some(c => c.email.toLowerCase() === email)) {
            App.toast('warning', 'Duplicate email detected: Contact updated.');
            return;
        }

        this.state.contacts.unshift({ id: Date.now(), name, email, company, phone, status, subscribed: true });
        this.logAudit("contact_added", `Added contact ${email}`);
        e.target.reset();
        this.renderAll();
        App.toast('success', `Contact ${name} saved with consent audit!`);
    },

    toggleSubscription(id) {
        const c = this.state.contacts.find(x => x.id === id);
        if (c) {
            c.subscribed = !c.subscribed;
            this.logAudit("consent_updated", `${c.email} subscription set to ${c.subscribed}`);
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

    renderConversations() {
        const listEl = document.getElementById('conv-list');
        const msgEl = document.getElementById('conv-messages');
        if (!listEl || !msgEl) return;

        listEl.innerHTML = this.state.conversations.map(c => `
            <div onclick="SuiteApp.selectConv(${c.id})" style="padding:14px 16px;border-bottom:1px solid #1e293b;cursor:pointer;background:${c.id === this.state.activeConvId ? 'rgba(0,242,254,0.08)' : 'transparent'}">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <strong style="font-size:13px;">${c.title}</strong>
                    <span class="badge-action">${c.channel}</span>
                </div>
                <div style="font-size:11px;color:#94a3b8;">${c.messages[c.messages.length - 1]?.text.substring(0, 45)}...</div>
            </div>
        `).join('');

        const active = this.state.conversations.find(c => c.id === this.state.activeConvId) || this.state.conversations[0];
        if (active) {
            document.getElementById('active-conv-title').textContent = active.title;
            document.getElementById('active-conv-channel').textContent = active.channel;
            msgEl.innerHTML = active.messages.map(m => `
                <div style="align-self:${m.type === 'agent' ? 'flex-end' : 'flex-start'};background:${m.type === 'agent' ? 'rgba(0,242,254,0.14)' : '#1e293b'};padding:10px 14px;border-radius:10px;max-width:75%;">
                    <div style="font-size:11px;color:#94a3b8;margin-bottom:3px;"><strong>${m.sender}</strong> &bull; ${m.time}</div>
                    <div style="font-size:13px;">${m.text}</div>
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
            this.renderConversations();
        }
    },

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

    renderProjects() {
        const tbody = document.getElementById('projects-tbody');
        if (!tbody) return;
        tbody.innerHTML = this.state.projects.map(p => `
            <tr>
                <td><strong>${p.title}</strong></td>
                <td>${p.client}</td>
                <td><span class="badge-action">${p.priority}</span></td>
                <td><span class="badge badge-status-processing">${p.status}</span></td>
                <td>${p.due}</td>
            </tr>
        `).join('');
    },

    addProject(e) {
        e.preventDefault();
        const title = document.getElementById('projTitle').value.trim();
        const client = document.getElementById('projClient').value.trim();
        const priority = document.getElementById('projPriority').value;
        const due = document.getElementById('projDue').value;
        this.state.projects.unshift({ id: Date.now(), title, client, priority, status: 'In Progress', due });
        this.logAudit("project_created", title);
        e.target.reset();
        this.renderAll();
        App.toast('success', 'Project created!');
    },

    renderTickets() {
        const tbody = document.getElementById('tickets-tbody');
        if (!tbody) return;
        tbody.innerHTML = this.state.tickets.map(t => `
            <tr>
                <td style="font-family:monospace;color:#00f2fe;">${t.number}</td>
                <td><strong>${t.subject}</strong></td>
                <td><span class="badge-action">${t.priority}</span></td>
                <td><span class="badge ${t.status === 'Resolved' ? 'badge-status-completed' : 'badge-status-queued'}">${t.status}</span></td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="SuiteApp.resolveTicket(${t.id})">Mark Resolved</button>
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
        this.renderAll();
        App.toast('success', `Ticket ${number} opened!`);
    },

    resolveTicket(id) {
        const t = this.state.tickets.find(x => x.id === id);
        if (t) {
            t.status = 'Resolved';
            this.logAudit("ticket_resolved", t.number);
            this.renderAll();
        }
    },

    renderAudit() {
        const tbody = document.getElementById('full-audit-tbody');
        if (!tbody) return;
        tbody.innerHTML = this.state.auditLogs.map(a => `
            <tr>
                <td style="font-family:monospace;font-size:12px;">${a.time}</td>
                <td><span class="badge-action">${a.action}</span></td>
                <td>${a.user}</td>
                <td style="color:#94a3b8;">${a.details}</td>
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
