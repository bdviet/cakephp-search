<?php
//humanizing Column Heads
use Cake\Utility\Inflector;
use Search\Helper\ChartHelper;

//getting column heads
$listingFields = explode(',', $widgetData['info']['columns']);
?>
<div class='dashboard-widget-display_report'>
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><strong> <?= $widgetData['info']['name'] ?></strong></h3>
                    </div> <!-- panel-heading -->

                    <div class="panel-body">
                        <?php if ($widgetData['info']['renderAs'] == 'table') : ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-datatable">
                                <thead>
                                    <tr>
                                    <?php foreach ($listingFields as $field) : ?>
                                        <th><?= Inflector::humanize($field) ?></th>
                                    <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php //pr($renderData); ?>
                                    <?php foreach ($renderData as $k => $row) : ?>
                                    <tr>
                                        <?php foreach ($row as $field) : ?>
                                            <td><?= $field ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div> <!-- table-responsive -->
                        <?php endif; ?>

                        <?php if ($widgetData['info']['renderAs'] == 'barChart') : ?>
                            <?php
                                echo $this->Html->script('https://d3js.org/d3.v4.min.js');
                                echo $this->Html->script('https://d3js.org/d3-axis.v1.min.js');
                                echo $this->Html->script('https://d3js.org/d3-scale.v1.min.js');
                                echo $this->Html->script('https://d3js.org/d3-array.v1.min.js');
                                echo $this->Html->script('https://code.jquery.com/jquery-2.1.4.min.js');
                            ?>
                            <style>
                            .bar {
                              fill: steelblue;
                            }

                            .bar:hover {
                              fill: brown;
                            }

                            .axis--x path {
                              display: none;
                            }
                            </style>
                            <div id="viz_<?php echo $widgetData['slug'];?>"></div>
                            <script type="text/javascript">
                                $(document).ready(function(){
                                    var containerWidth = $('#viz_<?php echo $widgetData['slug'];?>').parent().parent().width();

                                    var data = <?= $this->Chart->getChartData($renderData, ['data' => $widgetData]) ?>;

                                    var margin = {top: 20, right: 20, bottom: 70, left: 40},
                                        width = containerWidth - margin.left - margin.right,
                                        height = 350 - margin.top - margin.bottom;
                                    var w
                                    var x = d3.scaleBand().rangeRound([0, width]).padding(0.1),
                                        y = d3.scaleLinear().rangeRound([height, 0]);

                                    var svg = d3.select("#viz_<?php echo $widgetData['slug'];?>").append("svg")
                                        .attr("width", width + margin.left + margin.right)
                                        .attr("height", height + margin.top + margin.bottom);

                                    var g = svg.append("g")
                                        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

                                    x.domain( data.map( function(d) { return d.x_axis; }) );
                                    // we assume its an integer scale
                                    y.domain( [0, d3.max(data, function(d) { return parseInt(d.y_axis);}) ] );

                                    g.append("g")
                                      .attr("class", "axis axis--x")
                                      .attr("transform", "translate(0," + height + ")")
                                      .call(d3.axisBottom(x));

                                    g.append("g")
                                        .attr('class', "axis axis--y")
                                        .call( d3.axisLeft(y).ticks(10) )

                                     .append("text")
                                        .attr('transform', 'rotate(-90)')
                                        .attr('y', 6)
                                        .attr('dy', '0.71em')
                                        .attr('text-anchor', 'end')
                                        .text('Total');

                                    g.selectAll('.bar')
                                        .data(data)
                                        .enter().append('rect')
                                        .attr('class', 'bar')
                                        .attr('x', function(d) { return x(d.x_axis); })
                                        .attr('y', function(d) { return y(d.y_axis); })
                                        .attr('width', x.bandwidth())
                                        .attr('height', function(d){ return height - y(d.y_axis); });

                                });
                            </script>
                        <?php endif; ?>
                    </div> <!-- panel-body -->

                </div> <!-- panel-default -->

            </div> <!-- col-xs-12 -->
        </div> <!-- row -->
</div> <!-- dashboard-widget-display_report -->
