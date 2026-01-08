let currentMonth = new Date().toISOString().slice(0, 7); // YYYY-MM format

// Initialize on page load
document.addEventListener("DOMContentLoaded", function () {
  initializeMonthFilter();
  setupDownloadMenu();
  loadReportData();
});

// Initialize month filter dropdown
function initializeMonthFilter() {
  const select = document.getElementById("monthFilter");
  const currentDate = new Date();

  // Generate last 12 months
  for (let i = 0; i < 12; i++) {
    const date = new Date(
      currentDate.getFullYear(),
      currentDate.getMonth() - i,
      1
    );
    const value = date.toISOString().slice(0, 7);
    const text = date.toLocaleDateString("en-US", {
      year: "numeric",
      month: "long",
    });

    const option = document.createElement("option");
    option.value = value;
    option.textContent = text;
    if (i === 0) option.selected = true;

    select.appendChild(option);
  }

  // Add change event listener
  select.addEventListener("change", function () {
    currentMonth = this.value;
    loadReportData();
  });
}

// Setup download menu toggle
function setupDownloadMenu() {
  const downloadBtn = document.getElementById("downloadBtn");
  const downloadMenu = document.getElementById("downloadMenu");

  downloadBtn.addEventListener("click", function (e) {
    e.stopPropagation();
    downloadMenu.classList.toggle("hidden");
  });

  // Close menu when clicking outside
  document.addEventListener("click", function () {
    downloadMenu.classList.add("hidden");
  });

  downloadMenu.addEventListener("click", function (e) {
    e.stopPropagation();
  });
}

// Download report
function downloadReport(type) {
  const url = `report_handler.php?action=download_report&month=${currentMonth}&type=${type}`;
  window.location.href = url;
  document.getElementById("downloadMenu").classList.add("hidden");
}

// Load all report data
async function loadReportData() {
  try {
    await Promise.all([
      loadMonthlyStats(),
      loadSalesPerformance(),
      loadStockTurnover(),
      loadDeliveryEfficiency(),
    ]);
  } catch (error) {
    console.error("Error loading report data:", error);
  }
}

// Load monthly statistics (KPI cards)
async function loadMonthlyStats() {
  try {
    const response = await fetch(
      `report_handler.php?action=get_monthly_stats&month=${currentMonth}`
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    console.log("Monthly stats data:", data);

    if (data.error) {
      console.error("Server error:", data.error);
      return;
    }

    if (data.success) {
      // Update Revenue
      document.getElementById("totalRevenue").textContent =
        "Rs " + formatNumber(data.revenue.current);
      document.getElementById(
        "revenuePrevious"
      ).textContent = `Vs. previous period (Rs ${formatNumber(
        data.revenue.previous
      )})`;
      updateChangeIndicator("revenueChange", data.revenue.change);

      // Update Units
      document.getElementById("totalUnits").textContent = formatNumber(
        data.units.current
      );
      document.getElementById(
        "unitsPrevious"
      ).textContent = `Vs. previous period (${formatNumber(
        data.units.previous
      )})`;
      updateChangeIndicator("unitsChange", data.units.change);

      // Update Deliveries
      document.getElementById("activeDeliveries").textContent =
        data.deliveries.active;
      document.getElementById(
        "ontimeRate"
      ).textContent = `${data.deliveries.ontime_rate}% On-time rate`;

      // Update Stock Health
      document.getElementById(
        "stockHealth"
      ).textContent = `${data.stock.health_score}%`;
      document.getElementById(
        "lowStockAlerts"
      ).textContent = `Low stock alerts: ${data.stock.low_stock_alerts}`;
    }
  } catch (error) {
    console.error("Error loading monthly stats:", error);
    alert("Error loading monthly statistics. Check console for details.");
  }
}

// Load sales performance data
async function loadSalesPerformance() {
  try {
    const response = await fetch(
      `report_handler.php?action=get_sales_performance&month=${currentMonth}`
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    console.log("Sales performance data:", data);

    if (data.error) {
      console.error("Server error:", data.error);
      return;
    }

    if (data.success) {
      // Render sales chart
      renderSalesChart(data.weekly_sales);

      // Render top products table
      renderTopProducts(data.top_products);
    }
  } catch (error) {
    console.error("Error loading sales performance:", error);
  }
}

// Load stock turnover data
async function loadStockTurnover() {
  try {
    const response = await fetch(
      `report_handler.php?action=get_stock_turnover&month=${currentMonth}`
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    console.log("Stock turnover data:", data);

    if (data.error) {
      console.error("Server error:", data.error);
      return;
    }

    if (data.success) {
      renderStockTurnover(data.stock_turnover);
    }
  } catch (error) {
    console.error("Error loading stock turnover:", error);
  }
}

// Load delivery efficiency data
async function loadDeliveryEfficiency() {
  try {
    const response = await fetch(
      `report_handler.php?action=get_delivery_efficiency&month=${currentMonth}`
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    console.log("Delivery efficiency data:", data);

    if (data.error) {
      console.error("Server error:", data.error);
      return;
    }

    if (data.success) {
      // Calculate overall on-time rate from status data
      const delivered = data.by_status.find((s) => s.status === "delivered");
      const total = data.by_status.reduce((sum, s) => sum + s.count, 0);
      const ontimeRate =
        total > 0 ? Math.round(((delivered?.count || 0) / total) * 100) : 0;

      // Update gauge
      updateOntimeGauge(ontimeRate);

      // Calculate average delivery time
      const avgTime = delivered?.avg_time || 0;
      document.getElementById("avgDeliveryTime").textContent =
        avgTime.toFixed(1) + "h";

      // Render driver performance
      renderDriverPerformance(data.by_driver);
    }
  } catch (error) {
    console.error("Error loading delivery efficiency:", error);
  }
}

// Render sales chart (wave/line chart with gradient)
function renderSalesChart(weeklyData) {
  const chartContainer = document.getElementById("salesChart");

  if (!weeklyData || weeklyData.length === 0) {
    chartContainer.innerHTML =
      '<div class="flex items-center justify-center h-full text-text-sec-light dark:text-text-sec-dark">No sales data for this month</div>';
    return;
  }

  const maxRevenue = Math.max(...weeklyData.map((w) => w.revenue));
  const chartHeight = 250;
  const chartWidth = chartContainer.offsetWidth || 600;
  const padding = 40;
  const pointRadius = 6;

  // Calculate points
  const points = weeklyData.map((week, index) => {
    const x =
      padding +
      (index * (chartWidth - padding * 2)) / (weeklyData.length - 1 || 1);
    const y =
      chartHeight -
      padding -
      (week.revenue / maxRevenue) * (chartHeight - padding * 2);
    return {
      x,
      y,
      week: week.week,
      revenue: week.revenue,
      orders: week.orders,
    };
  });

  // Create smooth curve path using quadratic bezier curves
  let pathD = `M ${points[0].x} ${points[0].y}`;
  for (let i = 0; i < points.length - 1; i++) {
    const current = points[i];
    const next = points[i + 1];
    const controlX = (current.x + next.x) / 2;
    pathD += ` Q ${controlX} ${current.y}, ${next.x} ${next.y}`;
  }

  // Create area path (for gradient fill)
  const areaPath =
    pathD +
    ` L ${points[points.length - 1].x} ${chartHeight - padding} L ${
      points[0].x
    } ${chartHeight - padding} Z`;

  let html = `
    <svg class="w-full h-full" viewBox="0 0 ${chartWidth} ${chartHeight}" preserveAspectRatio="xMidYMid meet">
      <defs>
        <linearGradient id="waveGradient" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" style="stop-color:#11d452;stop-opacity:0.3" />
          <stop offset="100%" style="stop-color:#11d452;stop-opacity:0.05" />
        </linearGradient>
        <filter id="glow">
          <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
          <feMerge>
            <feMergeNode in="coloredBlur"/>
            <feMergeNode in="SourceGraphic"/>
          </feMerge>
        </filter>
      </defs>
      
      <!-- Grid lines -->
      ${Array.from({ length: 5 }, (_, i) => {
        const y = padding + (i * (chartHeight - padding * 2)) / 4;
        return `<line x1="${padding}" y1="${y}" x2="${
          chartWidth - padding
        }" y2="${y}" stroke="currentColor" stroke-opacity="0.1" stroke-width="1"/>`;
      }).join("")}
      
      <!-- Area fill -->
      <path d="${areaPath}" fill="url(#waveGradient)" />
      
      <!-- Wave line -->
      <path d="${pathD}" stroke="#11d452" stroke-width="3" fill="none" filter="url(#glow)" />
      
      <!-- Data points -->
      ${points
        .map(
          (point, index) => `
        <g class="chart-point" data-index="${index}">
          <circle cx="${point.x}" cy="${point.y}" r="${
            pointRadius + 2
          }" fill="#11d452" opacity="0.2"/>
          <circle cx="${point.x}" cy="${
            point.y
          }" r="${pointRadius}" fill="#11d452" stroke="#fff" stroke-width="2"/>
        </g>
      `
        )
        .join("")}
      
      <!-- Week labels -->
      ${points
        .map(
          (point) => `
        <text x="${point.x}" y="${
            chartHeight - 10
          }" text-anchor="middle" class="text-xs fill-current text-text-sec-light dark:text-text-sec-dark" style="font-size: 12px;">
          ${point.week}
        </text>
      `
        )
        .join("")}
    </svg>
  `;

  chartContainer.innerHTML = html;

  // Add hover interactions
  const chartPoints = chartContainer.querySelectorAll(".chart-point");
  chartPoints.forEach((point, index) => {
    const data = points[index];

    point.addEventListener("mouseenter", (e) => {
      // Create tooltip
      const tooltip = document.createElement("div");
      tooltip.id = "chart-tooltip";
      tooltip.className =
        "absolute bg-card-light dark:bg-card-dark px-3 py-2 rounded-lg shadow-xl border border-border-light dark:border-border-dark z-50 pointer-events-none";
      tooltip.innerHTML = `
        <div class="text-xs font-bold text-primary">Rs ${formatNumber(
          data.revenue
        )}</div>
        <div class="text-[10px] text-text-sec-light dark:text-text-sec-dark">${
          data.orders
        } orders</div>
      `;

      const rect = chartContainer.getBoundingClientRect();
      tooltip.style.left = data.x - 40 + "px";
      tooltip.style.top = data.y - 50 + "px";

      chartContainer.style.position = "relative";
      chartContainer.appendChild(tooltip);

      // Enlarge point
      const circles = point.querySelectorAll("circle");
      circles[0].setAttribute("r", pointRadius + 4);
      circles[1].setAttribute("r", pointRadius + 2);
    });

    point.addEventListener("mouseleave", () => {
      const tooltip = document.getElementById("chart-tooltip");
      if (tooltip) tooltip.remove();

      // Reset point size
      const circles = point.querySelectorAll("circle");
      circles[0].setAttribute("r", pointRadius + 2);
      circles[1].setAttribute("r", pointRadius);
    });
  });
}

// Render top products table
function renderTopProducts(products) {
  const tbody = document.getElementById("topProductsTable");

  if (!products || products.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="4" class="py-8 text-center text-text-sec-light dark:text-text-sec-dark">No products sold this month</td></tr>';
    return;
  }

  let html = "";
  products.forEach((product) => {
    html += `
            <tr class="group hover:bg-background-light dark:hover:bg-background-dark">
                <td class="py-3 font-medium">${escapeHtml(product.name)}</td>
                <td class="py-3 text-text-sec-light dark:text-text-sec-dark">${escapeHtml(
                  product.sku
                )}</td>
                <td class="py-3 text-right font-bold">${formatNumber(
                  product.units_sold
                )}</td>
                <td class="py-3 text-right font-bold text-primary">Rs ${formatNumber(
                  product.revenue
                )}</td>
            </tr>
        `;
  });

  tbody.innerHTML = html;
}

// Render stock turnover table
function renderStockTurnover(stockData) {
  const tbody = document.getElementById("stockTurnoverTable");

  if (!stockData || stockData.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="3" class="py-8 text-center text-text-sec-light dark:text-text-sec-dark">No stock data available</td></tr>';
    return;
  }

  let html = "";
  // Show top 10 by turnover ratio
  stockData.slice(0, 10).forEach((item) => {
    const statusBadge = getStockStatusBadge(item.stock_status);
    html += `
            <tr class="group hover:bg-background-light dark:hover:bg-background-dark">
                <td class="py-3">
                    <div class="font-medium">${escapeHtml(item.name)}</div>
                    <div class="text-xs text-text-sec-light dark:text-text-sec-dark">${escapeHtml(
                      item.category || "Uncategorized"
                    )}</div>
                </td>
                <td class="py-3 text-right font-bold">${
                  item.turnover_ratio
                }</td>
                <td class="py-3 text-right">${statusBadge}</td>
            </tr>
        `;
  });

  tbody.innerHTML = html;
}

// Render driver performance
function renderDriverPerformance(drivers) {
  const container = document.getElementById("driverPerformance");

  if (!drivers || drivers.length === 0) {
    container.innerHTML =
      '<div class="text-center text-text-sec-light dark:text-text-sec-dark py-4">No driver data for this month</div>';
    return;
  }

  let html = "";
  // Show top 5 drivers by completed deliveries
  drivers.slice(0, 5).forEach((driver) => {
    const completionRate =
      driver.total > 0
        ? Math.round((driver.completed / driver.total) * 100)
        : 0;
    html += `
            <div class="flex items-center justify-between p-3 bg-background-light dark:bg-background-dark rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center">
                        <span class="text-primary font-bold text-sm">${getInitials(
                          driver.driver
                        )}</span>
                    </div>
                    <div>
                        <div class="font-medium text-sm">${escapeHtml(
                          driver.driver
                        )}</div>
                        <div class="text-xs text-text-sec-light dark:text-text-sec-dark">${
                          driver.completed
                        }/${driver.total} deliveries</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-bold text-primary">${completionRate}%</div>
                    <div class="text-xs text-text-sec-light dark:text-text-sec-dark">${
                      driver.avg_time
                    }h avg</div>
                </div>
            </div>
        `;
  });

  container.innerHTML = html;
}

// Update change indicator
function updateChangeIndicator(elementId, change) {
  const element = document.getElementById(elementId);
  const isPositive = change >= 0;
  const icon = isPositive ? "trending_up" : "trending_down";
  const colorClass = isPositive ? "text-primary" : "text-red-500";

  element.className = `flex items-center ${colorClass} text-sm font-bold bg-primary/5 px-2 py-1 rounded`;
  element.innerHTML = `
        <span class="material-symbols-outlined text-[16px] mr-1">${icon}</span> 
        ${isPositive ? "+" : ""}${change}%
    `;
}

// Update on-time rate gauge
function updateOntimeGauge(rate) {
  const gauge = document.getElementById("ontimeGauge");
  const text = document.getElementById("ontimeGaugeText");

  gauge.setAttribute("stroke-dasharray", `${rate}, 100`);
  text.textContent = `${rate}%`;
}

// Get stock status badge
function getStockStatusBadge(status) {
  const badges = {
    healthy:
      '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Healthy</span>',
    low: '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Low Stock</span>',
    out: '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Out of Stock</span>',
  };
  return badges[status] || badges["healthy"];
}

// Helper functions
function formatNumber(num) {
  return new Intl.NumberFormat("en-US", {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  }).format(num);
}

function getInitials(name) {
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .toUpperCase()
    .slice(0, 2);
}

function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}
