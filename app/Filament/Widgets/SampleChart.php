<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class SampleChart extends ChartWidget
{
    protected static ?string $heading = 'Sample Chart';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created',
                    'data' => [5, 10],
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => ['A','B'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
