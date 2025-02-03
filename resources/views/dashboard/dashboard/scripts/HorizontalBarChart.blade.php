<script>
    document.addEventListener("DOMContentLoaded", function() {
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

        function createHorizontalChart(elementId, data) {
            // Clear existing chart
            d3.select(`#${elementId}`).html('');

            // Convert data keys to atb, apb, saldo
            const processedData = {};
            Object.keys(data).forEach(project => {
                processedData[project] = {
                    atb: data[project].penerimaan || 0,
                    apb: data[project].pengeluaran || 0,
                    saldo: data[project].saldo || 0
                };
            });

            // Get container dimensions
            const container = document.getElementById(elementId);
            const containerWidth = container.clientWidth || 800;
            const projectCount = Object.keys(processedData).length;
            const containerHeight = Math.max(400, projectCount * 80);

            // Chart dimensions with adjusted margins
            const margin = {
                top: 50,
                right: 100, // Increased for labels
                bottom: 50,
                left: 300 // Increased from 200 to 300 for longer project names
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

            // Get projects and prepare data
            const projects = Object.keys(processedData);
            const series = ['atb', 'apb', 'saldo'];
            const colors = {
                atb: '#fe6d73', // Red
                apb: '#f6f6f6', // White
                saldo: '#16ce9a' // Green
            };

            // Scales
            const y0 = d3.scaleBand()
                .domain(projects)
                .rangeRound([0, height])
                .paddingInner(0.2)
                .paddingOuter(0.3);

            const y1 = d3.scaleBand()
                .domain(series)
                .rangeRound([0, y0.bandwidth()])
                .padding(0.1);

            const x = d3.scaleLinear()
                .domain([0, d3.max(projects, project =>
                    d3.max(series, series => processedData[project][series])
                ) * 1.1])
                .nice()
                .range([0, width]);

            // Calculate total width of legend items
            const legendData = ['ATB', 'APB', 'Saldo'];
            const legendItemWidth = 150; // Width of each legend item
            const legendTotalWidth = legendData.length * legendItemWidth;

            // Add legend with centered positioning
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

            // Create project groups
            const projectGroups = svg.selectAll('g.project')
                .data(projects)
                .enter()
                .append('g')
                .attr('class', 'project')
                .attr('transform', d => `translate(0,${y0(d)})`);

            // Create tooltip div with updated positioning
            const tooltip = d3.select("body").append("div")
                .attr("class", "tooltip")
                .style("opacity", 0)
                .style("pointer-events", "none")
                .style("position", "absolute"); // Changed from fixed to absolute

            // Add bars
            projectGroups.selectAll('rect.bar')
                .data(d => series.map(key => ({
                    key,
                    value: processedData[d][key],
                    project: d
                })))
                .enter()
                .append('rect')
                .attr('class', 'bar')
                .attr('x', 0)
                .attr('y', d => y1(d.key))
                .attr('width', d => x(d.value || 0)) // Handle null/undefined values
                .attr('height', y1.bandwidth())
                .attr('fill', d => colors[d.key])
                .style('cursor', 'default');

            // Add wider transparent overlay for easier hovering
            projectGroups.selectAll('rect.bar-overlay')
                .data(d => series.map(key => ({
                    key,
                    value: processedData[d][key],
                    project: d
                })))
                .enter()
                .append('rect')
                .attr('class', 'bar-overlay')
                .attr('x', 0)
                .attr('y', d => y1(d.key) - y1.bandwidth() / 4)
                .attr('width', d => x(d.value || 0))
                .attr('height', y1.bandwidth() * 1.5)
                .style('fill', 'transparent')
                .style('cursor', 'pointer')
                .on('mouseover', function(event, d) {
                    // Highlight the bar
                    d3.select(this.parentNode)
                        .select('.bar')
                        .style('opacity', 0.8);

                    // Get mouse position relative to viewport
                    const mouseX = event.pageX;
                    const mouseY = event.pageY;

                    // Show tooltip with adjusted position
                    tooltip.transition()
                        .duration(200)
                        .style("opacity", .9);

                    tooltip.html(`
                        <strong>${d.project}</strong><br>
                        ${d.key.toUpperCase()}: ${formatRupiah(d.value || 0)}
                    `)
                        .style("left", (mouseX + 10) + "px")
                        .style("top", (mouseY - 40) + "px"); // Adjusted Y position
                })
                .on('mouseout', function() {
                    // Remove highlight
                    d3.select(this.parentNode)
                        .select('.bar')
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

            // Add value labels
            projectGroups.selectAll('text.value-label')
                .data(d => series.map(key => ({
                    key,
                    value: processedData[d][key],
                    project: d
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
                    // Show tooltip
                    tooltip.transition()
                        .duration(200)
                        .style("opacity", .9);

                    tooltip.html(`
                        <strong>${d.project}</strong><br>
                        ${d.key.toUpperCase()}: ${formatRupiah(d.value || 0)}
                    `)
                        .style("left", (event.pageX + 10) + "px")
                        .style("top", (event.pageY - 40) + "px");
                })
                .on('mouseout', function() {
                    // Hide tooltip
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
                .style('color', 'white')
                .selectAll('text')
                .style('fill', 'white');

            // Modify y-axis to handle long text
            svg.append('g')
                .attr('class', 'y-axis')
                .call(d3.axisLeft(y0))
                .style('color', 'white')
                .selectAll('text')
                .style('fill', 'white')
                .style('text-anchor', 'end')
                .attr('dx', '-0.5em')
                .attr('dy', '0.32em')
                .each(function(d) {
                    const text = d3.select(this);
                    const words = d.split(' ');
                    text.text(''); // Clear existing text

                    // Create tspan for each word
                    let tspan = text.append('tspan')
                        .attr('x', -10)
                        .attr('y', 0);

                    let line = '';
                    let lineNumber = 0;
                    const lineHeight = 1.1; // ems
                    const maxWidth = margin.left - 100; // Maximum width for text

                    words.forEach(function(word, i) {
                        const testLine = line + (line ? ' ' : '') + word;
                        tspan.text(testLine);

                        if (tspan.node().getComputedTextLength() > maxWidth && i > 0) {
                            // Start new line
                            tspan = text.append('tspan')
                                .attr('x', -10)
                                .attr('dy', `${lineHeight}em`)
                                .text(word);
                            line = word;
                            lineNumber++;
                        } else {
                            line = testLine;
                        }
                    });

                    // Adjust vertical position for multi-line text
                    if (lineNumber > 0) {
                        const yOffset = -lineNumber * lineHeight / 2;
                        text.selectAll('tspan')
                            .attr('dy', (d, i) => `${i === 0 ? yOffset : lineHeight}em`);
                    }
                });

            // X-axis label
            svg.append('text')
                .attr('x', width / 2)
                .attr('y', height + 40)
                .attr('text-anchor', 'middle')
                .style('fill', 'white')
                .text('Rupiah (dalam Miliar)');

            // Initial render
            renderChart();

            // Add window resize handler
            function renderChart() {
                const newWidth = container.clientWidth || 800;
                svg.attr('width', newWidth);
                // Update scales and redraw elements as needed
                x.range([0, newWidth - margin.left - margin.right]);
                // Update other elements...
            }

            window.addEventListener('resize', debounce(renderChart, 250));
        }

        // Create both horizontal charts with a small delay to ensure container sizes are calculated
        setTimeout(() => {
            createHorizontalChart('currentMonthHorizontalChart', @json($horizontalChartCurrent));
            createHorizontalChart('totalHorizontalChart', @json($horizontalChartTotal));
        }, 100);

        // Add resize handler
        window.addEventListener('resize', debounce(() => {
            createHorizontalChart('currentMonthHorizontalChart', @json($horizontalChartCurrent));
            createHorizontalChart('totalHorizontalChart', @json($horizontalChartTotal));
        }, 250));

        // Debounce function to prevent too many resize events
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
    });
</script>
