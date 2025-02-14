<script>
    $(document).ready(function() {

        // Initialize select2
        $('#projectSelect').select2({
            placeholder: 'Pilih Proyek',
            width: '100%'
        });

        // Chart data from PHP
        const currentMonthData = @json($chartDataCurrent);
        const totalData = @json($chartDataTotal);

        // Format number to billions with rounding up and specific format
        const formatBillion = (number) => {
            // Convert to billions and round to 2 decimal places
            const billions = (number / 1000000000);
            // Round up to 2 decimal places
            const rounded = Math.ceil(billions * 100) / 100;
            // Format with dot for thousands and comma for decimals
            return rounded.toLocaleString('de-DE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        };

        // Format number to display in full Rupiah with specific format
        const formatRupiah = (number) => {
            return 'Rp ' + number.toLocaleString('de-DE').split(',')[0];
        };

        function createVerticalChart(elementId, data) {
            // Clear existing chart
            d3.select(`#${elementId}`).html('');

            // Create tooltip div
            const tooltip = d3.select("body").append("div")
                .attr("class", "tooltip")
                .style("opacity", 0);

            // Chart dimensions with increased top margin
            const margin = {
                top: 80, // Increased from 60 to 80
                right: 50,
                bottom: 60,
                left: 100
            };
            const width = 500 - margin.left - margin.right;
            const height = 400 - margin.top - margin.bottom;

            // Create SVG container
            const svg = d3.select(`#${elementId}`)
                .append('svg')
                .attr('width', width + margin.left + margin.right)
                .attr('height', height + margin.top + margin.bottom)
                .append('g')
                .attr('transform', `translate(${margin.left},${margin.top})`);

            // Prepare data
            const categories = Object.keys(data);
            const series = ['atb', 'apb', 'saldo'];
            const colors = {
                atb: '#fe6d73', // Merah muda
                apb: '#f6f6f6', // Putih
                saldo: '#16ce9a' // Hijau
            };

            // Add legend at the top with more space
            const legendLabels = {
                'atb': 'ATB',
                'apb': 'APB',
                'saldo': 'Saldo'
            };

            const legend = svg.append('g')
                .attr('transform', `translate(0, ${-margin.top/1.2})`); // Changed from /2 to /1.5

            const legendItems = legend.selectAll('g')
                .data(Object.keys(legendLabels))
                .enter()
                .append('g')
                .attr('transform', (d, i) => `translate(${i * 150}, 0)`);

            legendItems.append('rect')
                .attr('width', 18)
                .attr('height', 18)
                .attr('fill', d => colors[d]);

            legendItems.append('text')
                .attr('x', 24)
                .attr('y', 9)
                .attr('dy', '.35em')
                .style('fill', 'white')
                .text(d => legendLabels[d]);

            // Scales
            const x0 = d3.scaleBand()
                .domain(categories)
                .rangeRound([0, width])
                .paddingOuter(0.1)
                .paddingInner(0.1); // Reduce space between category groups

            const x1 = d3.scaleBand()
                .domain(series)
                .rangeRound([0, x0.bandwidth()])
                .padding(0.05); // Reduce padding between bars in a group

            const y = d3.scaleLinear()
                .domain([0, d3.max(categories, category =>
                    d3.max(series, series => data[category][series])
                ) * 1.1]) // Multiply the max value by 1.1 to add 10% extra space
                .nice()
                .range([height, 0]);

            // Add bars with values and tooltips
            const categoryGroups = svg.selectAll('g.category')
                .data(categories)
                .enter().append('g')
                .attr('class', 'category')
                .attr('transform', d => `translate(${x0(d)},0)`);

            categoryGroups.selectAll('g.bar-group')
                .data(d => series.map(key => ({
                    key,
                    value: data[d][key],
                    color: colors[key] // Explicitly pass color
                })))
                .enter().append('g')
                .attr('class', 'bar-group')
                .each(function(d) {
                    const group = d3.select(this);
                    const xPos = x1(d.key);
                    const barWidth = x1.bandwidth();

                    // Add the actual bar
                    group.append('rect')
                        .attr('x', xPos)
                        .attr('y', d => y(d.value))
                        .attr('width', barWidth)
                        .attr('height', d => height - y(d.value))
                        .attr('fill', d => d.color) // Use color directly
                        .style('cursor', 'default'); // Remove pointer cursor from bar

                    // Add wider transparent overlay for easier hovering
                    const overlayWidth = Math.max(barWidth * 1.5, 10);
                    const barHeight = height - y(d.value);
                    const overlayHeight = Math.max(100, barHeight); // Minimum 100px height
                    const overlayY = y(d.value) - (overlayHeight - barHeight) / 2; // Center overlay around bar

                    group.append('rect')
                        .attr('class', 'bar-overlay')
                        .attr('x', xPos - (overlayWidth - barWidth) / 2)
                        .attr('y', overlayY)
                        .attr('width', overlayWidth)
                        .attr('height', overlayHeight)
                        .style('cursor', 'pointer') // Ensure pointer only on overlay
                        .on('mouseover', function(event, d) {
                            // Highlight the bar
                            group.select('.bar')
                                .style('opacity', 0.8);

                            // Show tooltip with full number format
                            tooltip.transition()
                                .duration(200)
                                .style("opacity", .9);

                            tooltip.html(`${legendLabels[d.key]}<br>${formatRupiah(d.value)}`)
                                .style("left", (event.pageX + 10) + "px")
                                .style("top", (event.pageY - 28) + "px");
                        })
                        .on('mouseout', function() {
                            // Remove highlight
                            group.select('.bar')
                                .style('opacity', 1);

                            // Hide tooltip
                            tooltip.transition()
                                .duration(500)
                                .style("opacity", 0);
                        });

                    // Add value label with tooltip functionality
                    if (d.value > 0) {
                        group.append('text')
                            .attr('class', 'value-label')
                            .attr('x', xPos + barWidth / 2)
                            .attr('y', d => y(d.value) - 5)
                            .attr('text-anchor', 'middle')
                            .style('fill', 'white')
                            .style('font-size', '10px')
                            .style('cursor', 'pointer') // Add pointer cursor
                            .text(d => formatBillion(d.value) + 'M')
                            .on('mouseover', function(event, d) {
                                // Highlight the bar
                                group.select('.bar')
                                    .style('opacity', 0.8);

                                // Show tooltip
                                tooltip.transition()
                                    .duration(200)
                                    .style("opacity", .9);

                                tooltip.html(`${legendLabels[d.key]}<br>${formatRupiah(d.value)}`)
                                    .style("left", (event.pageX + 10) + "px")
                                    .style("top", (event.pageY - 28) + "px");
                            })
                            .on('mouseout', function() {
                                // Remove highlight
                                group.select('.bar')
                                    .style('opacity', 1);

                                // Hide tooltip
                                tooltip.transition()
                                    .duration(500)
                                    .style("opacity", 0);
                            })
                            .on('mousemove', function(event) {
                                // Update tooltip position while moving
                                tooltip.style("left", (event.pageX + 10) + "px")
                                    .style("top", (event.pageY - 28) + "px");
                            });
                    }
                });

            // Add axes with white color
            svg.append('g')
                .attr('transform', `translate(0,${height})`)
                .call(d3.axisBottom(x0))
                .style('color', 'white')
                .selectAll('text')
                .style('fill', 'white')
                .attr('y', 20) // Move x-axis labels down by 20px
                .style('text-anchor', 'middle');

            svg.append('g')
                .call(d3.axisLeft(y)
                    .tickFormat(d => formatBillion(d) + ' M'))
                .style('color', 'white')
                .selectAll('text')
                .style('fill', 'white');

            // Y-axis label - moved more to the left
            svg.append('text')
                .attr('transform', 'rotate(-90)')
                .attr('y', 0 - margin.left + 15) // Reduced by 20px to move left
                .attr('x', 0 - (height / 2))
                .attr('dy', '12px')
                .style('text-anchor', 'middle')
                .style('fill', 'white')
                .text('Rupiah (dalam Miliar)');
        }

        // Create both charts
        createVerticalChart('currentMonthVerticalChart', currentMonthData);
        createVerticalChart('totalVerticalChart', totalData);
    });
</script>
