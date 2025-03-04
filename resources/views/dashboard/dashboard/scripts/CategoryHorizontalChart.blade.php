<script>
    $(document).ready(function() {
        // Format number to billions with rounding up and specific format
        const formatBillion = (number) => {
            const billions = (number / 1000000000);
            const rounded = Math.ceil(billions * 100) / 100;
            return rounded.toLocaleString('de-DE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        };

        // Format number to display in full Rupiah with specific format
        const formatRupiah = (number) => {
            return 'Rp ' + number.toLocaleString('de-DE').split(',')[0];
        };

        function createCategoryChart(elementId, data, seriesToShow = ['ATB', 'APB', 'Saldo']) {
            // Clear existing chart
            d3.select(`#${elementId}`).html('');

            // Get container dimensions
            const container = document.getElementById(elementId);
            const containerWidth = container.clientWidth || 800;
            const categoryCount = Object.keys(data).length;
            const containerHeight = Math.max(400, categoryCount * 80);

            // Chart dimensions with adjusted margins
            const margin = {
                top: 50,
                right: 100,
                bottom: 50,
                left: 300
            };
            const width = containerWidth - margin.left - margin.right;
            const height = containerHeight - margin.top - margin.bottom;

            // Create SVG container
            const svg = d3.select(`#${elementId}`)
                .append('svg')
                .attr('width', containerWidth)
                .attr('height', containerHeight)
                .append('g')
                .attr('transform', `translate(${margin.left},${margin.top})`);

            // Get categories and prepare data
            const categories = Object.keys(data);
            const series = seriesToShow; // Use the provided series
            const colors = {
                ATB: '#fe6d73', // Red
                APB: '#f6f6f6', // White
                Saldo: '#16ce9a' // Green
            };

            // Scales
            const y0 = d3.scaleBand()
                .domain(categories)
                .rangeRound([0, height])
                .paddingInner(0.2)
                .paddingOuter(0.3);

            const y1 = d3.scaleBand()
                .domain(series)
                .rangeRound([0, y0.bandwidth()])
                .padding(0.1);

            const x = d3.scaleLinear()
                .domain([0, d3.max(categories, category =>
                    d3.max(series, series => data[category][series])
                ) * 1.1])
                .nice()
                .range([0, width]);

            // Add legend
            const legendData = series;
            const legendItemWidth = 150;
            const legendTotalWidth = legendData.length * legendItemWidth;

            const legend = svg.append('g')
                .attr('transform', `translate(${(width - legendTotalWidth) / 2}, ${-margin.top/1.2})`);

            const legendItems = legend.selectAll('g')
                .data(legendData)
                .enter()
                .append('g')
                .attr('transform', (d, i) => `translate(${i * legendItemWidth}, 0)`);

            legendItems.append('rect')
                .attr('width', 18)
                .attr('height', 18)
                .attr('fill', (d, i) => colors[series[i]]);

            legendItems.append('text')
                .attr('x', 24)
                .attr('y', 9)
                .attr('dy', '.35em')
                .style('fill', 'white')
                .text(d => d);

            // Create category groups
            const categoryGroups = svg.selectAll('g.category')
                .data(categories)
                .enter()
                .append('g')
                .attr('class', 'category')
                .attr('transform', d => `translate(0,${y0(d)})`);

            // Create tooltip
            const tooltip = d3.select("body").append("div")
                .attr("class", "tooltip")
                .style("opacity", 0)
                .style("pointer-events", "none")
                .style("position", "absolute");

            // Add bars with the same styling and interactions as HorizontalBarChart
            // Add bars and tooltips
            const addBarsAndTooltips = (selection) => {
                selection.selectAll('rect.bar')
                    .data(d => series.map(key => ({
                        key,
                        value: data[d][key],
                        category: d
                    })))
                    .enter()
                    .append('rect')
                    .attr('class', 'bar')
                    .attr('x', 0)
                    .attr('y', d => y1(d.key))
                    .attr('width', d => x(d.value || 0))
                    .attr('height', y1.bandwidth())
                    .attr('fill', d => colors[d.key])
                    .style('cursor', 'default');

                // Add value labels
                selection.selectAll('text.value-label')
                    .data(d => series.map(key => ({
                        key,
                        value: data[d][key],
                        category: d
                    })))
                    .enter()
                    .append('text')
                    .attr('class', 'value-label')
                    .attr('x', d => x(d.value || 0) + 5)
                    .attr('y', d => y1(d.key) + y1.bandwidth() / 2)
                    .attr('dy', '.35em')
                    .style('fill', 'white')
                    .style('font-size', '10px')
                    .text(d => d.value ? formatBillion(d.value) + ' M' : '0 M');
            };

            // Apply bars and tooltips
            addBarsAndTooltips(categoryGroups);

            // Add wider transparent overlay for easier hovering
            categoryGroups.selectAll('rect.bar-overlay')
                .data(d => series.map(key => ({
                    key,
                    value: data[d][key],
                    category: d
                })))
                .enter()
                .append('rect')
                .attr('class', 'bar-overlay')
                .attr('x', 0)
                .attr('y', d => y1(d.key) - y1.bandwidth() / 4)
                .attr('width', d => Math.max(x(d.value || 0), 20)) // Minimum width of 20px for interaction
                .attr('height', y1.bandwidth() * 1.5)
                .style('fill', 'transparent')
                .style('cursor', 'pointer')
                .on('mouseover', function(event, d) {
                    // Highlight the bar
                    d3.select(this.parentNode)
                        .select('.bar')
                        .style('opacity', 0.8);

                    d3.select(this.parentNode)
                        .select('.value-label')
                        .style('opacity', 0.8);

                    // Get mouse position relative to viewport
                    const mouseX = event.pageX;
                    const mouseY = event.pageY;

                    // Show tooltip with adjusted position
                    tooltip.transition()
                        .duration(200)
                        .style("opacity", .9);

                    tooltip.html(`
                        <strong>${d.category}</strong><br>
                        ${d.key}: ${formatRupiah(d.value || 0)}
                    `)
                        .style("left", (mouseX + 10) + "px")
                        .style("top", (mouseY - 40) + "px");
                })
                .on('mouseout', function() {
                    // Remove highlight
                    d3.select(this.parentNode)
                        .select('.bar')
                        .style('opacity', 1);

                    d3.select(this.parentNode)
                        .select('.value-label')
                        .style('opacity', 1);

                    // Hide tooltip
                    tooltip.transition()
                        .duration(500)
                        .style("opacity", 0);
                })
                .on('mousemove', function(event) {
                    // Update tooltip position with adjusted coordinates
                    tooltip
                        .style("left", (event.pageX + 10) + "px")
                        .style("top", (event.pageY - 40) + "px");
                });

            // Remove old value labels and tooltip handlers since we're using the overlay now
            categoryGroups.selectAll('text.value-label')
                .data(d => series.map(key => ({
                    key,
                    value: data[d][key],
                    category: d
                })))
                .enter()
                .append('text')
                .attr('class', 'value-label')
                .attr('x', d => x(d.value || 0) + 5)
                .attr('y', d => y1(d.key) + y1.bandwidth() / 2)
                .attr('dy', '.35em')
                .style('fill', 'white')
                .style('font-size', '10px')
                .style('cursor', 'pointer')
                .text(d => d.value ? formatBillion(d.value) + ' M' : '0 M')
                .on('mouseover', function(event, d) {
                    // Highlight the bar
                    d3.select(this.parentNode)
                        .select('.bar')
                        .style('opacity', 0.8);

                    tooltip.transition()
                        .duration(200)
                        .style("opacity", .9);

                    tooltip.html(`
                        <strong>${d.category}</strong><br>
                        ${d.key}: ${formatRupiah(d.value || 0)}
                    `)
                        .style("left", (event.pageX + 10) + "px")
                        .style("top", (event.pageY - 40) + "px");
                })
                .on('mouseout', function() {
                    // Remove highlight
                    d3.select(this.parentNode)
                        .select('.bar')
                        .style('opacity', 1);

                    tooltip.transition()
                        .duration(500)
                        .style("opacity", 0);
                })
                .on('mousemove', function(event) {
                    tooltip
                        .style("left", (event.pageX + 10) + "px")
                        .style("top", (event.pageY - 40) + "px");
                });

            // Add axes
            svg.append('g')
                .attr('class', 'x-axis')
                .attr('transform', `translate(0,${height})`)
                .call(d3.axisBottom(x)
                    .tickFormat(d => formatBillion(d) + ' M'))
                .selectAll('text')
                .style('fill', 'white');

            // Add Y axis with word wrapping
            const yAxis = svg.append('g')
                .attr('class', 'y-axis')
                .call(d3.axisLeft(y0))
                .selectAll('text')
                .style('fill', 'white')
                .call(wrap, margin.left - 20);

            // Initial render
            renderChart();

            // Add window resize handler
            function renderChart() {
                const newWidth = container.clientWidth || 800;
                svg.attr('width', newWidth);
                x.range([0, newWidth - margin.left - margin.right]);
            }

            window.addEventListener('resize', debounce(renderChart, 250));
        }

        // Helper function for word wrapping
        function wrap(text, width) {
            text.each(function() {
                const text = d3.select(this);
                const words = text.text().split(/\s+/);
                const lineHeight = 1.1;
                const y = text.attr("y");
                const dy = parseFloat(text.attr("dy"));

                text.text(null);

                let line = [];
                let lineNumber = 0;
                let tspan = text.append("tspan").attr("x", -10).attr("y", y).attr("dy", dy + "em");

                words.forEach(word => {
                    line.push(word);
                    tspan.text(line.join(" "));

                    if (tspan.node().getComputedTextLength() > width) {
                        line.pop();
                        tspan.text(line.join(" "));
                        line = [word];
                        tspan = text.append("tspan")
                            .attr("x", -10)
                            .attr("y", y)
                            .attr("dy", ++lineNumber * lineHeight + dy + "em")
                            .text(word);
                    }
                });
            });
        }

        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Create both category charts with different series configurations
        setTimeout(() => {
            createCategoryChart('categoryChartCurrent', @json($categoryData['current']), ['ATB', 'APB']);
            createCategoryChart('categoryChartTotal', @json($categoryData['total']));
        }, 100);

        // Add resize handler
        window.addEventListener('resize', debounce(() => {
            createCategoryChart('categoryChartCurrent', @json($categoryData['current']), ['ATB', 'APB']);
            createCategoryChart('categoryChartTotal', @json($categoryData['total']));
        }, 250));
    });
</script>
