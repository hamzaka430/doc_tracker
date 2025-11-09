<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Remaining Documents</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 20px;
            line-height: 1.4;
        }
        
        .date {
            font-weight: bold;
            margin-bottom: 10px;
            color: #0066cc;
            font-size: 13px;
        }
        
        .document-list {
            margin-top: 5px;
        }
        
        .doc-item {
            margin-bottom: 8px;
            page-break-inside: avoid;
        }
        
        .doc-header {
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 12px;
        }
        
        .batch-line {
            margin-left: 5px;
            margin-bottom: 2px;
            font-size: 11px;
            line-height: 1.6;
        }
        
        .bullet {
            display: inline-block;
            width: 5px;
            height: 5px;
            background-color: #000;
            border-radius: 50%;
            margin-right: 6px;
            position: relative;
            top: -1px;
        }
    </style>
</head>
<body>
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
            <div style="text-align: center; margin-top: 30px;">No remaining documents found</div>
        @endif
    </div>
</body>
</html>
