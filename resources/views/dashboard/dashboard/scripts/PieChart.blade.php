<script>
    $(document).ready(function() {
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

        function createPieChart(elementId, data, includeSaldo = true) {
            // Clear existing chart
            d3.select(`#${elementId}`).html('');

            // Setup dimensions with adjusted spacing
            const width = 400; // Decreased from 500
            const height = 400;
            const radius = Math.min(width - 120, height) / 2; // Adjusted space for labels

            // Create color scheme
            const colors = {
                atb: '#fe6d73', // Red
                apb: '#f6f6f6', // White
                saldo: '#16ce9a' // Green
            };

            // Create SVG container with adjusted position
            const svg = d3.select(`#${elementId}`)
                .append('svg')
                .attr('width', width)
                .attr('height', height)
                .append('g')
                .attr('transform', `translate(${width/2},${height/2})`); // Centered position

            // Process data
            const pieData = [{
                    name: 'ATB',
                    value: data.atb || 0
                },
                {
                    name: 'APB',
                    value: data.apb || 0
                }
            ];

            // Only include Saldo if specified
            if (includeSaldo) {
                pieData.push({
                    name: 'Saldo',
                    value: data.saldo || 0
                });
            }

            // Ensure at least one slice has a non-zero value
            const hasNonZeroValue = pieData.some(d => d.value > 0);
            if (!hasNonZeroValue) {
                pieData.forEach(d => d.value = 1); // Assign a small value to each slice
            }

            // Create pie layout
            const pie = d3.pie()
                .value(d => d.value)
                .sort(null);

            // Create arc generators
            const arc = d3.arc()
                .innerRadius(0)
                .outerRadius(radius - 20);

            const outerArc = d3.arc()
                .innerRadius(radius * 0.9)
                .outerRadius(radius * 0.9);

            // Create tooltip
            const tooltip = d3.select("body").append("div")
                .attr("class", "tooltip")
                .style("opacity", 0)
                .style("position", "absolute");

            // Add slices with same hover effects
            const slices = svg.selectAll('path')
                .data(pie(pieData))
                .enter()
                .append('path')
                .attr('d', arc)
                .attr('fill', d => colors[d.data.name.toLowerCase()])
                .attr('stroke', '#353a50')
                .style('stroke-width', '2px')
                .on('mouseover', function(event, d) {
                    // Highlight slice
                    d3.select(this)
                        .transition()
                        .duration(200)
                        .attr('transform', function(d) {
                            const dist = 10;
                            const a = (d.startAngle + d.endAngle) / 2 * 180 / Math.PI;
                            const x = dist * Math.sin(a * Math.PI / 180);
                            const y = -dist * Math.cos(a * Math.PI / 180);
                            return `translate(${x},${y})`;
                        });

                    // Show tooltip
                    tooltip.transition()
                        .duration(200)
                        .style("opacity", .9);

                    const percentage = ((d.value / d3.sum(pieData, d => d.value)) * 100).toFixed(1);

                    // Check if the value is 1 (which is the placeholder value)
                    const valueDisplay = d.value === 1 && !hasNonZeroValue ? "Rp0" : formatRupiah(d.value);

                    tooltip.html(`
                        <strong>${d.data.name}</strong><br>
                        ${valueDisplay}<br>
                        ${percentage}%
                    `)
                        .style("left", (event.pageX + 10) + "px")
                        .style("top", (event.pageY - 40) + "px");
                })
                .on('mouseout', function() {
                    // Remove highlight
                    d3.select(this)
                        .transition()
                        .duration(200)
                        .attr('transform', 'translate(0,0)');

                    // Hide tooltip
                    tooltip.transition()
                        .duration(500)
                        .style("opacity", 0);
                });

            // Add outer labels with lines
            const labelGroup = svg.selectAll('.label-group')
                .data(pie(pieData))
                .enter()
                .append('g')
                .attr('class', 'label-group');

            // Add lines connecting slices to labels
            function midAngle(d) {
                return d.startAngle + (d.endAngle - d.startAngle) / 2;
            }

            // Modify the line connector positions
            labelGroup.append('polyline')
                .attr('points', function(d) {
                    const pos = outerArc.centroid(d);
                    pos[0] = radius * (midAngle(d) < Math.PI ? 0.9 : -0.9); // Reduced distance
                    return [arc.centroid(d), outerArc.centroid(d), pos];
                })
                .style('fill', 'none')
                .style('stroke', 'white')
                .style('stroke-width', '1px')
                .style('opacity', d => (d.value === 0 ? 0 : 0.75));

            // Adjust label positions to be closer
            labelGroup.append('text')
                .attr('transform', function(d) {
                    const pos = outerArc.centroid(d);
                    pos[0] = radius * (midAngle(d) < Math.PI ? 1.0 : -1.0); // Reduced distance
                    return `translate(${pos})`;
                })
                .style('text-anchor', d => midAngle(d) < Math.PI ? 'start' : 'end')
                .style('fill', 'white')
                .style('font-size', '11px') // Slightly smaller font
                .each(function(d) {
                    const percentage = ((d.value / d3.sum(pieData, d => d.value)) * 100).toFixed(1);
                    const billions = formatBillion(d.value);
                    const text = d3.select(this);

                    // Adjust line spacing for more compact layout
                    text.append('tspan')
                        .attr('x', 0)
                        .attr('dy', '-1em')
                        .text(d.data.name);

                    text.append('tspan')
                        .attr('x', 0)
                        .attr('dy', '1.1em')
                        .text(`${percentage}%`);

                    text.append('tspan')
                        .attr('x', 0)
                        .attr('dy', '1.1em')
                        .text(`${billions}M`);
                })
                .style('opacity', d => (d.value === 0 ? 0 : 1));
        }

        // Calculate total values for each category
        function calculateTotals(data) {
            const totals = {
                atb: 0,
                apb: 0,
                saldo: 0
            };
            Object.values(data).forEach(item => {
                totals.atb += item.penerimaan || 0;
                totals.apb += item.pengeluaran || 0;
                totals.saldo += item.saldo || 0;
            });
            return totals;
        }

        // Create both pie charts
        const currentTotals = calculateTotals(@json($horizontalChartCurrent));
        const totalTotals = calculateTotals(@json($horizontalChartTotal));

        setTimeout(() => {
            createPieChart('pieChartCurrent', currentTotals, false); // Don't include Saldo
            createPieChart('pieChartTotal', totalTotals, true); // Include Saldo
        }, 100);

        // Add resize handler
        window.addEventListener('resize', debounce(() => {
            createPieChart('pieChartCurrent', currentTotals, false); // Don't include Saldo
            createPieChart('pieChartTotal', totalTotals, true); // Include Saldo
        }, 250));
    });
</script>
