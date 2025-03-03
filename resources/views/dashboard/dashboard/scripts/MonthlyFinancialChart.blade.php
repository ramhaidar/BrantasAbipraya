<!-- Common data and utilities -->
<script>
    // Load data from controller
    const monthlyAtbData = @json($monthlyFinancialData['atb'] ?? []);
    const monthlyApbData = @json($monthlyFinancialData['apb'] ?? []);
    const monthlySaldoData = @json($monthlyFinancialData['saldo'] ?? []);

    // Function to create a placeholder for charts not yet loaded
    function createPlaceholder(elementId, title) {
        const chartElement = document.getElementById(elementId);
        if (!chartElement) return;

        // Clear any existing content
        chartElement.innerHTML = '';

        // Add a placeholder message
        const placeholder = document.createElement('div');
        placeholder.style.padding = '40px';
        placeholder.style.color = 'white';
        placeholder.style.fontSize = '16px';
        placeholder.textContent = title + ' per bulan akan ditampilkan di sini';
        chartElement.appendChild(placeholder);
    }

    // Remove any tooltips when resizing
    function clearTooltips() {
        d3.selectAll('.tooltip').remove();
    }
</script>

<!-- ATB Chart Implementation -->
<script>
    // Monthly ATB chart implementation
    function monthlyAtbChart(containerId = 'monthlyAtbChart', isModal = false) {
        const chartElement = document.getElementById(containerId);

        if (!chartElement) return;

        // Clear any existing content
        chartElement.innerHTML = '';

        // Set up chart dimensions and margins - Larger for modal
        const margin = {
            top: isModal ? 30 : 15,
            right: isModal ? 30 : 15,
            bottom: isModal ? 50 : 40,
            left: isModal ? 120 : 80
        };

        // Set width and height - Larger for modal
        const width = Math.min(chartElement.clientWidth - margin.left - margin.right, isModal ? 800 : 375);
        const height = (isModal ? 500 : 300) - margin.top - margin.bottom;

        // Create SVG container
        const svg = d3.select('#' + containerId)
            .append('svg')
            .attr('width', width + margin.left + margin.right)
            .attr('height', height + margin.top + margin.bottom)
            .append('g')
            .attr('transform', `translate(${margin.left},${margin.top})`);

        // Create tooltip div
        const tooltip = d3.select('body').append('div')
            .attr('class', 'tooltip')
            .style('opacity', 0);

        // X scale - months
        const x = d3.scaleBand()
            .domain(monthlyAtbData.map(d => d.month))
            .range([0, width])
            .padding(isModal ? 0.3 : 0.2); // More padding in modal for better visibility

        // Y scale - ATB values
        const y = d3.scaleLinear()
            .domain([0, d3.max(monthlyAtbData, d => d.value) * 1.1]) // Add 10% padding
            .range([height, 0]);

        // Add X axis with appropriate font size
        svg.append('g')
            .attr('transform', `translate(0,${height})`)
            .call(d3.axisBottom(x))
            .selectAll('text')
            .style('fill', 'white')
            .style('font-size', isModal ? '12px' : '8px')
            .style('pointer-events', 'none'); // Make axis text non-interactive

        // Add Y axis with formatted labels and appropriate font size
        svg.append('g')
            .call(d3.axisLeft(y)
                .ticks(isModal ? 10 : 5) // More ticks in modal
                .tickFormat(d => 'Rp' + d3.format(',')(d).replace(/,/g, '.'))
            )
            .selectAll('text')
            .style('fill', 'white')
            .style('font-size', isModal ? '10px' : '7px')
            .style('pointer-events', 'none'); // Make axis text non-interactive

        // Add X axis label with appropriate font size
        svg.append('text')
            .attr('class', 'axis-label')
            .attr('text-anchor', 'middle')
            .attr('x', width / 2)
            .attr('y', height + (isModal ? margin.bottom - 10 : margin.bottom))
            .style('fill', 'white')
            .style('font-size', isModal ? '14px' : '10px')
            .style('pointer-events', 'none') // Make label non-interactive
            .text('Bulan');

        // Add Y axis label with appropriate font size
        svg.append('text')
            .attr('class', 'axis-label')
            .attr('text-anchor', 'middle')
            .attr('transform', 'rotate(-90)')
            .attr('x', -height / 2)
            .attr('y', -(isModal ? margin.left - 10 : margin.left - 10))
            .style('fill', 'white')
            .style('font-size', isModal ? '14px' : '10px')
            .style('pointer-events', 'none') // Make label non-interactive
            .text('Nilai ATB (Rupiah)');

        // Use single solid color for ATB without hover effect
        const atbColor = '#fe6d73'; // Red color for ATB

        // Create bar groups
        const barGroups = svg.selectAll('.bar-group')
            .data(monthlyAtbData)
            .enter()
            .append('g')
            .attr('class', 'bar-group');

        // Add invisible overlay rectangles FIRST (before visible bars)
        // These extend from the bottom to the top with full height for complete coverage
        barGroups.append('rect')
            .attr('class', 'bar-overlay')
            .attr('x', d => x(d.month))
            .attr('width', x.bandwidth())
            .attr('y', 0) // Start from the top
            .attr('height', height) // Cover the full height
            .attr('fill', 'transparent') // Make it invisible
            .style('cursor', 'pointer')
            .on('mouseover', function(event, d) {
                // Show tooltip
                tooltip.transition()
                    .duration(200)
                    .style('opacity', 0.9);

                tooltip.html(`<strong>Bulan: ${d.month}</strong><br>
                              <strong>ATB:</strong> Rp${d.value.toLocaleString('de-DE', {
                                  minimumFractionDigits: 2,
                                  maximumFractionDigits: 2
                              }).replace(/\./g, ',').replace(/,/g, '.')}`)
                    .style('left', (event.pageX + 10) + 'px')
                    .style('top', (event.pageY - 28) + 'px');
            })
            .on('mouseout', function() {
                // Hide tooltip
                tooltip.transition()
                    .duration(500)
                    .style('opacity', 0);
            });

        // Add the visible bars
        barGroups.append('rect')
            .attr('class', 'bar')
            .attr('x', d => x(d.month))
            .attr('width', x.bandwidth())
            .attr('y', d => y(d.value))
            .attr('height', d => height - y(d.value))
            .attr('fill', atbColor) // Solid color
            .style('filter', `drop-shadow(0px ${isModal ? 2 : 1}px ${isModal ? 2 : 1}px rgba(0,0,0,0.3))`)
            .style('pointer-events', 'none'); // Make actual bars non-interactive

        // Add value labels on top of bars with appropriate font size
        barGroups.append('text')
            .attr('class', 'label')
            .attr('x', d => x(d.month) + x.bandwidth() / 2)
            .attr('y', d => y(d.value) - (isModal ? 5 : 3))
            .attr('text-anchor', 'middle')
            .style('fill', 'white')
            .style('font-size', isModal ? '10px' : '7px')
            .style('pointer-events', 'none') // Make labels non-interactive
            .text(d => 'Rp' + (d.value / 1000000).toFixed(1) + 'M');
    }
</script>

<!-- APB Chart Implementation -->
<script>
    // Monthly APB chart implementation
    function monthlyApbChart(containerId = 'monthlyApbChart', isModal = false) {
        const chartElement = document.getElementById(containerId);

        if (!chartElement) return;

        // Clear any existing content
        chartElement.innerHTML = '';

        // Set up chart dimensions and margins - Larger for modal
        const margin = {
            top: isModal ? 30 : 15,
            right: isModal ? 30 : 15,
            bottom: isModal ? 50 : 40,
            left: isModal ? 120 : 80
        };

        // Set width and height - Larger for modal
        const width = Math.min(chartElement.clientWidth - margin.left - margin.right, isModal ? 800 : 375);
        const height = (isModal ? 500 : 300) - margin.top - margin.bottom;

        // Create SVG container
        const svg = d3.select('#' + containerId)
            .append('svg')
            .attr('width', width + margin.left + margin.right)
            .attr('height', height + margin.top + margin.bottom)
            .append('g')
            .attr('transform', `translate(${margin.left},${margin.top})`);

        // Create tooltip div
        const tooltip = d3.select('body').append('div')
            .attr('class', 'tooltip')
            .style('opacity', 0);

        // X scale - months
        const x = d3.scaleBand()
            .domain(monthlyApbData.map(d => d.month))
            .range([0, width])
            .padding(isModal ? 0.3 : 0.2); // More padding in modal for better visibility

        // Y scale - APB values
        const y = d3.scaleLinear()
            .domain([0, d3.max(monthlyApbData, d => d.value) * 1.1]) // Add 10% padding
            .range([height, 0]);

        // Add X axis with appropriate font size
        svg.append('g')
            .attr('transform', `translate(0,${height})`)
            .call(d3.axisBottom(x))
            .selectAll('text')
            .style('fill', 'white')
            .style('font-size', isModal ? '12px' : '8px')
            .style('pointer-events', 'none'); // Make axis text non-interactive

        // Add Y axis with formatted labels and appropriate font size
        svg.append('g')
            .call(d3.axisLeft(y)
                .ticks(isModal ? 10 : 5) // More ticks in modal
                .tickFormat(d => 'Rp' + d3.format(',')(d).replace(/,/g, '.'))
            )
            .selectAll('text')
            .style('fill', 'white')
            .style('font-size', isModal ? '10px' : '7px')
            .style('pointer-events', 'none'); // Make axis text non-interactive

        // Add X axis label with appropriate font size
        svg.append('text')
            .attr('class', 'axis-label')
            .attr('text-anchor', 'middle')
            .attr('x', width / 2)
            .attr('y', height + (isModal ? margin.bottom - 10 : margin.bottom))
            .style('fill', 'white')
            .style('font-size', isModal ? '14px' : '10px')
            .style('pointer-events', 'none') // Make label non-interactive
            .text('Bulan');

        // Add Y axis label with appropriate font size
        svg.append('text')
            .attr('class', 'axis-label')
            .attr('text-anchor', 'middle')
            .attr('transform', 'rotate(-90)')
            .attr('x', -height / 2)
            .attr('y', -(isModal ? margin.left - 10 : margin.left - 10))
            .style('fill', 'white')
            .style('font-size', isModal ? '14px' : '10px')
            .style('pointer-events', 'none') // Make label non-interactive
            .text('Nilai APB (Rupiah)');

        // Use single solid color for APB without hover effect
        const apbColor = '#f6f6f6'; // White/gray color for APB

        // Create bar groups
        const barGroups = svg.selectAll('.bar-group')
            .data(monthlyApbData)
            .enter()
            .append('g')
            .attr('class', 'bar-group');

        // Add invisible overlay rectangles FIRST (before visible bars)
        // These extend from the bottom to the top with full height for complete coverage
        barGroups.append('rect')
            .attr('class', 'bar-overlay')
            .attr('x', d => x(d.month))
            .attr('width', x.bandwidth())
            .attr('y', 0) // Start from the top
            .attr('height', height) // Cover the full height
            .attr('fill', 'transparent') // Make it invisible
            .style('cursor', 'pointer')
            .on('mouseover', function(event, d) {
                // Show tooltip
                tooltip.transition()
                    .duration(200)
                    .style('opacity', 0.9);

                tooltip.html(`<strong>Bulan: ${d.month}</strong><br>
                              <strong>APB:</strong> Rp${d.value.toLocaleString('de-DE', {
                                  minimumFractionDigits: 2,
                                  maximumFractionDigits: 2
                              }).replace(/\./g, ',').replace(/,/g, '.')}`)
                    .style('left', (event.pageX + 10) + 'px')
                    .style('top', (event.pageY - 28) + 'px');
            })
            .on('mouseout', function() {
                // Hide tooltip
                tooltip.transition()
                    .duration(500)
                    .style('opacity', 0);
            });

        // Add the visible bars
        barGroups.append('rect')
            .attr('class', 'bar')
            .attr('x', d => x(d.month))
            .attr('width', x.bandwidth())
            .attr('y', d => y(d.value))
            .attr('height', d => height - y(d.value))
            .attr('fill', apbColor) // Solid color
            .style('filter', `drop-shadow(0px ${isModal ? 2 : 1}px ${isModal ? 2 : 1}px rgba(0,0,0,0.3))`)
            .style('pointer-events', 'none'); // Make actual bars non-interactive

        // Add value labels on top of bars with appropriate font size
        barGroups.append('text')
            .attr('class', 'label')
            .attr('x', d => x(d.month) + x.bandwidth() / 2)
            .attr('y', d => y(d.value) - (isModal ? 5 : 3))
            .attr('text-anchor', 'middle')
            .style('fill', 'white')
            .style('font-size', isModal ? '10px' : '7px')
            .style('pointer-events', 'none') // Make labels non-interactive
            .text(d => 'Rp' + (d.value / 1000000).toFixed(1) + 'M');
    }
</script>

<!-- Saldo Chart Implementation -->
<script>
    // Monthly Saldo chart implementation
    function monthlySaldoChart(containerId = 'monthlySaldoChart', isModal = false) {
        const chartElement = document.getElementById(containerId);

        if (!chartElement) return;

        // Clear any existing content
        chartElement.innerHTML = '';

        // Set up chart dimensions and margins - Larger for modal
        const margin = {
            top: isModal ? 30 : 15,
            right: isModal ? 30 : 15,
            bottom: isModal ? 50 : 40,
            left: isModal ? 120 : 80
        };

        // Set width and height - Larger for modal
        const width = Math.min(chartElement.clientWidth - margin.left - margin.right, isModal ? 800 : 375);
        const height = (isModal ? 500 : 300) - margin.top - margin.bottom;

        // Create SVG container
        const svg = d3.select('#' + containerId)
            .append('svg')
            .attr('width', width + margin.left + margin.right)
            .attr('height', height + margin.top + margin.bottom)
            .append('g')
            .attr('transform', `translate(${margin.left},${margin.top})`);

        // Create tooltip div
        const tooltip = d3.select('body').append('div')
            .attr('class', 'tooltip')
            .style('opacity', 0);

        // X scale - months
        const x = d3.scaleBand()
            .domain(monthlySaldoData.map(d => d.month))
            .range([0, width])
            .padding(isModal ? 0.3 : 0.2); // More padding in modal for better visibility

        // Y scale - Saldo values
        const y = d3.scaleLinear()
            .domain([0, d3.max(monthlySaldoData, d => d.value) * 1.1]) // Add 10% padding
            .range([height, 0]);

        // Add X axis with appropriate font size
        svg.append('g')
            .attr('transform', `translate(0,${height})`)
            .call(d3.axisBottom(x))
            .selectAll('text')
            .style('fill', 'white')
            .style('font-size', isModal ? '12px' : '8px')
            .style('pointer-events', 'none'); // Make axis text non-interactive

        // Add Y axis with formatted labels and appropriate font size
        svg.append('g')
            .call(d3.axisLeft(y)
                .ticks(isModal ? 10 : 5) // More ticks in modal
                .tickFormat(d => 'Rp' + d3.format(',')(d).replace(/,/g, '.'))
            )
            .selectAll('text')
            .style('fill', 'white')
            .style('font-size', isModal ? '10px' : '7px')
            .style('pointer-events', 'none'); // Make axis text non-interactive

        // Add X axis label with appropriate font size
        svg.append('text')
            .attr('class', 'axis-label')
            .attr('text-anchor', 'middle')
            .attr('x', width / 2)
            .attr('y', height + (isModal ? margin.bottom - 10 : margin.bottom))
            .style('fill', 'white')
            .style('font-size', isModal ? '14px' : '10px')
            .style('pointer-events', 'none') // Make label non-interactive
            .text('Bulan');

        // Add Y axis label with appropriate font size
        svg.append('text')
            .attr('class', 'axis-label')
            .attr('text-anchor', 'middle')
            .attr('transform', 'rotate(-90)')
            .attr('x', -height / 2)
            .attr('y', -(isModal ? margin.left - 10 : margin.left - 10))
            .style('fill', 'white')
            .style('font-size', isModal ? '14px' : '10px')
            .style('pointer-events', 'none') // Make label non-interactive
            .text('Nilai Saldo (Rupiah)');

        // Use single solid color for Saldo without hover effect
        const saldoColor = '#16ce9a'; // Green color for Saldo

        // Create bar groups
        const barGroups = svg.selectAll('.bar-group')
            .data(monthlySaldoData)
            .enter()
            .append('g')
            .attr('class', 'bar-group');

        // Add invisible overlay rectangles FIRST (before visible bars)
        // These extend from the bottom to the top with full height for complete coverage
        barGroups.append('rect')
            .attr('class', 'bar-overlay')
            .attr('x', d => x(d.month))
            .attr('width', x.bandwidth())
            .attr('y', 0) // Start from the top
            .attr('height', height) // Cover the full height
            .attr('fill', 'transparent') // Make it invisible
            .style('cursor', 'pointer')
            .on('mouseover', function(event, d) {
                // Show tooltip
                tooltip.transition()
                    .duration(200)
                    .style('opacity', 0.9);

                tooltip.html(`<strong>Bulan: ${d.month}</strong><br>
                              <strong>Saldo:</strong> Rp${d.value.toLocaleString('de-DE', {
                                  minimumFractionDigits: 2,
                                  maximumFractionDigits: 2
                              }).replace(/\./g, ',').replace(/,/g, '.')}`)
                    .style('left', (event.pageX + 10) + 'px')
                    .style('top', (event.pageY - 28) + 'px');
            })
            .on('mouseout', function() {
                // Hide tooltip
                tooltip.transition()
                    .duration(500)
                    .style('opacity', 0);
            });

        // Add the visible bars
        barGroups.append('rect')
            .attr('class', 'bar')
            .attr('x', d => x(d.month))
            .attr('width', x.bandwidth())
            .attr('y', d => y(d.value))
            .attr('height', d => height - y(d.value))
            .attr('fill', saldoColor) // Solid color
            .style('filter', `drop-shadow(0px ${isModal ? 2 : 1}px ${isModal ? 2 : 1}px rgba(0,0,0,0.3))`)
            .style('pointer-events', 'none'); // Make actual bars non-interactive

        // Add value labels on top of bars with appropriate font size
        barGroups.append('text')
            .attr('class', 'label')
            .attr('x', d => x(d.month) + x.bandwidth() / 2)
            .attr('y', d => y(d.value) - (isModal ? 5 : 3))
            .attr('text-anchor', 'middle')
            .style('fill', 'white')
            .style('font-size', isModal ? '10px' : '7px')
            .style('pointer-events', 'none') // Make labels non-interactive
            .text(d => 'Rp' + (d.value / 1000000).toFixed(1) + 'M');
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts in regular containers
        monthlyAtbChart();
        monthlyApbChart();
        monthlySaldoChart();

        // Set up modal event handlers to generate charts when modal is opened
        $('#atbChartModal').on('shown.bs.modal', function() {
            monthlyAtbChart('monthlyAtbChartModal', true);
        });

        $('#apbChartModal').on('shown.bs.modal', function() {
            monthlyApbChart('monthlyApbChartModal', true);
        });

        $('#saldoChartModal').on('shown.bs.modal', function() {
            monthlySaldoChart('monthlySaldoChartModal', true);
        });

        // Re-initialize on window resize for responsiveness
        window.addEventListener('resize', function() {
            // Remove any tooltip that might be visible
            clearTooltips();

            // Redraw charts in their current containers
            monthlyAtbChart();
            monthlyApbChart();
            monthlySaldoChart();

            // Check if any modal is visible and redraw its chart if needed
            if ($('#atbChartModal').is(':visible')) {
                monthlyAtbChart('monthlyAtbChartModal', true);
            }
            if ($('#apbChartModal').is(':visible')) {
                monthlyApbChart('monthlyApbChartModal', true);
            }
            if ($('#saldoChartModal').is(':visible')) {
                monthlySaldoChart('monthlySaldoChartModal', true);
            }
        });
    });
</script>
