<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kompakt Plan Bilgisi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .plan-info-compact {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 16px;
            margin-top: 12px;
            border-left: 4px solid #3e546a;
        }

        .plan-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e9ecef;
        }

        .plan-name-compact {
            font-weight: 600;
            color: #3e546a;
            margin: 0;
        }

        .plan-price-compact {
            font-weight: 700;
            color: #28a745;
            margin: 0;
        }

        .limits-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 12px;
        }

        .limit-item {
            background: white;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            min-width: fit-content;
        }

        .limit-value {
            background: #3e546a;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
            margin-right: 6px;
            min-width: 24px;
            text-align: center;
            font-size: 0.8rem;
        }

        .limit-value.unlimited {
            background: #28a745;
        }

        .features-compact {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .feature-tag {
            background: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .feature-tag::before {
            content: '✓';
            margin-right: 4px;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .plan-summary {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }
            
            .limits-row {
                gap: 8px;
            }
            
            .limit-item {
                font-size: 0.85rem;
                padding: 6px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- Select Dropdown -->
                <div class="mb-3">
                    <label for="subscription_plan" class="form-label">Abonelik Planı Seçin</label>
                    <select id="subscription_plan" class="form-select">
                        <option value="">Plan Seçiniz...</option>
                        <option value="1" data-name="Başlangıç" data-price="99.00" data-billing_cycle="monthly" 
                                data-users="3" data-dealers="10" data-stocks="100" data-konsinye="0"
                                data-tickets="true" data-basic_reports="true" data-inventory="false">
                            Başlangıç - ₺99.00/ay
                        </option>
                        <option value="2" data-name="Profesyonel" data-price="199.00" data-billing_cycle="monthly" 
                                data-users="10" data-dealers="50" data-stocks="500" data-konsinye="200"
                                data-tickets="true" data-basic_reports="true" data-inventory="true">
                            Profesyonel - ₺199.00/ay
                        </option>
                        <option value="3" data-name="Kurumsal" data-price="399.00" data-billing_cycle="monthly" 
                                data-users="-1" data-dealers="-1" data-stocks="-1" data-konsinye="-1"
                                data-tickets="true" data-basic_reports="true" data-inventory="true">
                            Kurumsal - ₺399.00/ay
                        </option>
                    </select>
                </div>

                <!-- Kompakt Plan Bilgisi -->
                <div id="planInfo" class="plan-info" style="display: none;">
                    <div class="plan-features"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#subscription_plan').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                if (selectedOption.val()) {
                    const planData = selectedOption.data();
                    showCompactPlanInfo(planData);
                } else {
                    hidePlanInfo();
                }
            });

            function showCompactPlanInfo(planData) {
                const planInfo = $('#planInfo');
                const featuresDiv = planInfo.find('.plan-features');
                
                // Format functions
                function formatLimitValue(value) {
                    if (value == -1) return 'Sınırsız';
                    if (value == 0) return null; // 0 ise null döndür
                    return value.toLocaleString('tr-TR');
                }

                function formatPrice(price, cycle) {
                    const formattedPrice = parseFloat(price).toLocaleString('tr-TR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    const cycleName = cycle === 'yearly' ? 'yıl' : 'ay';
                    return `₺${formattedPrice}/${cycleName}`;
                }

                // Limit items oluştur (sadece 0'dan büyük olanları)
                let limitItems = [];
                
                const limits = [
                    { key: 'users', label: 'Kullanıcı', value: planData.users },
                    { key: 'dealers', label: 'Bayi', value: planData.dealers },
                    { key: 'stocks', label: 'Stok', value: planData.stocks },
                    { key: 'konsinye', label: 'Konsinye', value: planData.konsinye }
                ];

                limits.forEach(limit => {
                    const formattedValue = formatLimitValue(limit.value);
                    if (formattedValue !== null) { // 0 olanları gösterme
                        const isUnlimited = limit.value == -1;
                        limitItems.push(`
                            <div class="limit-item">
                                <span class="limit-value ${isUnlimited ? 'unlimited' : ''}">${isUnlimited ? '∞' : limit.value}</span>
                                <span>${limit.label}</span>
                            </div>
                        `);
                    }
                });

                // Feature tags oluştur
                let featureTags = [];
                if (planData.tickets === 'true' || planData.tickets === true) {
                    featureTags.push('<span class="feature-tag">Ticket</span>');
                }
                if (planData.basic_reports === 'true' || planData.basic_reports === true) {
                    featureTags.push('<span class="feature-tag">Raporlar</span>');
                }
                if (planData.inventory === 'true' || planData.inventory === true) {
                    featureTags.push('<span class="feature-tag">Stok Yönetimi</span>');
                }

                const compactHTML = `
                    <div class="plan-info-compact">
                        <div class="plan-summary">
                            <h6 class="plan-name-compact">${planData.name} Planı</h6>
                            <div class="plan-price-compact">${formatPrice(planData.price, planData.billing_cycle)}</div>
                        </div>
                        
                        ${limitItems.length > 0 ? `
                            <div class="limits-row">
                                ${limitItems.join('')}
                            </div>
                        ` : ''}
                        
                        ${featureTags.length > 0 ? `
                            <div class="features-compact">
                                ${featureTags.join('')}
                            </div>
                        ` : ''}
                    </div>
                `;
                
                featuresDiv.html(compactHTML);
                planInfo.show();
            }

            function hidePlanInfo() {
                $('#planInfo').hide();
            }

            // Demo
            setTimeout(() => {
                $('#subscription_plan').val('1').trigger('change');
            }, 500);

            setTimeout(() => {
                $('#subscription_plan').val('2').trigger('change');
            }, 2000);

            setTimeout(() => {
                $('#subscription_plan').val('3').trigger('change');
            }, 4000);
        });
    </script>
</body>
</html>