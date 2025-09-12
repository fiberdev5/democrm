<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Paket Seçimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3e546a;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-bg: #f8f9fa;
            --border-radius: 12px;
            --shadow: 0 4px 16px rgba(0,0,0,0.1);
            --shadow-hover: 0 8px 25px rgba(0,0,0,0.15);
            --transition: all 0.3s ease;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            padding: 40px 20px;
        }

        .plan-selector {
            background: white;
            border-radius: var(--border-radius);
            padding: 40px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h3 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .section-title p {
            color: var(--secondary-color);
            font-size: 1.1rem;
        }

        /* Plan Cards Yaklaşımı */
        .plan-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .plan-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 30px 24px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-color);
        }

        .plan-card.selected {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, #667eea15, #764ba215);
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .plan-card.selected::before {
            content: '✓';
            position: absolute;
            top: -10px;
            right: -10px;
            background: var(--success-color);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .plan-card.popular::after {
            content: 'En Popüler';
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .plan-header {
            margin-bottom: 20px;
        }

        .plan-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .plan-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 4px;
        }

        .plan-period {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .plan-features {
            list-style: none;
            padding: 0;
            margin: 20px 0;
            flex-grow: 1;
        }

        .plan-features li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            color: #495057;
        }

        .plan-features li .icon {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .plan-features li .icon.check {
            background: #d4edda;
            color: #155724;
        }

        .plan-features li .icon.info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .plan-features li .icon.unlimited {
            background: #fff3cd;
            color: #856404;
        }

        /* Comparison Table Yaklaşımı */
        .comparison-toggle {
            text-align: center;
            margin-bottom: 30px;
        }

        .toggle-btn {
            background: #e9ecef;
            border: none;
            padding: 12px 24px;
            border-radius: var(--border-radius);
            margin: 0 5px;
            transition: var(--transition);
            font-weight: 500;
        }

        .toggle-btn.active {
            background: var(--primary-color);
            color: white;
        }

        .comparison-table {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            display: none;
        }

        .comparison-table.active {
            display: block;
        }

        .comparison-table table {
            width: 100%;
            margin: 0;
        }

        .comparison-table th {
            background: var(--light-bg);
            padding: 20px 15px;
            font-weight: 600;
            text-align: center;
            border-bottom: 2px solid #e9ecef;
        }

        .comparison-table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
        }

        .comparison-table tr:hover {
            background: #f8f9fa;
        }

        .feature-name {
            font-weight: 500;
            text-align: left !important;
            color: var(--primary-color);
        }

        .feature-value {
            font-weight: 600;
        }

        .feature-value.unlimited {
            color: var(--success-color);
        }

        .feature-value.limited {
            color: var(--warning-color);
        }

        .feature-check {
            color: var(--success-color);
            font-size: 1.2rem;
        }

        .feature-cross {
            color: var(--danger-color);
            font-size: 1.2rem;
        }

        .plan-select-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: var(--transition);
        }

        .plan-select-btn:hover {
            background: #2c3e50;
            transform: translateY(-2px);
        }

        .plan-select-btn.selected {
            background: var(--success-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .plan-cards {
                grid-template-columns: 1fr;
            }
            
            .comparison-table table {
                font-size: 0.9rem;
            }
            
            .plan-selector {
                padding: 20px;
            }
        }

        /* Loading Animation */
        .loading {
            text-align: center;
            padding: 40px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="plan-selector">
            <div class="section-title">
                <h3>Size Uygun Planı Seçin</h3>
                <p>İş ihtiyaçlarınıza en uygun paketi seçin ve hemen başlayın</p>
            </div>

            <!-- View Toggle -->
            <div class="comparison-toggle">
                <button class="toggle-btn active" id="cardsView">Kart Görünümü</button>
                <button class="toggle-btn" id="tableView">Karşılaştırma Tablosu</button>
            </div>

            <!-- Loading State -->
            <div class="loading" id="loadingState">
                <div class="spinner"></div>
                <p>Planlar yükleniyor...</p>
            </div>

            <!-- Cards View -->
            <div id="cardsContainer" class="plan-cards" style="display: none;"></div>

            <!-- Comparison Table View -->
            <div id="tableContainer" class="comparison-table"></div>

            <!-- Hidden Input for Selected Plan -->
            <input type="hidden" id="selectedPlan" name="subscription_plan" value="">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        class PlanSelector {
            constructor() {
                this.selectedPlan = null;
                this.currentView = 'cards';
                this.plans = [];
                this.init();
            }

            init() {
                this.bindEvents();
                this.loadPlans();
            }

            bindEvents() {
                document.getElementById('cardsView').addEventListener('click', () => this.switchView('cards'));
                document.getElementById('tableView').addEventListener('click', () => this.switchView('table'));
            }

            switchView(view) {
                this.currentView = view;
                
                // Update toggle buttons
                document.querySelectorAll('.toggle-btn').forEach(btn => btn.classList.remove('active'));
                document.getElementById(view === 'cards' ? 'cardsView' : 'tableView').classList.add('active');
                
                // Update containers
                if (view === 'cards') {
                    document.getElementById('cardsContainer').style.display = 'grid';
                    document.getElementById('tableContainer').classList.remove('active');
                } else {
                    document.getElementById('cardsContainer').style.display = 'none';
                    document.getElementById('tableContainer').classList.add('active');
                }
            }

            async loadPlans() {
                // Simulate API call
                setTimeout(() => {
                    this.plans = [
                        {
                            id: 1,
                            name: 'Başlangıç',
                            price: 99,
                            billing_cycle: 'monthly',
                            popular: false,
                            limits: {
                                users: 3,
                                dealers: 10,
                                stocks: 100,
                                konsinye: 50
                            },
                            features: {
                                tickets: true,
                                basic_reports: true,
                                inventory: false,
                                advanced_reports: false,
                                api_access: false
                            }
                        },
                        {
                            id: 2,
                            name: 'Profesyonel',
                            price: 199,
                            billing_cycle: 'monthly',
                            popular: true,
                            limits: {
                                users: 10,
                                dealers: 50,
                                stocks: 500,
                                konsinye: 200
                            },
                            features: {
                                tickets: true,
                                basic_reports: true,
                                inventory: true,
                                advanced_reports: true,
                                api_access: false
                            }
                        },
                        {
                            id: 3,
                            name: 'Kurumsal',
                            price: 399,
                            billing_cycle: 'monthly',
                            popular: false,
                            limits: {
                                users: -1,
                                dealers: -1,
                                stocks: -1,
                                konsinye: -1
                            },
                            features: {
                                tickets: true,
                                basic_reports: true,
                                inventory: true,
                                advanced_reports: true,
                                api_access: true
                            }
                        }
                    ];
                    
                    this.renderPlans();
                    document.getElementById('loadingState').style.display = 'none';
                    document.getElementById('cardsContainer').style.display = 'grid';
                }, 1500);
            }

            renderPlans() {
                this.renderCards();
                this.renderTable();
            }

            renderCards() {
                const container = document.getElementById('cardsContainer');
                container.innerHTML = '';

                this.plans.forEach(plan => {
                    const card = document.createElement('div');
                    card.className = `plan-card ${plan.popular ? 'popular' : ''}`;
                    card.dataset.planId = plan.id;
                    
                    card.innerHTML = `
                        <div class="plan-header">
                            <div class="plan-name">${plan.name}</div>
                            <div class="plan-price">₺${plan.price}</div>
                            <div class="plan-period">/${plan.billing_cycle === 'yearly' ? 'yıl' : 'ay'}</div>
                        </div>
                        <ul class="plan-features">
                            <li>
                                <span class="icon ${plan.limits.users === -1 ? 'unlimited' : 'info'}">${plan.limits.users === -1 ? '∞' : plan.limits.users}</span>
                                <span>${plan.limits.users === -1 ? 'Sınırsız' : plan.limits.users} Kullanıcı</span>
                            </li>
                            <li>
                                <span class="icon ${plan.limits.dealers === -1 ? 'unlimited' : 'info'}">${plan.limits.dealers === -1 ? '∞' : plan.limits.dealers}</span>
                                <span>${plan.limits.dealers === -1 ? 'Sınırsız' : plan.limits.dealers} Bayi</span>
                            </li>
                            <li>
                                <span class="icon ${plan.limits.stocks === -1 ? 'unlimited' : 'info'}">${plan.limits.stocks === -1 ? '∞' : plan.limits.stocks}</span>
                                <span>${plan.limits.stocks === -1 ? 'Sınırsız' : plan.limits.stocks} Stok Kalemi</span>
                            </li>
                            <li>
                                <span class="icon ${plan.limits.konsinye === -1 ? 'unlimited' : 'info'}">${plan.limits.konsinye === -1 ? '∞' : plan.limits.konsinye}</span>
                                <span>${plan.limits.konsinye === -1 ? 'Sınırsız' : plan.limits.konsinye} Konsinye</span>
                            </li>
                            <li>
                                <span class="icon check">✓</span>
                                <span>Ticket Sistemi</span>
                            </li>
                            <li>
                                <span class="icon check">✓</span>
                                <span>Temel Raporlar</span>
                            </li>
                            ${plan.features.inventory ? '<li><span class="icon check">✓</span><span>Stok Yönetimi</span></li>' : ''}
                            ${plan.features.advanced_reports ? '<li><span class="icon check">✓</span><span>Gelişmiş Raporlar</span></li>' : ''}
                            ${plan.features.api_access ? '<li><span class="icon check">✓</span><span>API Erişimi</span></li>' : ''}
                        </ul>
                    `;

                    card.addEventListener('click', () => this.selectPlan(plan.id, card));
                    container.appendChild(card);
                });
            }

            renderTable() {
                const container = document.getElementById('tableContainer');
                
                const table = `
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>Özellikler</th>
                                ${this.plans.map(plan => `
                                    <th>
                                        <div style="margin-bottom: 10px;">
                                            <strong>${plan.name}</strong><br>
                                            <span style="font-size: 1.5rem; color: var(--primary-color);">₺${plan.price}</span>
                                            <small>/${plan.billing_cycle === 'yearly' ? 'yıl' : 'ay'}</small>
                                        </div>
                                        <button class="plan-select-btn" data-plan-id="${plan.id}">
                                            Seç
                                        </button>
                                    </th>
                                `).join('')}
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="feature-name">Kullanıcı Sayısı</td>
                                ${this.plans.map(plan => `
                                    <td class="feature-value ${plan.limits.users === -1 ? 'unlimited' : 'limited'}">
                                        ${plan.limits.users === -1 ? 'Sınırsız' : plan.limits.users}
                                    </td>
                                `).join('')}
                            </tr>
                            <tr>
                                <td class="feature-name">Bayi Sayısı</td>
                                ${this.plans.map(plan => `
                                    <td class="feature-value ${plan.limits.dealers === -1 ? 'unlimited' : 'limited'}">
                                        ${plan.limits.dealers === -1 ? 'Sınırsız' : plan.limits.dealers}
                                    </td>
                                `).join('')}
                            </tr>
                            <tr>
                                <td class="feature-name">Stok Kalemi</td>
                                ${this.plans.map(plan => `
                                    <td class="feature-value ${plan.limits.stocks === -1 ? 'unlimited' : 'limited'}">
                                        ${plan.limits.stocks === -1 ? 'Sınırsız' : plan.limits.stocks}
                                    </td>
                                `).join('')}
                            </tr>
                            <tr>
                                <td class="feature-name">Konsinye</td>
                                ${this.plans.map(plan => `
                                    <td class="feature-value ${plan.limits.konsinye === -1 ? 'unlimited' : 'limited'}">
                                        ${plan.limits.konsinye === -1 ? 'Sınırsız' : plan.limits.konsinye}
                                    </td>
                                `).join('')}
                            </tr>
                            <tr>
                                <td class="feature-name">Ticket Sistemi</td>
                                ${this.plans.map(plan => `
                                    <td class="feature-check">✓</td>
                                `).join('')}
                            </tr>
                            <tr>
                                <td class="feature-name">Temel Raporlar</td>
                                ${this.plans.map(plan => `
                                    <td class="feature-check">✓</td>
                                `).join('')}
                            </tr>
                            <tr>
                                <td class="feature-name">Stok Yönetimi</td>
                                ${this.plans.map(plan => `
                                    <td class="${plan.features.inventory ? 'feature-check' : 'feature-cross'}">
                                        ${plan.features.inventory ? '✓' : '✗'}
                                    </td>
                                `).join('')}
                            </tr>
                            <tr>
                                <td class="feature-name">Gelişmiş Raporlar</td>
                                ${this.plans.map(plan => `
                                    <td class="${plan.features.advanced_reports ? 'feature-check' : 'feature-cross'}">
                                        ${plan.features.advanced_reports ? '✓' : '✗'}
                                    </td>
                                `).join('')}
                            </tr>
                            <tr>
                                <td class="feature-name">API Erişimi</td>
                                ${this.plans.map(plan => `
                                    <td class="${plan.features.api_access ? 'feature-check' : 'feature-cross'}">
                                        ${plan.features.api_access ? '✓' : '✗'}
                                    </td>
                                `).join('')}
                            </tr>
                        </tbody>
                    </table>
                `;

                container.innerHTML = table;

                // Add event listeners to table buttons
                container.querySelectorAll('.plan-select-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const planId = parseInt(e.target.dataset.planId);
                        this.selectPlan(planId, null, e.target);
                    });
                });
            }

            selectPlan(planId, cardElement = null, buttonElement = null) {
                this.selectedPlan = planId;
                
                // Update hidden input
                document.getElementById('selectedPlan').value = planId;
                
                if (this.currentView === 'cards') {
                    // Update card selection
                    document.querySelectorAll('.plan-card').forEach(card => {
                        card.classList.remove('selected');
                    });
                    
                    if (cardElement) {
                        cardElement.classList.add('selected');
                    }
                } else {
                    // Update table button states
                    document.querySelectorAll('.plan-select-btn').forEach(btn => {
                        btn.classList.remove('selected');
                        btn.textContent = 'Seç';
                    });
                    
                    if (buttonElement) {
                        buttonElement.classList.add('selected');
                        buttonElement.textContent = 'Seçildi ✓';
                    }
                }
                
                console.log('Seçilen plan:', planId);
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new PlanSelector();
        });
    </script>
</body>
</html>