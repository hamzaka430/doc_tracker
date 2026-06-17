<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Remaining Documents - Double Layout</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        
        .page-container {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .column {
            display: table-cell;
            width: 50%;
            padding: 20px 15px 20px 30px;
            vertical-align: top;
            border-right: 2px dashed #999;
        }
        
        .column:last-child {
            border-right: none;
        }
        
        .date {
            font-weight: bold;
            margin-bottom: 8px;
            color: #0066cc;
            font-size: 12px;
        }
        
        .document-list {
            margin-top: 4px;
        }
        
        .doc-item {
            margin-bottom: 6px;
            page-break-inside: avoid;
        }
        
        .doc-header {
            font-weight: bold;
            margin-bottom: 2px;
            font-size: 11px;
        }
        
        .batch-line {
            margin-left: 4px;
            margin-bottom: 1px;
            font-size: 10px;
            line-height: 1.5;
        }
        
        .bullet {
            display: inline-block;
            width: 4px;
            height: 4px;
            background-color: #000;
            border-radius: 50%;
            margin-right: 5px;
            position: relative;
            top: -1px;
        }
        
        .divider {
            border-top: 1px dashed #ccc;
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Left Column -->
        <div class="column">
            <div class="date">Date: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</div>
            
            <div class="document-list">
                @if($groupedProducts->count() > 0)
                    @foreach($groupedProducts as $index => $product)
                        <div class="doc-item">
                            <div class="doc-header">{{ $product['name'] }}</div>
                            @foreach($product['batches'] as $batch)
                                <div class="batch-line">
                                    <span class="bullet"></span>{{ $batch['batch_no'] }} --------- {{ $batch['stage'] }}
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; margin-top: 20px; font-size: 10px;">No remaining documents</div>
                @endif
            </div>
        </div>
        
        <!-- Right Column (Duplicate of Left) -->
        <div class="column">
            <div class="date">Date: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</div>
            
            <div class="document-list">
                @if($groupedProducts->count() > 0)
                    @foreach($groupedProducts as $index => $product)
                        <div class="doc-item">
                            <div class="doc-header">{{ $product['name'] }}</div>
                            @foreach($product['batches'] as $batch)
                                <div class="batch-line">
                                    <span class="bullet"></span>{{ $batch['batch_no'] }} --------- {{ $batch['stage'] }}
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; margin-top: 20px; font-size: 10px;">No remaining documents</div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
